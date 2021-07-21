<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/users", name="app_users")
     */
    public function index(UserRepository $userRepository): Response
    {
        $validatedUsers = $userRepository->findBy([
            'isValid' => 1,
            'isVerified' => 1    
        ],
        ['id' => 'ASC']);

        $users = [];
        foreach ($validatedUsers as $user){
            if (in_array("ROLE_TUTOR", $user->getRoles())){
                array_push($users, $user);
            }
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }
}
