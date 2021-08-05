<?php

namespace App\Controller;

use App\Entity\Session;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use App\Repository\SessionRepository;
use App\Repository\ClassroomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecretaryController extends AbstractController
{
    private function isSecretary(){
        if (!in_array("ROLE_SECRETARY", $this->getUser()->getRoles()) || $this->getUser()->getIsValid() !== 2 || !$this->getUser()->isVerified()){
            return false;
        }
        return true;
    }

    /**
     * @Route("/sessions/pending", name="app_sessions_pending", methods="GET")
     */
    public function sessionsPending(SessionRepository $sessionRepository, ClassroomRepository $classroomRepository): Response
    {
        if (!$this->getUser() || !$this->isSecretary()){
            return $this->redirectToRoute('app_login');
        }

        require_once("Requires/getSessions.php");

        return $this->render('secretary/sessions-pending.html.twig', [
           'sessions' => getSessions($sessionRepository, $this->getUser(), 0), // 0 -> same as false
           'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/validate", name="app_session_validate")
     */
    public function validateSession(EntityManagerInterface $em, Session $session, UserRepository $userRepository, ClassroomRepository $classroomRepository, MailerInterface $mailer): Response
    {
        if (!$this->getUser() || !$this->isSecretary()){
            return $this->redirectToRoute('app_login');
        }

        $session->setIsValid(true);
        $em->persist($session);
        $em->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
            ->to($userRepository->findOneBy(['id' => $session->getTutor()->getId()])->getEmail())
            ->subject('Demande de salle de cours validée')
            ->htmlTemplate('email/session-validated.html.twig')
            ->context([
                // 'link' => $this->generateUrl('app_sessions_pending', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'session' => $session
            ]);
        $mailer->send($email);

        return $this->redirectToRoute('app_sessions_pending', [
            'session' => $session,
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/counter", name="app_session_counter")
     */
    public function counterSession(Session $session, ClassroomRepository $classroomRepository, Request $request): Response
    {
        dd($request->request);

        return $this->render('secretary/manage.html.twig', [
            'session' => $session,
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }
}
