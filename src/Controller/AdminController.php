<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    private function isAdmin(){
        if (!$this->getUser() || !in_array("ROLE_ADMIN", $this->getUser()->getRoles()) || $this->getUser()->getIsValid() != 2 || !$this->getUser()->isVerified()){
            return false;
        }
        return true;
    }

    /**
     * @Route("/users", name="app_users")
     */
    public function index(UserRepository $userRepository, SessionRepository $sessionRepository): Response
    {
        if (!$this->isAdmin()){
            return $this->redirectToRoute('app_home');
        }

        $users = $userRepository->findBy([
            'isVerified' => 1,
            'faculty' => $this->getUser()->getFaculty()    
        ],
        ['isValid' => 'ASC']);

        $adminCount = 0;
        foreach ($users as $user) {
            if (in_array("ROLE_ADMIN", $user->getRoles())){
                $adminCount++;
            }
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'adminCount' => $adminCount,
            'sessionRepository' => $sessionRepository
        ]);
    }
    /**
     * @Route("/user/{id<\d+>}/validate", name="app_user_validate")
     */
    public function validate(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()){
            return $this->redirectToRoute('app_home');
        }

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
        if (!$this->isAdmin()){
            return $this->redirectToRoute('app_home');
        }

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
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()){
            return $this->redirectToRoute('app_home');
        }

        if (!$this->isCsrfTokenValid('delete-user' . $user->getId(), $request->request->get('token'))) {
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
        if (!$this->isCsrfTokenValid('delete-subject' . $subject->getId(), $request->request->get('token'))) {
            $this->addFlash('danger', "Une erreur est survenue.");
            return $this->redirectToRoute('app_subject');
        }

        // verify if module used

        $em->remove($subject);
        $em->flush();

        $this->addFlash('success', 'Le module ' . $subject . ' a bien été suprimmé !');
        return $this->redirectToRoute("app_subject");
    }

    /**
     * @Route("/sessions/log", name="app_sessions_log", methods={"GET", "POST"})
     */
    public function sessionsLog(SessionRepository $sessionRepository, EntityManagerInterface $em, UserRepository $userRepository): Response
    {  
        return $this->render('admin/sessions-log.html.twig', [
            'sessions' => $sessionRepository->findFacultySessions(
                $this->getUser()->getFaculty(),
                ['isValid' => true], 
             ),
        ]);
    }
}
