<?php

namespace App\Controller\Traits;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait adminValidationEmail{
    use emailData;

    private function sendAdminsEmailForPendingUser(MailerInterface $mailer, UserRepository $userRepository, User $user = null){
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerEmail, $this->mailerName))
            ->to(...$userRepository->findFacultyAdminEmails($user ? $user->getFaculty() : $this->getUser()->getFaculty()))
            ->subject('Tutoru : Nouveau compte à valider')
            ->htmlTemplate('email/new-pending-user.html.twig')
            ->context([
                'link' => $this->generateUrl('app_users', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        $mailer->send($email);
    }
}