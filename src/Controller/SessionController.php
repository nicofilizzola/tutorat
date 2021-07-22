<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    private function isTutor(){
        if (!$this->getUser() || !$this->getUser()->isVerified() || $this->getUser()->getIsValid() !== 2 || !in_array("ROLE_TUTOR", $this->getUser()->getRoles())){
            return false;
        }
        return true;
    }

    /**
     * @Route("/session", name="app_session", methods="GET")
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        if (!$this->getUser() || !$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_login');
        }
        
        $allSessions = $sessionRepository->findBy([], ['id' => 'DESC']);
        $facultySessions = [];
        foreach ($allSessions as $session) {
            if ($session->getSubject()->getFaculty() == $this->getUser->getFaculty()){
                array_push($facultySessions, $session);
            }
        }

        return $this->render('session/index.html.twig', [
           'sessions' => $facultySessions
        ]);
    }

    /**
     * @Route("/session/create", name="app_session_create", methods={"GET", "POST"})
     */
    public function create(Request $request): Response
    {
        if (!$this->isTutor()){
            return $this->redirectToRoute('app_login');
        }

        $session = new Session;
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);
        $formView = $form->createView();

        return $this->render('session/create.html.twig', [
            'form' => $formView
        ]);
    }
}
