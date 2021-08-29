<?php

namespace App\Controller\Traits;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait emailData {
    private $mailerName = "Tutoru";
    private $mailerEmail = "no-reply@tutoru.fr";

    private function sendEmail(MailerInterface $mailer, array $toAddress, $subject, $template, $context = null){
        $contextLinks = [
            'homeLink' => $this->generateUrl('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'loginLink' => $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'registerLink' => $this->generateUrl('app_register', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];
        // dd($contextLinks);
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerEmail, $this->mailerName))
            ->to(...$toAddress)
            ->subject("Tutoru : " . $subject)
            ->htmlTemplate("email/" . $template)
            ->context(is_null($context) ? $contextLinks : array_merge($contextLinks, $context));
        $mailer->send($email);
    }
}