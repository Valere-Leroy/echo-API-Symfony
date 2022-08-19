<?php

namespace App\Controller\Admin;

use App\Entity\Commentary;
use App\Form\CommentaryFormType;
use App\Repository\CommentaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminCommentaryController extends AbstractController
{
    /**
     * @Route("/admin/commentary/list", name="admin_commentary_list")
     */
    public function index(AuthorizationCheckerInterface $authChecker, CommentaryRepository $commentaryRepository)
    {
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/commentary/index.html.twig', [
                'comments' => $commentaryRepository->findAll(),
            ]);
            } else {
                return $this->redirectToRoute('admin_app_login');
            }
    }

    /**
     * @Route("/admin/commentary/edit/{id}", name="admin_commentary_edit")
     */
    public function commentaryEdit(Commentary $commentary, Request $request)
    {
        $form = $this->createForm(CommentaryFormType::class, $commentary);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $commentary->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', "Le commentaire a bien été modifié");
            return $this->redirectToRoute('admin_commentary_list');
        }
        return $this->render('admin/commentary/commentaryForm.html.twig', [
            'form' => $form->createView(),
            'comment' => $commentary,
        ]);
    }


    /**
     * @Route("/admin/commentary/delete/{id}", name="admin_commentary_delete")
     */
    public function delete(AuthorizationCheckerInterface $authChecker, Commentary $commentary, EntityManagerInterface $em)
    {
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            $em->remove($commentary);
            $em->flush();
            $this->addFlash('info', "Le commentaire à bien été supprimer");
            return $this->redirectToRoute('admin_commentary_list');
        } else {
            return $this->redirectToRoute('admin_app_login');
        }
    }
}
