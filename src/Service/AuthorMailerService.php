<?php
// src/Service/AuthorMailerService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AuthorMailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyAuthor(string $authorEmail, string $bookTitle): void
    {
        $email = (new Email())
            ->from('no-reply@mysite.com')
            ->to($authorEmail)
            ->subject('Nouveau livre publié !')
            ->text("Félicitations ! Votre nouveau livre '{$bookTitle}' a été ajouté.");

        $this->mailer->send($email);
    }
}
