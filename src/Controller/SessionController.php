<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Form\SessionType;
use App\Form\DateFilterType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\SessionRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/sessions", name="app_sessions", methods="GET")
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

        return $this->render('sessions/index.html.twig', [
           'sessions' => getSessions(
               $sessionRepository, 
               ['isValid' => true], 
               $this->getUser()
            ),
           'subjects' => $subjectRepository->findBy(['faculty' => $this->getUser()->getFaculty()]),
           'tutors' => getTutors($userRepository, $this->getUser()),
        ]);
    }

    /**
     * @Route("/sessions/{id<\d+>}/join", name="app_sessions_join", methods={"POST"})
     */
    public function join(EntityManagerInterface $em, Session $session, Request $request): Response
    {
        if (!$this->sessionIsJoinable($session) || !$this->isCsrfTokenValid('join-session' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions_view', ['session' => $session]);
        }

        $session->addStudent($this->getUser());
        $em->persist($session);
        $em->flush();

        $this->addFlash('success', "Tu t'es inscrit au cours avec succès !");
        return $this->redirectToRoute('app_sessions_view', ['session' => $session]);
    }

    /**
     * @Route("/sessions/create", name="app_sessions_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        function getSecretaryMail($users){
            foreach ($users as $user){
                if (in_array("ROLE_SECRETARY", $user->getRoles()) && !in_array("ROLE_ADMIN", $user->getRoles())){
                    return $user->getEmail();
                }
            }
        }  

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
                return $this->redirectToRoute("app_sessions_create"); 
            }
            $ftf = $_POST['session']['faceToFace'];
            $session->setFaceToFace($ftf == 1 ? 1 : 2);
            if ($session->getFaceToFace() == 1){
                if (is_null($session->getClassroom())){
                    $this->addFlash("danger", "Pas de salle de cours sélectionnée.");
                    return $this->redirectToRoute("app_sessions_create");
                } 

                $session->setIsValid(false); // needs further secretary validation
                $secretaryMail = getSecretaryMail($userRepository->findBy(['faculty' => $this->getUser()->getFaculty()]));
                $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
                    ->to($secretaryMail)
                    ->subject('Demande de salle de cours')
                    ->htmlTemplate('email/new-pending-session.html.twig')
                    ->context(['link' => $this->generateUrl('app_sessions_pending', [], UrlGeneratorInterface::ABSOLUTE_URL)]);
                $mailer->send($email);
            } 

            if ($session->getFaceToFace() == 2){
                if (is_null($session->getLink())){
                    $this->addFlash("danger", "Pas de lien de visio.");
                    return $this->redirectToRoute("app_sessions_create");
                }
                $session->setIsValid(true);
            } 

            $session->setTutor($this->getUser());
            $session->updateTimestamp();
            $em->persist($session);
            $em->flush();

            $flashMessage = $session->getFaceToFace() == 1 ? " La demande de salle a ainsi été envoyée au secrétariat (Tu recevras une réponse par mail)." : "";
            $this->addFlash('success', 'Ton cours de ' . $session->getSubject() . ' a bien été proposé !' . $flashMessage);
            return $this->redirectToRoute("app_sessions");
        }

        return $this->render('sessions/create.html.twig', [
            'form' => $formView
        ]);
    }

    /**
     * @Route("/sessions/{id<\d+>}/delete", name="app_sessions_delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Session $session): Response
    {
        if (!$this->isCsrfTokenValid('delete-session' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions');
        }

        $em->remove($session);
        $em->flush();

        $this->addFlash('success', 'Le cours a bien été suprimmé !');
        return $this->redirectToRoute('app_sessions');
    }

    /**
     * @Route("/sessions/{id<\d+>}", name="app_sessions_view", methods={"GET"})
     */
    public function view(SessionRepository $sessionRepository, Session $session): Response
    {
        require_once('Requires/getSessions.php');

        if (!$this->getUser() || $this->getUser()->getFaculty() !== $session->getSubject()->getFaculty()){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions');
        }

        $allSessions = getSessions(
            $sessionRepository, [
                 'isValid' => true,
                 'subject' => $session->getSubject()
             ], 
             $this->getUser(), 
             $session
        );
        if (!is_null($allSessions) && count($allSessions) >= 3){
            $sessions = [];
            for ($i = 0; $i < 2; $i++){
                array_push($sessions, $allSessions[$i]);
            }
        } else {
            $sessions = $allSessions;
        }

        return $this->render('sessions/view.html.twig', [
           'sessions' => $sessions,
           'currentSession' => $session
        ]);
    }
}
