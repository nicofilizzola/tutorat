<?php

namespace App\Controller\Traits;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

trait emailData {
    private $mailerName = "Tutoru";
    private $mailerEmail = "no-reply@tutoru.fr";

    private function sendEmail(MailerInterface $mailer, array $toAddress, $subject, $template, $context = null){
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerEmail, $this->mailerName))
            ->to(...$toAddress)
            ->subject("Tutoru : " . $subject)
            ->htmlTemplate($template)
            ->context(is_null($context) ? [] : $context);
        $mailer->send($email);
    }
   
}