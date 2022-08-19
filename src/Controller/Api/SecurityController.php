<?php

namespace App\Controller\Api;

use Swift_Mailer;
use App\Entity\User;
use App\Entity\Validator\RegistrationHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api", name="api_")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(Request $request)
    {
        return $this->json($this->getUser(), 200, [], ['groups' => ['userShow']]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->json("utilisateur déconnecté", 200);
    }

    /**
     * @Route("/logout/success", name="app_logout_success")
     */
    public function logoutCheck()
    {
        return $this->json(
            [
                'status' => Response::HTTP_OK, 
                'message' => 'Logout OK',
            ], 
            Response::HTTP_OK);
    }


    /**
     * @Route("/registration", name="app_registration", methods={"POST"})
     */
    public function registration(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, ValidatorInterface $validator, Swift_Mailer $mailer)
    {
        $content = $request->getContent();
        $data = json_decode($content);

        // Check if username or email is already registered
        $isRegisteredUserName = $userRepository->findOneBy(['username' => $data->username]);
        $isRegisteredEmail = $userRepository->findOneBy(['email' => $data->email]);
        if($isRegisteredUserName || $isRegisteredEmail) {
            return $this->json(
                [
                    'status' => Response::HTTP_FORBIDDEN, 
                    'message' => 'Nom de compte ou email déjà existant'
                ], 
                Response::HTTP_FORBIDDEN);
        }

        // send registration information to the Registration Entity to handle errors
        $registrationHandler = new RegistrationHandler();
        $registrationHandler->setUsername($data->username);
        $registrationHandler->setEmail($data->email);
        $registrationHandler->setPassword($data->password);
        $registrationHandler->setConfirmPassword($data->confirmPassword);

        //Handling Error
        $usernameError = $validator->validateProperty($registrationHandler, 'username');
        $emailError = $validator->validateProperty($registrationHandler, 'email');
        $passwordError = $validator->validateProperty($registrationHandler, 'password');
        $confirmPasswordError = $validator->validateProperty($registrationHandler, 'confirmPassword');
        
        // Pass all errors on a table and return it as json data
        $formsEror = [];
        if(count($usernameError) > 0) {
            $formsEror['usernameError'] = $usernameError[0]->getMessage();
        }
        if(count($emailError) > 0) {
            $formsEror['emailError'] = $emailError[0]->getMessage();
        }
        if(count($passwordError) > 0) {
            $formsEror['passwordError'] = $passwordError[0]->getMessage();
        }
        if(count($confirmPasswordError) > 0) {
            $formsEror['confirmPasswordError'] = $confirmPasswordError[0]->getMessage();
        }
        if($formsEror) {
            return $this->json($formsEror, Response::HTTP_UNAUTHORIZED); 
        }

        //If no error returned, start the registration process with the sending of the email
        $user = new User();
        $hashPassword = $encoder->encodePassword($user, $registrationHandler->getPassword());
        $user->setUsername($registrationHandler->getUsername());
        $user->setEmail($registrationHandler->getEmail());
        $user->setPassword($hashPassword);
        $user->setActivationCode($this->keyGenerator());
        $user->setAdress('null');
        $user->setRole(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        // Send email once is registered into the DB
        $this->sendRegistrationMail($mailer, $user->getUsername(), $user->getEmail(), $user->getActivationCode());
        return $this->json(
            [
                'status' => Response::HTTP_OK, 
                'message' => 'Inscription et email OK',
            ], 
            Response::HTTP_OK);

    }

    /**
     * Account Activation
     * On recupère un utilisateur avec son code d'activation. si il n'existe pas, $user vaut null par défaut
     * @Route("/activation/{activationCode}", name="api_activation", methods={"GET"})
     * 
     */
    public function activation(User $user = null, EntityManagerInterface $em)
    {
        // si l'activation code n'appartient a aucun user enregistré
        if($user == null) {
            return $this->json(    
                [
                    'status' => Response::HTTP_BAD_REQUEST, 
                    'message' => 'Error',
                ], 
                Response::HTTP_BAD_REQUEST);
        }

        // si le user a un compte déjà actif
        if($user->getIsActivated() === true) {
            return $this->json(
                [
                    'status' => Response::HTTP_BAD_REQUEST, 
                    'message' => 'already activated',
                ], 
                Response::HTTP_BAD_REQUEST);
        }
        $user->setIsActivated(true);
        $em->flush();
        return $this->json(
            [
                'status' => Response::HTTP_OK, 
                'message' => 'Activation OK',
            ], 
            Response::HTTP_OK);
    }

    /**
     * Generate activation code based on random bytes hashed in sha256
     */
    private function keyGenerator() 
    {
        return hash('sha256', random_bytes(10));
    }


    private function sendRegistrationMail($mailer, $user, $userEmail, $activationCode)
    {
        $mail = (new Swift_Message('Inscription sur Echo'));
        $mail->setFrom('staff@echo.fr');
        $mail->setTo($userEmail);
        $mail->setBody(         // Retourne une vue twig uniquement pour l'email (pas de style)
            $this->renderView(
                'email/registration.html.twig', [
                    'user' => $user,
                    'activationCode' => $activationCode
                ]
                ),
                'text/html'
            );
            $mailer->send($mail);
    }
}