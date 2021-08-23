<?php

namespace App\Controller;

use App\Controller\Traits\emailData;
use App\Entity\User;
use App\Entity\Faculty;
use App\Traits\getRoles;
use App\Form\FacultyType;
use App\Form\SuperAdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuperAdminController extends AbstractController
{
    use emailData;
    use getRoles;

    private function isSuperAdmin(){
        if (!$this->getUser() || !in_array($this->getRoles()[4], $this->getUser()->getRoles()) || !$this->getUser()->isVerified() || $this->getUser()->getIsValid() !== 2){
            return false;
        }
        return true;
    }

    /**
     * @Route("/start", name="app_start")
     */
    public function start(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    { 
        if (!empty($userRepository->findAll())){ return $this->redirectToRoute('app_home'); }

        $user = new User;
        $form = $this->createForm(SuperAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $request->request->get('super_admin');
            $user->setRoles($this->setRoles(4));
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['password']['first']
                )
            );
            $user->updateTimestamp();

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Super admin account created');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('super_admin/start.html.twig', [
            'form' => $form->createView()
        ]);  
    }

    /**
     * @Route("/superadmin", name="app_superadmin")
     */
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, UserRepository $userRepository): Response
    {

        if (!$this->isSuperAdmin()) { return $this->redirectToRoute('app_home'); }

        $faculty = new Faculty;
        $form = $this->createForm(FacultyType::class, $faculty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $request->request->get('faculty');
            $admin = new User;
            $secretary = new User;

            $em->persist($faculty);
            $em->flush();

            // setup admin user
            $admin->setFirstName($data['adminFirstName']);
            $admin->setLastName($data['adminLastName']);
            $admin->setEmail($data['adminEmail']);
            $admin->setFaculty($faculty);
            $admin->setRoles($this->setRoles(3));
            $admin->setYear(4);
            $admin->updateTimestamp();
            $admin->setIsValid(2);
            $admin->setIsVerified(true);
            $adminPlainPwd = $data['adminPassword'];
            $admin->setPassword(
                $passwordHasher->hashPassword(
                    $admin,
                    $adminPlainPwd
                )
            );

            // setup secretary user
            $secretary->setFirstName('Secrétaire');
            $secretary->setLastName($faculty->getShort());
            $secretary->setEmail($data['secretaryEmail']);
            $secretary->setFaculty($faculty);
            $secretary->setRoles($this->setRoles(2));
            $secretary->setYear(4);
            $secretary->updateTimestamp();
            $secretary->setIsValid(2);
            $secretary->setIsVerified(true);
            $secretaryPlainPwd = $data['secretaryPassword'];
            $secretary->setPassword(
                $passwordHasher->hashPassword(
                    $secretary,
                    $secretaryPlainPwd
                )
            );

            $em->persist($admin);
            $em->persist($secretary);
            $em->flush();

            $this->sendEmail($mailer, [$admin->getEmail()], 'Création de département', 'email/admin-new-faculty.html.twig', [
                'faculty' => $faculty->getName(),
                'mail' => $admin->getEmail(),
                'password' => $adminPlainPwd,
                'adminCode' => $faculty->getCode()
            ]);
            $this->sendEmail($mailer, [$secretary->getEmail()], 'Création de département', 'email/secretary-new-faculty.html.twig', [
                'faculty' => $faculty->getName(),
                'mail' => $secretary->getEmail(),
                'password' => $secretaryPlainPwd,
            ]);

            $this->addFlash('success', 'Faculty added');
            return $this->redirectToRoute('app_superadmin');
        }

        return $this->render('super_admin/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
