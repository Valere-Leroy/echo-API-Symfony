<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\EchoPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user/list", name="admin_user_list")
     */
    public function userList(AuthorizationCheckerInterface $authChecker, UserRepository $userRepository)
    {
        if ($authChecker->isGranted('ROLE_ADMIN')) {
        return $this->render('admin/user/userList.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
        } else {
            return $this->redirectToRoute('admin_app_login');
        }
    }

    /**
     * @Route("/admin/user/edit/{id}", name="admin_user_edit")
     */
    public function userEdit(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', 'L\'utilisateur "' . $user->getUsername() . '" a bien été modifié');
            return $this->redirectToRoute('admin_user_list');
        }
        return $this->render('admin/user/userForm.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete")
     */
    public function delete(AuthorizationCheckerInterface $authChecker, User $user, EntityManagerInterface $em)
    {
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('info', "L'utilisateur à bien été supprimer");
            return $this->redirectToRoute('admin_user_list');
        } else {
            return $this->redirectToRoute('admin_app_login');
        }
    }

    /**
     * @Route("/admin/user/{id}/echo/list", name="admin_user_echo_list")
     */
    public function adminUserEchoList(User $user, AuthorizationCheckerInterface $authChecker, EchoPostRepository $echos, Request $request)
    {
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            $parameters = [];
            $filterRequest = $request->query->get('filter');
            $orderRequest = $request->query->get('order');

            if (isset($filterRequest)) {
                $filter = $filterRequest;
            } else {
                $filter = 'id';
            }

            if (isset($orderRequest)) {
                $order = $orderRequest;
            } else {
                $order = 'ASC';
            }

            $parameters +=
                [
                'user' => $user,
                'filter' => $filter,
                'order' => $order,
            ];

            $queryEchos = $echos->findList($parameters);
            return $this->render('admin/user/echo.list.html.twig', [
                'echos' => $queryEchos,
                'user' => $user
            ]);
        } else {
            return $this->redirectToRoute('admin_app_login');
        }
    }

    /**
     * @Route("/admin/user/{id}/activate", name="admin_user_activate")
     */
    public function moderateEcho(User $user, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('ROLE_MODERATOR')) {

            if ($user->getIsActivated()) {
                $user->setIsActivated(false);
                $em->flush();
                $message = "L'utilisateur est désactivé";
                $code = 200;

            } else {
                $user->setIsActivated(true);
                $em->flush();
                $message = "L'utilisateur est activé";
                $code = 200;
            }
        } else {
            $message = "utilisateur non connecté ou autorisé";
            $code = 401;
        }

        return $this->json($message, $code);
    }
}
