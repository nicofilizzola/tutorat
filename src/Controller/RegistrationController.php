<?php

namespace App\Controller;

use App\Entity\User;
use App\Traits\getRoles;
use App\Form\FacultyType;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\FacultyRepository;
use App\Repository\AdminCodeRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Traits\adminValidationEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    use adminValidationEmail;
    use getRoles;

    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, FacultyRepository $facultyRepository, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        function manageFormData(User $user, $form, UserPasswordHasherInterface $passwordHasher){
            function managePassword(User $user, $form, UserPasswordHasherInterface $passwordHasher){
                // encode the plain password
                $user->setPassword(
                    $passwordHasher->hashPassword(
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
            function manageIsValid($user, $form){
                if ($form->get('role')->getData() > 1){
                    $user->setIsValid(1); // 1 == pending    
                } else {
                    $user->setIsValid(2); // 2 == valid   
                }
            }

            managePassword($user, $form, $passwordHasher);
            manageAdminYear($user, $form);
            manageIsValid($user, $form);
            $user->updateTimestamp();
        }

        if ($this->getUser() || empty($facultyRepository->findAll())) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            manageFormData($user, $form, $passwordHasher);
            $userRoles = [];
            for ($i = 0; $i <= $form->get('role')->getData() - 1; $i++){
                array_push($userRoles, $this->getRoles()[$i]);
            }
            $user->setRoles($userRoles);

            if ($form->get('role')->getData() >= 3 && $form->get('adminCode')->getData() !== $facultyRepository->findOneBy(['id' => $form->get('faculty')->getData()])->getCode()){
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
                    ->subject('Tutoru : Vérifiez votre adresse email')
                    ->htmlTemplate('email/verify_email.html.twig')
                    ->context([
                        'homeLink' => $this->generateUrl('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'loginLink' => $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'registerLink' => $this->generateUrl('app_register', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ])
            );
            
            if (in_array($this->getRoles()[1], $user->getRoles())){
                $this->sendAdminsEmailForPendingUser($mailer, $userRepository, $user);
            }

            return $this->render('registration/after.html.twig', [
                'user' => $user,
            ]);
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
}
