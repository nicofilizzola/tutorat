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
    $user = $user ?? $this->getUser();
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerEmail, $this->mailerName))
            ->to(...$userRepository->findFacultyAdminEmails($user->getFaculty()))
            ->subject('Tutoru : Nouveau compte à valider')
            ->htmlTemplate('email/new-pending-user.html.twig')
            ->context([
                'homeLink' => $this->generateUrl('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'loginLink' => $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'registerLink' => $this->generateUrl('app_register', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'user' => $user
            ]);
        $mailer->send($email);
    }
}