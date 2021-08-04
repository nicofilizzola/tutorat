<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\ClassroomRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecretaryController extends AbstractController
{
    /**
     * @Route("/session/pending", name="app_sessions_pending")
     */
    public function index(): Response
    {
        return $this->render('secretary/index.html.twig', [
          
        ]);
    }
    /**
     * @Route("/session/{id<\d+>}/manage", name="app_session_manage")
     */
    public function manageClassroom(Session $session, ClassroomRepository $classroomRepository): Response
    {
        return $this->render('secretary/manage.html.twig', [
            'session' => $session,
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()])
        ]);
    }
}
