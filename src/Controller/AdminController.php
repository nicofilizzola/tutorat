<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Subject;
use App\Entity\Semester;
use App\Entity\Classroom;
use App\Form\SubjectType;
use App\Form\SemesterType;
use App\Form\ClassroomType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\SessionRepository;
use App\Repository\SubjectRepository;
use App\Repository\SemesterRepository;
use App\Repository\ClassroomRepository;
use App\Traits\getRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class AdminController extends AbstractController
{
    use getRoles;

    // Must be included as the FIRST instruction in EVERY AdminController route
    private function isAdmin(){ 
        if (!$this->getUser() || !in_array($this->getRoles()[3], $this->getUser()->getRoles()) || $this->getUser()->getIsValid() != 2 || !$this->getUser()->isVerified()){
            return false;
        }
        return true;
    }


    /**
     * @Route("/sessions/log", name="app_sessions_log", methods={"GET", "POST"})
     */
    public function sessionsLog(
        Request $request, 
        SessionRepository $sessionRepository, 
        SemesterRepository $semesterRepository, 
        UserRepository $userRepository
    ): Response
    {  
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        $faculty = $this->getUser()->getFaculty();
        $requestedSemester = $request->request->get('semester');
        $currentSemester = is_null($requestedSemester) ? 
            $semesterRepository->findCurrentFacultySemester($faculty) : 
            $semesterRepository->findOneBy([
                'id' => $requestedSemester,
                'faculty' => $faculty
            ]);
        $requestedTutor = $request->request->get('tutor') ?? "all";
        $currentTutor = $requestedTutor == "all" ? 
            "all" : 
            $userRepository->findOneBy([
                'id' => $requestedTutor,
                'faculty' => $faculty
            ]
        );
        $sessionCriteria = [
            'isValid' => true,
            'semester' => $currentSemester,
        ];
        if ($currentTutor !== "all"){
            $sessionCriteria['tutor'] = $currentTutor;
        }

        return $this->render('admin/sessions-log.html.twig', [
            'sessions' => $sessionRepository->findByFaculty(
                $faculty,
                $sessionCriteria, 
                false
            ),
            'semesters' => $semesterRepository->findBy(
                ['faculty' => $faculty],
                ['id' => 'DESC']
            ),
            'currentSemester' => $currentSemester,
            'tutors' => $userRepository->findFacultyTutors($this->getUser()->getFaculty()),
            'currentTutor' => $currentTutor
        ]);
    }


    /**
     * @Route("/users", name="app_users")
     */
    public function index(UserRepository $userRepository, SessionRepository $sessionRepository): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        $users = $userRepository->findBy([
            'isVerified' => 1,
            'faculty' => $this->getUser()->getFaculty()    
        ],
        ['isValid' => 'ASC']);

        $adminCount = 0;
        $tutorHours = [];
        foreach ($users as $user) {
            $userRoles = $user->getRoles();

            if (in_array($this->getRoles()[3], $userRoles)){
                $adminCount++;
            }

            if (in_array($this->getRoles()[1], $userRoles) && $userRoles[count($userRoles) - 1] == $this->getRoles()[1]){
                array_push($tutorHours, $user->getTutorHours($sessionRepository));
            } else {
                array_push($tutorHours, null);
            }
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'adminCount' => $adminCount,
            'tutorHours' => $tutorHours,
            'roles' => $this->getRoles()
        ]);
    }
    /**
     * @Route("/user/{id<\d+>}/validate", name="app_user_validate")
     */
    public function validate(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if (!$this->isCsrfTokenValid('validate-user' . $user->getId(), $request->request->get('token'))) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_users');
        }

        $user->setIsValid(2);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash("success", "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a été validé !");
        return $this->redirectToRoute('app_users');
    }
    /**
     * @Route("/user/{id<\d+>}/refuse", name="app_user_refuse", methods="POST")
     */
    public function refuse(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if ($this->isCsrfTokenValid('refuse-user' . $user->getId(), $request->request->get('token'))) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_users');
        }

        $user->setIsValid(3);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash("success", "La demande de l'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a été refusée !");
        return $this->redirectToRoute('app_users');
    }
    /**
     * @Route("/user/{id<\d+>}/delete", name="app_user_delete", methods="POST")
     */
    public function delete(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if (
            !$this->isCsrfTokenValid(
                'delete-user' . $user->getId(), 
                $request->request->get('token')
            ) ||
            $user == $this->getUser()
            // missing validation if adminCount == 1
        ) {
            $this->addFlash(
                'danger', 
                "Une erreur est survenue."
            );
            return $this->redirectToRoute('app_users');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash(
            "success", 
            "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a été suprimmé !"
        );
        return $this->redirectToRoute('app_users');
    }
    /**
     * @Route("/user/{id<\d+>}/demote", name="app_user_demote", methods="POST")
     */
    public function demote(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if (!$this->isCsrfTokenValid(
            'demote-user' . $user->getId(), 
            $request->request->get('token')
        )) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_users');
        }

        $user->setRoles([$this->getRoles()[0]]);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash(
            "success", 
            "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a été révoqué de ses droits de tuteur !"
        );
        return $this->redirectToRoute('app_users');
    }


    /**
     * @Route("/subject", name="app_subject", methods={"GET", "POST"})
     */
    public function subject(SubjectRepository $subjectRepository, Request $request, EntityManagerInterface $em): Response
    { 
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}
        
        $subject = new Subject;
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()){
            $subject->setFaculty($this->getUser()->getFaculty());

            $em->persist($subject);
            $em->flush();

            $this->addFlash("success", "La matière " . $subject->getTitle() . " a bien été ajoutée !");
            return $this->redirectToRoute('app_subject');
        }

        return $this->render('admin/subjects.html.twig', [
            'subjects' => $subjectRepository->findBy(['faculty' => $this->getUser()->getFaculty()]),
            'form' => $formView
        ]);
    }
    /**
     * @Route("/subject/{id<\d+>}/delete", name="app_subject_delete", methods={"POST"})
     */
    public function subjectDelete(Subject $subject, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if (!$this->isCsrfTokenValid('delete-subject' . $subject->getId(), $request->request->get('token'))) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_subject');
        }

        $em->remove($subject);
        $em->flush();

        $this->addFlash('success', 'Le module ' . $subject . ' a bien été suprimmé !');
        return $this->redirectToRoute("app_subject");
    }

    
    /**
     * @Route("/classroom", name="app_classroom", methods={"GET", "POST"})
     */
    public function classroom(ClassroomRepository $classroomRepository, Request $request, EntityManagerInterface $em): Response
    {  
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        $classroom = new Classroom;
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()){
            $classroom->setFaculty($this->getUser()->getFaculty());

            $em->persist($classroom);
            $em->flush();

            $this->addFlash("success", "La salle de cours " . $classroom->getName() . " a bien été ajoutée !");
            return $this->redirectToRoute('app_subject');
        }

        return $this->render('admin/classrooms.html.twig', [
            'classrooms' => $classroomRepository->findBy(['faculty' => $this->getUser()->getFaculty()]),
            'form' => $formView
        ]);
    }
    /**
     * @Route("/classroom/{id<\d+>}/delete", name="app_classroom_delete", methods={"POST"})
     */
    public function classroomDelete(Classroom $classroom, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if (!$this->isCsrfTokenValid('delete-classroom' . $classroom->getId(), $request->request->get('token'))) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_classroom');
        }

        $em->remove($classroom);
        $em->flush();

        $this->addFlash('success', 'La salle de cours ' . $classroom->getName() . ' a bien été suprimmé !');
        return $this->redirectToRoute("app_classroom");
    }


    /**
     * @Route("/semester", name="app_semester", methods={"GET", "POST"})
     */
    public function semester(
        Request $request, 
        EntityManagerInterface $em, 
        SemesterRepository $semesterRepository,
        SessionRepository $sessionRepository
        ): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        $semesterLimit = 5;
        $semester = new Semester;
        $faculty = $this->getUser()->getFaculty();
        $form = $this->createForm(SemesterType::class, $semester);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if (intval($semester->getStartYear()) + 1 !== intval($semester->getEndYear())){
                $this->addFlash('danger', "Une erreur est survenue...");
                return $this->redirectToRoute('app_semester');
            }
            if ($semesterRepository->findOneBy([
                'startYear' => $semester->getStartYear(),
                'endYear' => $semester->getEndYear(),
                'yearOrder' => $semester->getYearOrder()
            ])) {
                $this->addFlash('danger', "Erreur : le semestre que vous avez essayé de créer (" . $semester .") existe déjà...");
                return $this->redirectToRoute('app_semester');
            }

            function updatePendingSessions($sessions, $semester, $em){
                if (!empty($sessions)){
                    foreach($sessions as $session){
                        $session->setSemester($semester);
                        $em->persist($session);
                    }
                }
            }
            function deleteExcessSemester($semesters, $semesterLimit, $semester, $faculty, $em){
                $deleteMessage = null;
                if (count($semesters) >= $semesterLimit){
                    $em->remove($semesters[count($semesters) - 1]);
                    $deleteMessage =  " Également, le semestre " . $semesters[count($semesters) - 1] . " a été supprimé";
                }
                $semester->setFaculty($faculty);
                $em->persist($semester);

                return $deleteMessage;
            }
            $facultySemesters = $semesterRepository->findBy(
                ['faculty' => $faculty],
                ['id' => 'DESC']
            );
            $facultySemesterSessions = $sessionRepository->findByFacultyAfterToday(
                $faculty, 
                [
                    'isValid' => true,
                    'semester' => $semester
                ],
                null,
                false
            );

            $deleteMessage = deleteExcessSemester($facultySemesters, $semesterLimit, $semester, $faculty, $em);
            updatePendingSessions($facultySemesterSessions, $semester, $em);

            $em->flush();

            $this->addFlash('success', "Le nouveau semestre a bien été ajouté et tout a été remis à 0 !" . $deleteMessage ?? "");
            return $this->redirectToRoute('app_semester');
        }

        return $this->render('admin/semester.html.twig', [
            'semesters' => $semesterRepository->findBy(
                ['faculty' => $faculty],
                ['id' => 'DESC']),
            'form' => $form->createView(),
            'semesterLimit' => $semesterLimit
        ]);
    }
    /**
     * @Route("/semester/{id<\d+>}/delete", name="app_semester_delete", methods={"POST"})
     */
    public function semesterDelete(
        Request $request, 
        Semester $semester, 
        EntityManagerInterface $em
        ): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if (!$this->isCsrfTokenValid('delete-semester' . $semester->getId(), $request->request->get('token'))) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_semester');
        }

        $em->remove($semester);
        $em->flush();

        $this->addFlash('success', 'Le semestre ' . $semester . ' et toutes les informations relatives à celui-ci ont été suprimmées !');
        return $this->redirectToRoute("app_semester");
    }
}
