<?php

namespace App\Controller;

use App\Controller\Traits\adminValidationEmail;
use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\AdminCodeRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    use adminValidationEmail;

    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, AdminCodeRepository $adminCodeRepository, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        function manageFormData(User $user, $form, UserPasswordEncoderInterface $passwordEncoder){
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
                require('../Requires/Roles.php');
                $userRoles = [];
                for ($i = 0; $i <= $form->get('role')->getData() - 1; $i++){
                    array_push($userRoles, $roles[$i]);
                }
                $user->setRoles($userRoles);
            }
            function manageIsValid($user, $form){
                if ($form->get('role')->getData() > 1){
                    $user->setIsValid(1); // 1 == pending    
                } else {
                    $user->setIsValid(2); // 2 == valid   
                }
            }

            managePassword($user, $form, $passwordEncoder);
            manageAdminYear($user, $form);
            manageRoles($user, $form);
            manageIsValid($user, $form);
            $user->updateTimestamp();
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            manageFormData($user, $form, $passwordEncoder);
            if ($form->get('role')->getData() >= 3 && $form->get('adminCode')->getData() !== $adminCodeRepository->findOneBy([], ['id' => 'DESC'])){
                $this->addFlash('danger', 'Votre requête est invalide. Veuillez réesayer');
                $this->redirectToRoute('app_register');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // user email
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
                    ->to($user->getEmail())
                    ->subject('Vérifiez votre adresse email')
                    ->htmlTemplate('email/verify_email.html.twig')
            );
            
            // admin email
            $this->sendAdminsEmailForPendingUser($mailer, $userRepository);

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
