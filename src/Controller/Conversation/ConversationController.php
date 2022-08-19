<?php

namespace App\Controller\Conversation;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
* @Route("/conv", name="conv_")
*/
class ConversationController extends AbstractController
{
    /**
    * @Route("/user/list", name="user_list")
    */
    public function conversationByUser(AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('ROLE_USER')) {

            // récupère les conversations de l'utilisateur courant et les renvoie à la vue
            $conversations = $this->getUser()->getConversations();
            return $this->render('conversation/conversation.list.html.twig', [
            'conversations' => $conversations,
        ]);
        }

        return $this->redirect($this->frontUrl.'/login');
    }

    /**
    * @Route("/new", name="new")
    */
    public function conversationNew(Request $request, EntityManagerInterface $em)
    {
        //Vérifie la méthode
        if ($request->getMethod() == "POST") {
            //stocke les données en post
            $title= $request->request->get('title');
            $content= $request->request->get('content');
            //set l'utilisateur courant pour plus de lisibilité
            $currentUser = $this->getUser();

            //vérifie que l'utilisateur n'a pas laissé de données vides
            if (!empty($title) && !empty($content)) {

                //Crée un nouvel objet de l'entité Conversation et set ses champs.
                $conv = new Conversation;
                $conv->setAuthor($currentUser);
                $conv->addUser($currentUser);
                $conv->setTitle($title);
                $em->persist($conv);
                $em->flush();

                //Crée un nouvel objet de l'entité Message et set ses champs en l'associant à la conversation créé.
                $message = new Message;
                $message->setConversation($conv);
                $message->setUser($currentUser);
                $message->setContent($content);
                $em->persist($message);
                $em->flush();

                //message flash pour indiquer la création réussie
                $this->addFlash(
                    'success',
                    'Nouvelle conversation bien créé'
                );

                //envoie vers la page pour ajouter des utilisateurs à la conversation
                return $this->redirectToRoute('conv_user_edit', ['id' => $conv->getId()]);
            }

            $this->addFlash(
                'warning',
                'Le contenu de votre conversation ne doit pas être vide'
            );


            return $this->render('conversation/conversation.new.html.twig');
        }
        return $this->render('conversation/conversation.new.html.twig');
    }

    /**
    * @Route("/{id}/show", name="show", requirements={"id"="\d+"}, methods={"GET"})
    */
    public function conversationWithMessage(Conversation $conv)
    {
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();
        //vérifie que l'utilisateur courant est bien membre ou auteur de la conversation
        if ($conv->getUsers()->contains($currentUser) || $conv->getAuthor() == $currentUser) {

            return $this->render('conversation/conversation.show.html.twig', [
                'conv' => $conv,
            ]);
        }
        return $this->redirectToRoute('conv_user_list');
    }

    /**
    * @Route("/{id}/message/add", name="message_add",requirements={"id"="\d+"}, methods={"POST"})
    */
    public function conversationMessageAdd(Conversation $conv, Request $request, EntityManagerInterface $em)
    {
        //stocke les données de la requête
        $data = ['content' => $request->request->get('content') ];
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();

        // retourne une erreur si le message est vide
        if (empty($data['content'])) {

            $this->addFlash(
                'warning',
                'Le contenu de votre message ne doit pas être vide'
            );

            return $this->redirectToRoute('conv_show', ['id' => $conv->getId()]);

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

            $this->addFlash(
                'success',
                'Message bien envoyé'
            );

            return $this->redirectToRoute('conv_show', ['id' => $conv->getId()]);
        }
        $this->addFlash(
            'warning',
            "vous n'êtes pas membre de cette discussion"
        );
        return $this->redirectToRoute('conv_user_list');
    }

    /**
    * @Route("/{id}/tag/edit", name="tag_edit", requirements={"id"="\d+"}, methods={"GET"})
    */
    public function conversationTagEdit(Conversation $conv, TagRepository $tagRepository)
    {
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();
        if ($conv->getAuthor() == $currentUser) {

            return $this->render('conversation/conversation.tag.edit.html.twig', [
                'conv' => $conv,
                'tags' => $tagRepository->findAll()
            ]);
        }

        $this->addFlash(
            'warning',
            "vous n'êtes pas l'auteur de cette discussion"
        );
        return $this->redirectToRoute('conv_show', ['id' => $conv->getId() ]);
    }

    /**
    * @Route("/{id}/tag/ajax", name="tag_ajax", requirements={"id"="\d+"}, methods={"POST"})
    */
    public function conversationTagEditAjax(Conversation $conv, Request $request, EntityManagerInterface $em, TagRepository $tagRepository)
    {
        $tagId = $request->request->get('id');
        $tag = $tagRepository->find($tagId);

        $sub = $request->request->get('sub');
        $subBoolean = ($sub === 'true');
        if ($subBoolean) {
            $conv->removeTag($tag);
        } else {
            $conv->addTag($tag);
        }

        $em->flush();
        
        return $this->json("tag correctement modifié", 200);
    }

    /**
    * @Route("/{id}/user/edit", name="user_edit", requirements={"id"="\d+"})
    */
    public function conversationUserEdit(Conversation $conv, Request $request, UserRepository $userRepository)
    {
        //set l'utilisateur courant pour plus de lisibilité
        $currentUser = $this->getUser();
        if ($conv->getAuthor() == $currentUser) {

            if ($request->getMethod() == "POST") {
                $search = $request->request->get('search');
                $users= $userRepository->findByUsername($search);
                return $this->render('conversation/conversation.user.edit.html.twig', [
                    'search'=> $search,
                    'conv' => $conv,
                    'users' => $users
                ]);
            }

            return $this->render('conversation/conversation.user.edit.html.twig', [
                'conv' => $conv
            ]);
        }

        $this->addFlash(
            'warning',
            "vous n'êtes pas l'auteur de cette discussion"
        );
        return $this->redirectToRoute('conv_show', ['id' => $conv->getId() ]);
    }


    /**
    * @Route("/{id}/user/ajax", name="user_ajax", requirements={"id"="\d+"}, methods={"POST"})
    */
    public function conversationUserEditAjax(Conversation $conv, Request $request, EntityManagerInterface $em, UserRepository $userRepository)
    {
        $userId = $request->request->get('id');
        $user = $userRepository->find($userId);

        $currentUser = $this->getUser();

        if ($conv->getUsers()->contains($user) && $conv->getAuthor() == $currentUser) {
            $conv->removeUser($user);
        } else {
            $conv->addUser($user);
        }

        $em->flush();
        
        return $this->json("Utilisateur correctement modifié", 200);
    }
}
