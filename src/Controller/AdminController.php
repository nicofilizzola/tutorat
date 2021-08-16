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
use App\Repository\SessionRepository;
use App\Repository\SubjectRepository;
use App\Repository\SemesterRepository;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    // Must be included as the FIRST instruction in EVERY AdminController route
    private function isAdmin(){ 
        if (!$this->getUser() || !in_array("ROLE_ADMIN", $this->getUser()->getRoles()) || $this->getUser()->getIsValid() != 2 || !$this->getUser()->isVerified()){
            return false;
        }
        return true;
    }


    /**
     * @Route("/sessions/log", name="app_sessions_log", methods={"GET", "POST"})
     */
    public function sessionsLog(SessionRepository $sessionRepository, SemesterRepository $semesterRepository, Request $request): Response
    {  
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        $faculty = $this->getUser()->getFaculty();

        return $this->render('admin/sessions-log.html.twig', [
            'sessions' => $sessionRepository->findByFaculty(
                $faculty,
                [
                    'isValid' => true,
                    'semester' => !$request->query->get('semester') ? 
                        $semesterRepository->findCurrentFacultySemester($faculty) : 
                        $semesterRepository->findOneBy([
                            'id' => $request->query->get('semester'),
                            'faculty' => $faculty
                        ], ['id' => 'DESC'])
                ], 
             ),
             'semesters' => $semesterRepository->findBy(['faculty' => $faculty])
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
        require('Requires/Roles.php');
        foreach ($users as $user) {
            $userRoles = $user->getRoles();

            if (in_array($roles[3], $userRoles)){
                $adminCount++;
            }

            if (in_array($roles[1], $userRoles) && $userRoles[count($userRoles) - 1] == $roles[1]){
                array_push($tutorHours, $user->getTutorHours($sessionRepository));
            } else {
                array_push($tutorHours, null);
            }
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'adminCount' => $adminCount,
            'tutorHours' => $tutorHours
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
     * @Route("/user/{id<\d+>}/cancel", name="app_user_cancel", methods="POST")
     */
    public function cancel(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if ($this->isCsrfTokenValid('cancel-user' . $user->getId(), $request->request->get('token'))) {
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
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        if (!$this->isCsrfTokenValid('delete-user' . $user->getId(), $request->request->get('token')) ||
            $user == $this->getUser()
            // missing validation if adminCount == 1
        ) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_users');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash("success", "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a été suprimmé !");
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
     * @Route("/semester", name="app_semester", methods={"GET"})
     */
    public function semester(SemesterRepository $semesterRepository, Request $request): Response
    {
        if (!$this->isAdmin()){return $this->redirectToRoute('app_home');}

        $semester = new Semester;
        $form = $this->createForm(SemesterType::class, $semester);
        $form->handleRequest($request);

        return $this->render('admin/semester.html.twig', [
            'semesters' => $semesterRepository->findBy(
                ['faculty' => $this->getUser()->getFaculty()],
                ['id' => 'DESC']),
            'form' => $form->createView()
        ]);
    }
}
