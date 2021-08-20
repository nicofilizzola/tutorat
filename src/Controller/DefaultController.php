<?php

namespace App\Controller;

use App\Controller\Traits\adminValidationEmail;
use App\Controller\Traits\isVerifiedUser;
use App\Entity\Session;
use App\Repository\FacultyRepository;
use App\Repository\SessionRepository;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Traits\emailRegex;
use App\Traits\getRoles;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends AbstractController
{
    use adminValidationEmail;
    use getRoles;
    use emailRegex;
    use isVerifiedUser;

    private function isOnlyStudent(){
        return !$this->isVerifiedUser() || in_array($this->getRoles()[1], $this->getUser()->getRoles()) ? false : true;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(SessionRepository $sessionRepository, UserRepository $userRepository): Response
    {
        return $this->render('default/index.html.twig', [
            'sessions' => $this->isVerifiedUser() ? $sessionRepository->findByStudentAwaiting($this->getUser()) : null,
            'users' => $this->getUser() && in_array($this->getRoles()[3], $this->getUser()->getRoles()) ? 
                $userRepository->findBy([
                    'isValid' => 1, 
                    'isVerified' => true
                ]) :
                null,
            'roles' => $this->getRoles()
        ]);
    }

    /**
     * @Route("/become-tutor", name="app_become-tutor", methods={"GET", "POST"})
     */
    public function becomeTutor(Request $request, EntityManagerInterface $em, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        if (!$this->isOnlyStudent()){
            return $this->redirectToRoute('app_home');
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('become-tutor', $submittedToken)) {
            $this->getUser()->setRoles([$this->getRoles()[0], $this->getRoles()[1]]);
            $this->getUser()->setIsValid(1);

            $em->persist($this->getUser());
            $em->flush();

            // reauth user
            $token = new UsernamePasswordToken($this->getUser(), null, 'main', $this->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            $this->sendAdminsEmailForPendingUser($mailer, $userRepository);

            $this->addFlash('success', 'Votre demande a bien été envoyée ! Vous aurez une réponse dans environ une semaine.');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('default/become-tutor.html.twig');
    }

    /**
     * @Route("/contact", name="app_contact", methods={"GET", "POST"})
     */
    public function contact(Request $request, MailerInterface $mailer, UserRepository $userRepository, FacultyRepository $facultyRepository): Response
    {
        if ($request->isMethod('post')){
            if (!$this->isCsrfTokenValid('contact', $request->request->get('token'))){
                $this->addFlash('danger', "Une erreur est survenue...");
                return $this->redirectToRoute('app_contact');
            }

            $postRequest = $request->request;
            $data = [
                'email' => $postRequest->get('email'),
                'faculty' => $postRequest->get('faculty'),
                'subject' => $postRequest->get('subject'),
                'message' => $postRequest->get('message'),
                'date' => date('d-m-Y')
            ];
            $adminEmails = $userRepository->findFacultyAdminEmails($facultyRepository->findOneBy(['id' => $data['faculty']]));

            if (
                is_null($data['email']) || 
                is_null($data['faculty']) ||
                is_null($data['subject']) || 
                is_null($data['message'])
            ){
                $this->addFlash('danger', "Tous les champs n'ont pas été remplis.");
                return $this->redirectToRoute('app_contact');
            }

            if (!preg_match($this->getEmailRegex(), $data['email'])){
                $this->addFlash('danger', "Adresse email invalide.");
                return $this->redirectToRoute('app_contact');
            }

            $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
            ->to(...$adminEmails)
            ->subject('Contact: Nouveau message')
            ->htmlTemplate('email/contact.html.twig')
            ->context([
                // 'link' => $this->generateUrl('app_sessions_pending', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'data' => $data,
                'adminEmails' => $adminEmails
            ]);
            $mailer->send($email);

            $this->addFlash('danger', "Ton message a bien été envoyé ! Tu recevras une réponse par mail dans environ une semaine.");
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('default/contact.html.twig', [
            'faculties' => $facultyRepository->findAll()
        ]);
    }
}
