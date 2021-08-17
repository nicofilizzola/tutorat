<?php

namespace App\Controller;

use App\Controller\Traits\adminValidationEmail;
use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Traits\getRoles;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultController extends AbstractController
{
    use adminValidationEmail;
    use getRoles;

    /**
     * @Route("/", name="app_home")
     */
    public function index(SessionRepository $sessionRepository, UserRepository $userRepository): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('default/index.html.twig', [
            'sessions' => $sessionRepository->findByStudent($this->getUser()),
            'users' => $userRepository->findBy([
                'isValid' => 1, 
                'isVerified' => true
            ]),
            'roles' => $this->getRoles()
        ]);
    }

    /**
     * @Route("/become-tutor", name="app_become-tutor", methods={"GET", "POST"})
     */
    public function becomeTutor(Request $request, EntityManagerInterface $em, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        require_once("Requires/getAdminsMails.php");

        if (!$this->getUser() || in_array($this->getRoles()[1], $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_home');
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('become-tutor', $submittedToken)) {
            $this->getUser()->setRoles([$this->getRoles()[0], $this->getRoles()[1]]);
            $this->getUser()->setIsValid(1);

            $em->persist($this->getUser());
            $em->flush();

            // reauth user
            $token = new UsernamePasswordToken($this->getUser(), null, 'main', $this->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            $this->sendAdminsEmailForPendingUser($mailer, $userRepository);

            $this->addFlash('Success', 'Votre demande a bien été envoyée ! Vous aurez une réponse dans environ une semaine.');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('default/become-tutor.html.twig', [
            
        ]);
    }
}
