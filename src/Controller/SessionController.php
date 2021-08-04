<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\DateFilterType;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SessionController extends AbstractController
{
    private function sessionIsJoinable(Session $session){
        if (in_array($this->getUser()->getId(), $session->getStudents()->getValues())){
            $isJoinable = false;
        } else if ($this->getUser() === $session->getTutor()){
            $isJoinable = false;
        } else if ($this->getUser()->getFaculty() !== $session->getSubject()->getFaculty()){
            $isJoinable = false;
        }

        return !$isJoinable ? false : true;
    }

    private function isTutor(){
        if (!$this->getUser() || !$this->getUser()->isVerified() || $this->getUser()->getIsValid() !== 2 || !in_array("ROLE_TUTOR", $this->getUser()->getRoles())){
            return false;
        }
        return true;
    }

    /**
     * @Route("/session", name="app_session", methods="GET")
     */
    public function index(SessionRepository $sessionRepository, SubjectRepository $subjectRepository, UserRepository $userRepository): Response
    {
        require_once('Requires/getSessions.php');
        function getTutors($userRepository, $user){
            $facultyUsers = $userRepository->findBy(['faculty' => $user->getFaculty()]);
            $tutors = [];
            foreach ($facultyUsers as $user){
                if (in_array("ROLE_TUTOR", $user->getRoles()) && !in_array("ROLE_ADMIN", $user->getRoles())){
                    array_push($tutors, $user);
                }
            }
            return $tutors;
        }

        if (!$this->getUser() || !$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_login');
        }

        $facultyUsers = $userRepository->findBy(['faculty' => $this->getUser()->getFaculty()]);
        $tutors = [];
        foreach ($facultyUsers as $user){
            if (in_array("ROLE_TUTOR", $user->getRoles())/*/ && !in_array("ROLE_ADMIN", $user->getRoles())*/){
                array_push($tutors, $user);
            }
        }

        return $this->render('session/index.html.twig', [
           'sessions' => getSessions($sessionRepository, $this->getUser(), true),
           'subjects' => $subjectRepository->findBy(['faculty' => $this->getUser()->getFaculty()]),
           'tutors' => getTutors($userRepository, $this->getUser()),
        ]);
    }

    /**
     * @Route("/session/{id<\d+>}/join", name="app_session_join", methods={"POST"})
     */
    public function join(EntityManagerInterface $em, Session $session, Request $request): Response
    {
        if (!$this->sessionIsJoinable($session) || !$this->isCsrfTokenValid('join-session' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_session');
        }

        $session->addStudent($this->getUser());
        $em->persist($session);
        $em->flush();

        $this->addFlash('success', "Tu t'es inscrit au cours avec succès !");
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
            if ($session->getFaceToFace() == 1){
                if (is_null($session->getClassroom())){
                    $this->addFlash("danger", "Pas de salle de cours sélectionnée.");
                    return $this->redirectToRoute("app_session_create");
                }
                $session->setIsValid(false); // needs further secretary validation
            } 
            if ($session->getFaceToFace() == 2){
                if (is_null($session->getLink())){
                    $this->addFlash("danger", "Pas de lien de visio.");
                    return $this->redirectToRoute("app_session_create");
                }
                $session->setIsValid(true);
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

    /**
     * @Route("/session/{id<\d+>}/delete", name="app_session_delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Session $session): Response
    {
        if (!$this->isCsrfTokenValid('delete-session' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_session');
        }

        $em->remove($session);
        $em->flush();

        $this->addFlash('success', 'Le cours a bien été suprimmé !');
        return $this->redirectToRoute('app_session');
    }
}
