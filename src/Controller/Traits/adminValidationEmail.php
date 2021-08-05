<?php

namespace App\Controller\Traits;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait adminValidationEmail{
    private function sendAdminsEmailForPendingUser(MailerInterface $mailer, UserRepository $userRepository){
        function getAdminMails($users){
            $adminEmails = [];
            foreach ($users as $user){
                if (in_array("ROLE_ADMIN", $user->getRoles())){
                    array_push($adminEmails, $user->getEmail());
                }
            }
            return $adminEmails;   
        }
    
        $toAddresses = getAdminMails($userRepository->findBy(['faculty' => $this->getUser()->getFaculty()]));
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tutorat-iut-tarbes.fr', 'Tutorat IUT de Tarbes'))
            ->to(...$toAddresses)
            ->subject('Nouveau compte à valider')
            ->htmlTemplate('email/new-pending-user.html.twig')
            ->context([
                'link' => $this->generateUrl('app_users', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        $mailer->send($email);
    }
}