<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        
        $allSessions = $sessionRepository->findBy([], ['id' => 'ASC']);
        $facultySessions = [];
        foreach ($allSessions as $session) {
            if ($session->getSubject()->getFaculty() == $this->getUser()->getFaculty()){
                array_push($facultySessions, $session);
            }
        }
        $sessionsAfterToday = [];
        foreach ($facultySessions as $session) {
            if (date('Y-m-d h:i:s', strtotime('+1 hour')) < date('Y-m-d h:i:s', $session->getDateTime()->getTimestamp())){
                array_push($sessionsAfterToday, $session);
            }
        }

        return $this->render('session/index.html.twig', [
           'sessions' => $sessionsAfterToday,
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/join", name="app_session_join", methods={"POST"})
     */
    public function join(EntityManagerInterface $em, Session $session): Response
    {
        $oldParticipants = $session->getParticipants();
        if (!in_array($this->getUser()->getId(), $oldParticipants) && $this->getUser()->getFaculty() == $session->getSubject()->getFaculty()){
            $session->addStudent($this->getUser());
            // $session->setParticipants([[
            //         'student' => $this->getUser()->getId(),
            //         'present' => false,
            //     ],
            //     ...$oldParticipants
            // ]);
            $em->persist($session);
            $em->flush();

            $this->addFlash('success', "Tu t'es inscrit au cours avec succès !");
            return $this->redirectToRoute('app_session');
        }

        $this->addFlash('danger', 'Une erreur est survenue.');
        return $this->redirectToRoute('app_session');
    }

    /**
     * @Route("/session/create", name="app_session_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isTutor()){
            return $this->redirectToRoute('app_login');
        }

        $session = new Session;
        $form = $this->createForm(SessionType::class, $session, ['allow_extra_fields' => true]);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()){
            if (!isset($_POST['session']['faceToFace'])){
                $this->addFlash("danger", "Votre requête n'a pas pu être traitée.");
                return $this->redirectToRoute("app_session_create"); 
            }
            $ftf = $_POST['session']['faceToFace'];
            $session->setFaceToFace($ftf == 1 ? 1 : 2);
            if ($session->getFaceToFace() == 1 && is_null($session->getClassroom())){
                $this->addFlash("danger", "Pas de salle de cours sélectionnée.");
                return $this->redirectToRoute("app_session_create");
            }
            if ($session->getFaceToFace() == 2 && is_null($session->getLink())){
                $this->addFlash("danger", "Pas de lien de visio.");
                return $this->redirectToRoute("app_session_create");
            }

            $session->setTutor($this->getUser());
            $session->updateTimestamp();
            $em->persist($session);
            $em->flush();

            $this->addFlash('success', 'Ton cours de ' . $session->getSubject() . ' a bien été proposé !');
            return $this->redirectToRoute("app_session_create");
        }

        return $this->render('session/create.html.twig', [
            'form' => $formView
        ]);
    }


}
