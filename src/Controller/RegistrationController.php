<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\AdminCodeRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, AdminCodeRepository $adminCodeRepository): Response
    {
        function manageFormData(User $user, $form, UserPasswordEncoderInterface $passwordEncoder){
            function manageEmail(User $user, $form){
                $user->setEmail($form->get('email')->getData() . "@iut-tarbes.fr");
            }
            function managePassword(User $user, $form, UserPasswordEncoderInterface $passwordEncoder){
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            function manageAdminYear(User $user, $form){
                if ($form->get('year')->getData() === null){
                    $user->setYear(4);
                }
            }
            function manageRoles(User $user, $form){
                require('Requires/Roles.php');
                $userRoles = [];
                for ($i = 0; $i <= $form->get('role')->getData() - 1; $i++){
                    array_push($userRoles, $roles[$i]);
                }
                $user->setRoles($userRoles);
            }

            manageEmail($user, $form);
            managePassword($user, $form, $passwordEncoder);
            $user->setIsValid(1); // 1 == invalid
            manageAdminYear($user, $form);
            manageRoles($user, $form);
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            manageFormData($user, $form, $passwordEncoder);
            if ($form->get('role')->getData() == 3 && $form->get('adminCode')->getData() !== $adminCodeRepository->findOneBy([], ['id' => 'DESC'])){
                $this->addFlash('danger', 'Votre requête est invalide. Veuillez réesayer');
                $this->redirectToRoute('app_register');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_register_after', ['id' => $user->getId()]);
        }

        return $this->render('registration/register.html.twig', ['registrationForm' => $form->createView()]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse email a bien été validée !');

        return $this->redirectToRoute('app_register');
    }

    /**
     * @Route("/register/{id<\d+>}/after", name="app_register_after", methods="GET")
     */
    public function after(User $user): Response
    {
        return $this->render('registration/after.html.twig', [
            'user' => $user
        ]);
    }
}
