<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/api/conv", name="api_conv_")
*/
class ConversationController extends AbstractController
{
    /**
    * @Route("/user/list", name="user_list")
    */
    public function ApiConversationByUser()
    {
        // récupère les conversations de l'utilisateur courant et les renvoie en Json
        $conversation = $this->getUser()->getConversations();
        return $this->json($conversation, 200, [], ['groups' => ['userConversation']]);
    }

    /**
    * @Route("/{id}/show", name="show", requirements={"id"="\d+"}, methods={"GET"})
    */
    public function ApiConversationWithMessage(Conversation $conv)
    {
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();
        //vérifie que l'utilisateur courant est bien membre ou auteur de la conversation
        if ($conv->getUsers()->contains($currentUser) || $conv->getAuthor() == $currentUser) {
            //renvoie la conversation ainsi que ses messages en Json
            return $this->json($conv, 200, [], ['groups' => ['userConversation', 'conversationShow']]);
        }
        return $this->json("L'utilisateur n'est pas membre de cette conversation", 403);
    }

    /**
    * @Route("/{id}/message/add", name="message_add",requirements={"id"="\d+"}, methods={"POST"})
    */
    public function ApiConversationMessageAdd(Conversation $conv, Request $request, EntityManagerInterface $em)
    {
        //décode les données de la requête
        $data = json_decode($request->getContent(), true);
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();

        // retourne une erreur si le message est vide
        if (empty($data['content'])) {
            return $this->json("Le message ne peut être vide", 403);
        }

        //vérifie que l'utilisateur courant est bien membre ou auteur de la conversation
        if ($conv->getUsers()->contains($currentUser) || $conv->getAuthor() == $currentUser) {
            
            //récupère le contenu de la requête dans un array
            $message = new Message;
            // set l'utilisateur courant comme auteur de message
            $message->setUser($currentUser);

            // set la conversation affichée comme lié au message
            $message->setConversation($conv);

            //set le contenu du message
            $message->setContent($data['content']);

            //sauvegarde le message
            $em->persist($message);
            $em->flush();

            //renvoie un message de confirmation avec un code 200
            return $this->json("message correctement posté", 200);
        }
        return $this->json("L'utilisateur n'est pas membre de cette conversation", 403);
    }

    /**
    * @Route("/{id}/user/add", name="user_add", requirements={"id"="\d+"}, methods={"POST"})
    */
    public function ApiConversationUserAdd(Conversation $conv, Request $request, EntityManagerInterface $em, UserRepository $userRepository)
    {
        //récupère le contenu de la requête dans un array
        $data = json_decode($request->getContent(), true);
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();

        //vérifie que l'utilisateur courant est bien membre ou auteur de la conversation
        if ($conv->getUsers()->contains($currentUser) || $conv->getAuthor() == $currentUser) {


            // trouve l'utilsateur à ajouter selon un id
            $userToAdd = $userRepository->find($data['userId']);

            //vérifie qu'un utilisateur correspondant existe bien
            if ($userToAdd != null) {
                //vérifie que l'utilisateur n'est pas déjà membre
                if (!$conv->getUsers()->contains($userToAdd)) {
                    //ajoute l'utilisateur à la conversation
                    $conv->addUser($userToAdd);
                    $em->flush();

                    //envoie un message de confirmation
                    return $this->json("Utilisateur correctement ajouté", 200);
                }
                return $this->json("Cet utilisateur est déjà membre de la conversation", 403);
            }

            return $this->json("L'utilisateur n'existe pas", 403);
        }
        return $this->json("L'utilisateur courant n'est pas membre de cette conversation", 403);
    }

    /**
    * @Route("/{id}/user/remove", name="user_remove", requirements={"id"="\d+"}, methods={"POST"})
    */
    public function ApiConversationUserRemove(Conversation $conv, Request $request, EntityManagerInterface $em, UserRepository $userRepository)
    {
        //récupère le contenu de la requête dans un array
        $data = json_decode($request->getContent(), true);
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();
        //trouve l'utilisateur à enlever de la conversation
        $userToRemove = $userRepository->find($data['userId']);

        //vérifie que l'utilisateur est soit l'utilisateur à enlever, soit l'auteur de la conversation
        if ($userToRemove == $currentUser || $conv->getAuthor() == $currentUser) {

            //vérifie que l'utilisateur à enlever existe bien

            if ($userToRemove != null) {
                //vérifie que l'utilisateur est bien dans la conversation
                if ($conv->getUsers()->contains($userToRemove)) {
                    //enlève l'utilisateur de la conversation
                    $conv->removeUser($userToRemove);
                    $em->flush();

                    //envoie un message de confirmation ainsi qu'un code 200
                    return $this->json("utilisateur correctement retiré", 200);
                }
                return $this->json("L'utilisateur n'est pas membre de cette conversation", 403);
            }

            return $this->json("L'utilisateur n'existe pas", 403);
        }
        return $this->json("L'utilisateur n'est pas membre de cette conversation", 403);
    }
}
