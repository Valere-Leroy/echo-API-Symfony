<?php

namespace App\Controller\Admin;

use Swift_Mailer;
use Swift_Message;
use App\Entity\User;
use App\Form\ContactType;
use App\Entity\StaffMessage;
use App\Repository\StaffMessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/contact", name="contact_")
 */
class AdminContactController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StaffMessageRepository $staffMessageRepository, Request $request)
    {
        $filter = 'createdAt';
        $order = 'ASC';

        if($request->query->get('filter') != null){
            $filter = $request->query->get('filter');
        };
        if($request->query->get('order') != null){
            $order = $request->query->get('order');
        };

        $contacts = $staffMessageRepository->findAllBy($filter, $order);

        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * @Route("/{id}/answer", name="answer")
     */
    public function answer(StaffMessage $staffmessage, Request $request, Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class, $staffmessage);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->sendContactMail($mailer, $staffmessage->getEmail(), $staffmessage->getAnswer(), $staffmessage->getContent());
        }
        return $this->render('admin/contact/single.html.twig', [
            'contact' => $staffmessage,
            'form' => $form->createView(),
        ]); 
    }

    private function sendContactMail($mailer, $userEmail,$answer, $content)
    {
        $mail = (new Swift_Message('Réponse à votre message'));
        $mail->setFrom('staff@echo.fr');
        $mail->setTo($userEmail);
        $mail->setBody(         // Retourne une vue twig uniquement pour l'email (pas de style)
            $this->renderView(
                'email/contact.html.twig', [
                    'answer' => $answer,
                    'content' => $content,
                ]
                ),
                'text/html'
            );
            $mailer->send($mail);
    }
}
