<?php

namespace App\Controller\Admin;

use App\Entity\Commentary;
use App\Entity\EchoPost;
use App\Form\EchoFormType;
use App\Form\LoginFormType;
use App\Repository\EchoPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    /**
     * @Route("/api/admin/echo/{id}/moderate", name="api_admin_echo_moderate")
     */
    public function moderateEcho(EchoPost $echo, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('ROLE_MODERATOR')) {

            if ($echo->getIsModerated()) {
                $echo->setIsModerated(false);
                $em->flush();
                $message = "L'écho n'est plus modéré";
                $code = 200;

            } else {
                $echo->setIsModerated(true);
                $em->flush();
                $message = "L'écho est correctement modéré";
                $code = 200;
            }
        } else {
            $message = "utilisateur non connecté ou autorisé";
            $code = 401;
        }

        return $this->json($message, $code);
    }

    /**
     * @Route("/api/admin/echo/commentary/{id}/moderate", name="api_admin_echo_commentary_moderate")
     */
    public function moderateCommentary(Commentary $commentary, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('ROLE_MODERATOR')) {

            if ($commentary->getIsModerated()) {
                $commentary->setIsModerated(false);
                $em->flush();
                $message = "Le commentaire n'est plus modéré";
                $code = 200;

            } else {
                $commentary->setIsModerated(true);
                $em->flush();
                $message = "Le commentaire est correctement modéré";
                $code = 200;
            }
        } else {
            $message = "utilisateur non connecté ou autorisé";
            $code = 401;
        }

        return $this->json($message, $code);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(AuthorizationCheckerInterface $authChecker, EchoPostRepository $echos, Request $request)
    {
        if ($authChecker->isGranted('ROLE_ADMIN')) {

            $parameters = [];
            $filterRequest = $request->query->get('filter');
            $orderRequest = $request->query->get('order');
            $searchRequest = $request->request->get('search');

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


            if (isset($searchRequest)) {
                $parameters += [   'search' => $searchRequest ];
            }

            $parameters +=
                [
                'filter' => $filter,
                'order' => $order,
            ];

            $queryEchos = $echos->findList($parameters);
            return $this->render('admin/index.html.twig', [
                'echos' => $queryEchos,
            ]);
        } else {
            return $this->redirectToRoute('admin_app_login');
        }
    }

    /**
     * @Route("admin/echo/{id}/edit", name="admin_echo_edit", requirements={"id"="\d+"})
     */
    public function editEcho(EchoPost $echo, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(EchoFormType::class, $echo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash(
                'success',
                'echo correctly edited'
            );
            $echo->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/echo.edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/echo/{id}/delete", name="admin_echo_delete", requirements={"id"="\d+"})
     */
    public function deleteEcho(EchoPost $echo, Request $request, EntityManagerInterface $em)
    {

        $this->addFlash(
            'warning',
            'echo permantly delete'
        );
        $em->remove($echo);
        $em->flush();
        return $this->redirectToRoute('admin');
    }
}
