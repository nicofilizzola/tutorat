<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Faculty;
use App\Traits\getRoles;
use App\Form\FacultyType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuperAdminController extends AbstractController
{
    use getRoles;

    /**
     * @Route("/superadmin", name="app_superadmin")
     */
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        $faculty = new Faculty;
        $form = $this->createForm(FacultyType::class, $faculty);
        $form->handleRequest($request);

        if ($request->isMethod('post')){
            $data = $request->request->get('faculty');
            $admin = new User;
            $secretary = new User;

            $em->persist($faculty);
            $em->flush();

            $adminRoles = [];
            $secretaryRoles = [];
            for ($i = 0; $i <= 3; $i++){
                $adminRoles[] = $this->getRoles()[$i];
                if ($i < 3){
                    $secretaryRoles[] = $this->getRoles()[$i];
                }
            }

            // setup admin user
            $admin->setFirstName($data['adminFirstName']);
            $admin->setLastName($data['adminLastName']);
            $admin->setEmail($data['adminEmail']);
            $admin->setFaculty($faculty);
            $admin->setRoles($adminRoles);
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
            $secretary->setRoles($secretaryRoles);
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

            // admin mail
            $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
            ->to($admin->getEmail(), $secretary->getEmail())
            ->subject('Tutoru : Création de département')
            ->htmlTemplate('email/admin-new-faculty.html.twig')
            ->context([
                'faculty' => $faculty->getName(),
                'mail' => $admin->getEmail(),
                'password' => $adminPlainPwd,
                'adminCode' => $faculty->getCode()
            ]);
            $mailer->send($email);

            // secretary mail
            $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
            ->to($admin->getEmail(), $secretary->getEmail())
            ->subject('Tutoru : Création de département')
            ->htmlTemplate('email/secretary-new-faculty.html.twig')
            ->context([
                'faculty' => $faculty->getName(),
                'mail' => $secretary->getEmail(),
                'password' => $secretaryPlainPwd,
            ]);
            $mailer->send($email);

            $this->addFlash('success', 'Faculty added');
            return $this->redirectToRoute('app_superadmin');
        }

        return $this->render('super_admin/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
