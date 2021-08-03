<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
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
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
        ]);
    }

    /**
     * @Route("/become-tutor", name="app_become-tutor", methods={"GET", "POST"})
     */
    public function becomeTutor(Request $request, EntityManagerInterface $em, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        function getAdminMails($users){
            $adminEmails = [];
            foreach ($users as $user){
                if (in_array("ROLE_ADMIN", $user->getRoles())){
                    array_push($adminEmails, $user->getEmail());
                }
            }
            return $adminEmails;   
        }

        if (!$this->getUser() || in_array("ROLE_TUTOR", $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_home');
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('become-tutor', $submittedToken)) {
            $this->getUser()->setRoles(["ROLE_STUDENT", "ROLE_TUTOR"]);
            $this->getUser()->setIsValid(1);

            $em->persist($this->getUser());
            $em->flush();

            // reauth user
            $token = new UsernamePasswordToken($this->getUser(), null, 'main', $this->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            $toAddresses = getAdminMails($userRepository->findBy(['faculty' => $this->getUser()->getFaculty()]));
            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
                ->to(...$toAddresses)
                ->subject('Nouvelle demande de tuteur')
                ->htmlTemplate('email/become-tutor.html.twig')
                ->context(['link' => $this->generateUrl('app_users', [], UrlGeneratorInterface::ABSOLUTE_URL)]);
            $mailer->send($email);

            $this->addFlash('Success', 'Votre demande a bien été envoyée ! Vous aurez une réponse dans environ une semaine.');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('default/become-tutor.html.twig', [
            
        ]);
    }
}
