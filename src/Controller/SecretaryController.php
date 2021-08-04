<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\ClassroomRepository;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
           'sessions' => getSessions($sessionRepository, $this->getUser(), null), // null -> same as false
           'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/validate", name="app_session_validate")
     */
    public function validateSession(Session $session, ClassroomRepository $classroomRepository): Response
    {
        return $this->render('secretary/manage.html.twig', [
            'session' => $session,
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/counter", name="app_session_counter")
     */
    public function counterSession(Session $session, ClassroomRepository $classroomRepository): Response
    {
        return $this->render('secretary/manage.html.twig', [
            'session' => $session,
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }
}
