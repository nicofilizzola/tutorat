<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Form\SessionType;
use App\Form\DateFilterType;
use App\Repository\SemesterRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\SessionRepository;
use App\Repository\SubjectRepository;
use App\Traits\getRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Mailer;

class SessionController extends AbstractController
{
    use getRoles;

    private function sessionIsJoinable(Session $session){
        $isJoinable = true;
        if (in_array($this->getUser(), $session->getStudents()->getValues())){
            $isJoinable = false;
        } else if ($this->getUser() === $session->getTutor()){
            $isJoinable = false;
        } else if ($this->getUser()->getFaculty() !== $session->getSubject()->getFaculty()){
            $isJoinable = false;
        } else if (count($session->getStudents()) == $session->getStudentLimit()){
            $isJoinable = false;
        }

        return !$isJoinable ? false : true;
    }
    private function isTutor(){
        if (!$this->getUser() || !$this->getUser()->isVerified() || $this->getUser()->getIsValid() !== 2 || !in_array($this->getRoles()[1], $this->getUser()->getRoles())){
            return false;
        }
        return true;
    }


    /**
     * @Route("/sessions", name="app_sessions", methods="GET")
     */
    public function index(SessionRepository $sessionRepository, SubjectRepository $subjectRepository, UserRepository $userRepository, SemesterRepository $semesterRepository): Response
    {
        if (!$this->getUser() || !$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_login');
        }

        $faculty = $this->getUser()->getFaculty();

        return $this->render('sessions/index.html.twig', [
           'sessions' => $sessionRepository->findByFacultyAfterToday(
                $faculty,
               [
                   'isValid' => true,
                   'semester' => $semesterRepository->findCurrentFacultySemester($faculty)
                ], 
                null,
                false
            ),
           'subjects' => $subjectRepository->findBy(['faculty' => $faculty]),
           'tutors' => $userRepository->findFacultyTutors($faculty),
           'tutorSessions' => $this->isTutor() ? 
                $sessionRepository->findByFaculty($faculty, [
                    'tutor' => $this->getUser()
                ]) : 
                null
        ]);
    }
    /**
     * @Route("/sessions/{id<\d+>}", name="app_sessions_view", methods={"GET"})
     */
    public function view(SessionRepository $sessionRepository, Session $session): Response
    {
        if (!$this->getUser() || $this->getUser()->getFaculty() !== $session->getSubject()->getFaculty()){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('sessions/view.html.twig', [
           'sessions' => $sessionRepository->findThreeBySessionSubject($session),
           'currentSession' => $session,
           'roles' => $this->getRoles()
        ]);
    }
    /**
     * @Route("/ownSessions", name="app_ownSessions", methods={"GET"})
     */
    public function userSessions(SessionRepository $sessionRepository, SemesterRepository $semesterRepository): Response
    {
        if (!$this->getUser() || !$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }

        $faculty = $this->getUser()->getFaculty();

        return $this->render('sessions/ownSessions.html.twig', [
            'tutorSessions' => $this->isTutor() ? 
                $sessionRepository->findBy([
                    'tutor' => $this->getUser(),
                    'semester' => $semesterRepository->findCurrentFacultySemester($faculty),
                ]) : null,
            'joinedSessions' => $sessionRepository->findByJoinedSessions($semesterRepository, $this->getUser()),
            'user' => $this->getUser(),
            'roles' => $this->getRoles()
        ]);
    }


    /**
     * @Route("/sessions/{id<\d+>}/join", name="app_sessions_join", methods={"POST"})
     */
    public function join(EntityManagerInterface $em, Session $session, Request $request): Response
    {
        if (!$this->sessionIsJoinable($session) || !$this->isCsrfTokenValid('join-session' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions_view', ['id' => $session->getId()]);
        }

        $session->addStudent($this->getUser());
        $em->persist($session);
        $em->flush();

        $this->addFlash('success', "Tu t'es inscrit au cours avec succès !");
        return $this->redirectToRoute('app_sessions_view', ['id' => $session->getId()]);
    }
    /**
     * @Route("/sessions/{id<\d+>}/leave", name="app_sessions_leave", methods={"POST"})
     */
    public function leave(EntityManagerInterface $em, Session $session, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('leave-session' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions_view', ['id' => $session->getId()]);
        }

        $session->removeStudent($this->getUser());
        $em->persist($session);
        $em->flush();

        $this->addFlash('success', "Tu t'es bien désinscrit du cours !");
        return $this->redirectToRoute('app_sessions_view', ['id' => $session->getId()]);
    }


    // TUTOR ONLY

    /**
     * @Route("/sessions/create", name="app_sessions_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, MailerInterface $mailer, UserRepository $userRepository, SemesterRepository $semesterRepository): Response
    {
        if (!$this->isTutor()){
            return $this->redirectToRoute('app_login');
        }

        $session = new Session;
        $session->setTutor($this->getUser());

        $form = $this->createForm(SessionType::class, $session, ['allow_extra_fields' => true]);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()){
            if (!isset($_POST['session']['faceToFace']) || !isset($_POST['session']['timeFormat'])){
                $this->addFlash("danger", "Votre requête n'a pas pu être traitée.");
                return $this->redirectToRoute("app_sessions_create"); 
            }

            $ftf = $_POST['session']['faceToFace'];
            $session->setFaceToFace($ftf == 1 ? 1 : 2);
            if ($session->getFaceToFace() == 1){
                $session->setIsValid(false); // needs further secretary validation

                $secretaryMail = $userRepository->findFacultySecretaryEmail($this->getUser()->getFaculty());
                $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
                    ->to($secretaryMail)
                    ->subject('Demande de salle de cours')
                    ->htmlTemplate('email/new-pending-session.html.twig')
                    ->context([
                        'link' => $this->generateUrl('app_sessions_pending', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'session' => $session
                    ]);
                $mailer->send($email);
            } 

            if ($session->getFaceToFace() == 2){
                if (is_null($session->getLink())){
                    $this->addFlash("danger", "Pas de lien de visio.");
                    return $this->redirectToRoute("app_sessions_create");
                }
                $session->setIsValid(true);
            } 

            $session->setTimeFormat($_POST['session']['timeFormat']);
            $session->setSemester($semesterRepository->findCurrentFacultySemester($session->getSubject()->getFaculty()));
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
    public function delete(
        Request $request, 
        EntityManagerInterface $em, 
        MailerInterface $mailer, 
        Session $session,
        UserRepository $userRepository
    ): Response
    {
        if (!$this->isCsrfTokenValid('delete-session' . $session->getId(), $request->request->get('token'))){
            $this->addFlash('danger', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_sessions');
        }

        function sendEmailToJoinedStudents(
            UserRepository $userRepository, 
            Session $session, 
            MailerInterface $mailer
        ){
            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
                ->to(...$userRepository->findSessionJoinedStudentEmails($session))
                ->subject('Cours de tutorat annulé')
                ->htmlTemplate('email/deleted-session.html.twig')
                ->context([
                    'session' => $session,
                ]);
            $mailer->send($email);
        }

        sendEmailToJoinedStudents(
            $userRepository, 
            $session, 
            $mailer
        );
        
        $em->remove($session);
        $em->flush();

        $this->addFlash('success', 'Le cours a bien été suprimmé !');
        return $this->redirectToRoute('app_sessions');
    }
    /**
     * @Route("/sessions/{id<\d+>}/participants", name="app_sessions_participants", methods={"GET", "POST"})
     */
    public function manageParticipants(
        Session $session, 
        Request $request, 
        EntityManagerInterface $em
    ): Response
    {  
        if (
            !$this->isTutor() && $session->getTutor() !== $this->getUser() || 
            !empty($session->getParticipants())
        ){
            return $this->redirectToRoute('app_home');
        }

        // if (empty($session->getStudents())){
        //     $this->addFlash(
        //         "danger", 
        //         "L'appel ne peut pas être fait car aucun étudiant ne s'est inscrit à ce cours"
        //     );
        //     return $this->redirectToRoute(
        //         'app_sessions_view', 
        //         ['id' => $session->getId()]
        //     );
        // }

        // if (strtotime("today") < $session->getDateTime()->getTimestamp()){
        //     $this->addFlash(
        //         "danger", 
        //         "L'appel ne peut pas être fait car la date du cours n'a pas encore été atteinte"
        //     );
        //     return $this->redirectToRoute(
        //         'app_sessions_view', 
        //         ['id' => $session->getId()]
        //     );
        // }        

        if ($request->isMethod('post')){
            $participants = [];
            foreach($session->getStudents() as $student){
                array_push($participants, [
                    'studentId' => $student->getId(),
                    'present' => $request->request->get($student->getId()) ? 
                        true : 
                        false
                ]);
            }
            $session->setParticipants($participants);

            $em->persist($session);
            $em->flush();

            $this->addFlash(
                'success', 
                "La liste d'appel a été transmise et le cours a bien été validé !"
            );
            return $this->redirectToRoute('app_sessions');

        }

        return $this->render(
            'sessions/participants.html.twig', 
            [
                'session' => $session
            ]
        );
    }
}
