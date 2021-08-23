<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SemesterType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use App\Repository\SessionRepository;
use App\Repository\ClassroomRepository;
use App\Repository\SemesterRepository;
use App\Traits\getRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecretaryController extends AbstractController
{
    use getRoles;

    private function isSecretary(){
        $userRoles = $this->getUser()->getRoles();
        if (
            !$this->getUser() ||
            !in_array($this->getRoles()[2], $userRoles) || 
            in_array($this->getRoles()[3], $userRoles) || 
            $this->getUser()->getIsValid() !== 2 || 
            !$this->getUser()->isVerified()
        ){
            return false;
        }
        return true;
    }

    /**
     * @Route("/sessions/pending", name="app_sessions_pending", methods="GET")
     */
    public function sessionsPending(SessionRepository $sessionRepository, ClassroomRepository $classroomRepository, SemesterRepository $semesterRepository): Response
    {
        if (!$this->isSecretary()){
            return $this->redirectToRoute('app_home');
        }

        $faculty = $this->getUser()->getFaculty();

        return $this->render('secretary/sessions-pending.html.twig', [
           'sessions' => $sessionRepository->findByFacultyAfterToday(
                $faculty,
               [
                   'isValid' => 0,
                   'semester' => $semesterRepository->findCurrentFacultySemester($faculty)
                ],
            ), 
           'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/validate", name="app_session_validate")
     */
    public function validateSession(Session $session, ClassroomRepository $classroomRepository, Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if (!$this->isSecretary()){
            return $this->redirectToRoute('app_home');
        }

        $selectedClassroom = $request->request->get('classroom-for-' . $session->getId());
        if (is_null($selectedClassroom) || !$this->isCsrfTokenValid('session-validate' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions_pending');
        }

        $session->setClassroom($classroomRepository->findOneBy(['id' => $selectedClassroom]));
        $session->setIsValid(true);
        $em->persist($session);
        $em->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
            ->to($session->getTutor()->getEmail())
            ->subject('Tutoru : Salle attribuée')
            ->htmlTemplate('email/session-validated.html.twig')
            ->context([
                // 'link' => $this->generateUrl('app_sessions_pending', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'session' => $session,
            ]);
        $mailer->send($email);

        $this->addFlash('success', "L'attribution de salle a bien été transmise !");
        return $this->redirectToRoute('app_sessions_pending', [
            'session' => $session,
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/refuse", name="app_session_refuse")
     */
    public function refuseSession(Session $session, ClassroomRepository $classroomRepository, Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if (!$this->isSecretary()){
            return $this->redirectToRoute('app_home');
        }

        if (!$this->isCsrfTokenValid('session-refuse' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions_pending');
        }

        $em->remove($session);
        $em->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
            ->to($session->getTutor()->getEmail())
            ->subject('Tutoru : Demande de salle refusée')
            ->htmlTemplate('email/session-refused.html.twig')
            ->context([
                // 'link' => $this->generateUrl('app_sessions_pending', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'session' => $session,
            ]);
        $mailer->send($email);

        $this->addFlash('success', "La demande a été refusée et le cours a bien été suprimmé !");
        return $this->redirectToRoute('app_sessions_pending', [
            'session' => $session,
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }
}
