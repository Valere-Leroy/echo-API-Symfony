<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\Conversation;
use App\Entity\Notification;
use App\Entity\StaffMessage;
use Doctrine\ORM\EntityManager;
use App\Repository\EchoPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/api/user", name="api_user_")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/myprofile", name="myprofile", methods={"GET"})
     */
    public function myProfile(Request $request)
    {
        return $this->json($this->getUser(), 200, [], ['groups' => ['userShow']]);
    }
    
    /**
     * @Route("/{id}/show", name="show", requirements={"id"="\d+"})
     */
    public function ApiUserShow(User $user)
    {
        $data = [
            'user' => $user,
        ];

        return $this->json($data, 200, [], ['groups' => ['userShow']]);
    }

    /**
     * @Route("/notification/all", name="notification_all")
     */
    public function ApiUserNotificationAll(NotificationRepository $notifications)
    {
        $user =$this->getUser();

        $notificationsNotValidate =$notifications->findBy(['user' => $user]);
        $data = [
            'notifications' => $notificationsNotValidate,
        ];

        return $this->json($data, 200, [], ['groups' => ['notificationData']]);
    }

    /**
     * @Route("/notification/new", name="notification_new")
     */
    public function ApiUserNotificationNew(NotificationRepository $notifications)
    {
        $user =$this->getUser();

        $notificationsNotValidate =$notifications->findBy(['user' => $user, 'isValidated' => false]);
        $data = [
            'notifications' => $notificationsNotValidate,
        ];

        return $this->json($data, 200, [], ['groups' => ['notificationData']]);
    }

    /**
     * @Route("/notification/{id}/viewed", name="notification_viewed")
     */
    public function ApiUserNotificationChange(Notification $notification, EntityManagerInterface $em)
    {
        $notification->setIsValidated(true);
        $em->flush();       

        return $this->json($notification, 200, [], ['groups' => ['notificationData']]);
    }

    /**
    * @Route("/{id}/echo/list", name="echo_list", requirements={"id"="\d+"})
    */
    public function ApiByUserEchoList(User $user, EchoPostRepository $echos, Request $request, AuthorizationCheckerInterface $authChecker)
    {
        $parameters = [];
        if (!$authChecker->isGranted('ROLE_MODERATOR')) {
            $parameters += ["moderate" => false];
        }

        $data = json_decode($request->getContent(), true);
        $filterRequest = $data['filter'];
        $orderRequest = $data['order'];
        
        if (isset($filterRequest)) {
            $filter = $filterRequest;
        } else {
            $filter = 'createdAt';
        }

        if (isset($orderRequest)) {
            $order = $orderRequest;
        } else {
            $order = 'DESC';
        }

        $parameters =
        [
            'filter' => $filter,
            'order' => $order,
            'user' => $user
        ];

        $queryEchos = $echos->findList($parameters);

        $data = [
            'user' => $user,
            'echos' => $queryEchos,
        ];

        return $this->json($data, 200, [], ['groups' => ['echoList']]);
    }

    /**
     * @Route("/tag/{id}/subscribe", name="tag_subscribe", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function ApiUserSubscribe(
        AuthorizationCheckerInterface $authChecker,
        EntityManagerInterface $em,
        Tag $tag
    ) {
        if ($authChecker->isGranted('ROLE_USER')) {
            ($this->getUser())->addTag($tag);
            $em->flush();

            return $this->json(
               [
                   'status' => 200,
                   'message' => $tag->getName(). " ajout?? ?? la liste"
                ]
           );
        } else {
            return $this->json("utilisateur non connect?? ou autoris??", 401);
        }
    }

    /**
     * @Route("/tag/{id}/unsubscribe", name="tag_unsubscribe", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function ApiUserUnsubscribe(
        AuthorizationCheckerInterface $authChecker,
        EntityManagerInterface $em,
        Tag $tag
    ) {
        if ($authChecker->isGranted('ROLE_USER')) {
            ($this->getUser())->removeTag($tag);
            $em->flush();

            return $this->json(
                [
                   'status' => 200,
                   'message' => $tag->getName(). " retir?? de la liste"
                ]
            );
        } else {
            return $this->json("utilisateur non connect?? ou autoris??", 401);
        }
    }

    /**
     * @Route("/contact", name="contact", methods={"POST"})
     */
    public function ApiContactForm(
        EntityManagerInterface $em,
        Request $request
    ) {
        $message = new StaffMessage;
        $data = json_decode($request->getContent(), true);

        if ($data['name'] == null) {
            return $this->json('Le nom du message est vide.', 403);
        }
        if ($data['email'] == null) {
            return $this->json('L\'email du message est vide.', 403);
        }
        if ($data['message'] == null) {
            return $this->json('Le contenu du message est vide.', 403);
        }

        $message->setEmail($data['email']);
        $message->setUsername($data['name']);
        $message->setContent($data['message']);

        $em->persist($message);
        $em->flush();

        return $this->json('Le message de contact a bien ??t?? envoy??.', 200);
    }

    /**
     * @Route("/{id}/conversation/new", name="conversation_new", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function ApiUserConversationNew(User $user, EntityManagerInterface $em, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $currentUser =$this->getUser();
        $conv = new Conversation;
        $conv->setAuthor($currentUser);
        $conv->addUser($user);
        $conv->addUser($currentUser);
        $conv->setTitle($data['newConv']['title']);
        $em->persist($conv);
        $em->flush();
        $message = new Message;
        $message->setUser($currentUser);
        $message->setConversation($conv);
        $message->setContent($data['newConv']['content']);
        $em->persist($message);
        $em->flush();
        $result = [
            "id" => $conv->getId(),
            "message" => "Conversation correctement cr????"
        ];
        return $this->json($result, 200);
    }

    /**
     * @Route("/follow/{id}", name="follow_user", methods={"GET"})
     */
    public function followUser(User $user = null, EntityManagerInterface $em)
    {
        // si l'utilisateur s'abonne a un utilisateur inexistant
        if($user == null) {
            return $this->json(
                [
                    'message' => "Erreur dans la requ??te: utilisateur inexistant",
                ], 
                Response::HTTP_BAD_REQUEST);
        }

        // si l'utilisateur s'abonne a lui m??me 
        if($user == $this->getUser()) {
            return $this->json(
                [
                    'message' => "L'utilisateur ne peut s'abonner ?? lui m??me !",
                ], 
                Response::HTTP_FORBIDDEN);
        }

        // Si il a deja  cet utiisateur p??rmis ses abonn??s
        foreach($this->getUser()->getUsers() as $currentFollowedUser) {
            if( $currentFollowedUser == $user) {
                return $this->json(
                    [
                        'message' => "D??j?? abonn?? ?? cet utilisateur",
                    ], 
                    Response::HTTP_BAD_REQUEST);
            }
        }

        // Ajoute a l'utilisateur cible le user actuel comme abonn??
        $user->addFollower($this->getUser());

        // ajoute au user actuel l'utilisateur cible comme abonnement
        $this->getUser()->addUser($user);

        $em->persist($user);
        $em->flush();

        return $this->json(
            [
                'message' => "Abonn?? a cet utilisateur avec succ??s",
            ], 
            Response::HTTP_OK);
    }

    /**
     * @Route("/unfollow/{id}", name="unfollow_user", methods={"GET"})
     */
    public function unfollowUser(User $user = null, EntityManagerInterface $em)
    {
        if($user == null) {
            return $this->json(
                [
                    'message' => "Erreur dans la requ??te: utilisateur inexistant",
                ], 
                Response::HTTP_BAD_REQUEST);
        }

        if($user == $this->getUser()) {
            return $this->json(
                [
                    'message' => "L'utilisateur ne peut d??sabonner de lui m??me !",
                ], 
                Response::HTTP_FORBIDDEN);
        }

        // supprime a l'utilisateur cible le user actuel abonn??
        $user->removeFollower($this->getUser());

        // supprime au user actuel l'utilisateur cible comme abonnement
        $this->getUser()->removeUser($user);

        $em->persist($user);
        $em->flush();

        return $this->json(
            [
                'message' => "D??sabonn?? de cet utilisateur avec succ??s",
            ], 
            Response::HTTP_OK);
    }
}
