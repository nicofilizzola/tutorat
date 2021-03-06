<?php

namespace App\Controller;

use App\Traits\getRoles;
use App\Traits\emailRegex;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Controller\Traits\emailData;
use App\Repository\FacultyRepository;
use App\Repository\SessionRepository;
use App\Repository\SemesterRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Traits\isVerifiedUser;
use App\Controller\Traits\roleValidation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Traits\adminValidationEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends AbstractController
{
    use emailData;
    use adminValidationEmail;
    use getRoles;
    use emailRegex;
    use isVerifiedUser;
    use roleValidation;


    private function isOnlyStudent(){
        return !$this->isVerifiedUser() || in_array($this->getRoles()[1], $this->getUser()->getRoles()) ? false : true;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(SessionRepository $sessionRepository, UserRepository $userRepository, SemesterRepository $semesterRepository): Response
    {
        $faculty = $this->isSecretary(true) ? $this->getUser()->getFaculty() : null;

        return $this->render('default/index.html.twig', [
            'sessions' => $this->isVerifiedUser() ? $sessionRepository->findByStudentAwaiting($this->getUser()) : null,
            'awaiting_sessions' => !is_null($faculty) ? $sessionRepository->findByFacultyAfterToday(
                $faculty,
               [
                   'isValid' => 0,
                   'semester' => $semesterRepository->findCurrentFacultySemester($faculty)
                ],
            ) : null,
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
     * @Route("/become_tutor", name="app_become_tutor", methods={"GET", "POST"})
     */
    public function becomeTutor(Request $request, EntityManagerInterface $em, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        if (!$this->isOnlyStudent()){
            return $this->redirectToRoute('app_home');
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('become_tutor', $submittedToken)) {
            $this->getUser()->setRoles([$this->getRoles()[0], $this->getRoles()[1]]);
            $this->getUser()->setIsValid(1);

            $em->persist($this->getUser());
            $em->flush();

            // reauth user
            $token = new UsernamePasswordToken($this->getUser(), null, 'main', $this->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            $this->sendAdminsEmailForPendingUser($mailer, $userRepository);

            $this->addFlash('success', 'Votre demande a bien ??t?? envoy??e ! Vous aurez une r??ponse dans environ une semaine.');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('default/become_tutor.html.twig');
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
                $this->addFlash('danger', "Tous les champs n'ont pas ??t?? remplis.");
                return $this->redirectToRoute('app_contact');
            }

            if (!preg_match($this->getEmailRegex(), $data['email'])){
                $this->addFlash('danger', "Adresse email invalide.");
                return $this->redirectToRoute('app_contact');
            }

            $this->sendEmail($mailer, $adminEmails, 'Nouveau message', 'contact.html.twig', [
                // 'link' => $this->generateUrl('app_sessions_pending', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'data' => $data,
                'adminEmails' => $adminEmails
            ]);

            $this->addFlash('danger', "Ton message a bien ??t?? envoy?? ! Tu recevras une r??ponse par mail dans environ une semaine.");
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('default/contact.html.twig', [
            'faculties' => $facultyRepository->findAll()
        ]);
    }
}
