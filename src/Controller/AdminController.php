<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->isAdmin()){
            return $this->redirectToRoute('app_home');
        }

        $verifiedUsers = $userRepository->findBy([
            'isValid' => 1,
            'isVerified' => 1    
        ],
        ['id' => 'ASC']);

        $users = [];
        foreach ($verifiedUsers as $user){
            if (in_array("ROLE_TUTOR", $user->getRoles())){
                array_push($users, $user);
            }
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users
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

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('validate-user' . $user->getId(), $submittedToken)) {
            $user->setIsValid(2);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a été validé !");
            return $this->redirectToRoute('app_users');
        }

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

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('cancel-user' . $user->getId(), $submittedToken)) {
            $user->setIsValid(3);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "La demande de l'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " a été refusée !");
            return $this->redirectToRoute('app_users');
        }

        return $this->redirectToRoute('app_users');
    }
}
