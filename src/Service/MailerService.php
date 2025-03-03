<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;


class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEvaluationNotification(
        EntityManagerInterface $entityManager, // Attendu ici
        $email, 
        string $evaluationTitre, 
        MailerInterface $mailer, 
        \DateTimeInterface $date
    )
    {
        $email = (new Email())
            ->from('yo.yotalent7@gmail.com')
            ->to('marammelki12@gmail.com')
            ->subject('Confirmation de votre commande')
            ->html('<h1>Tu seras évalué</h1>');
        
        $mailer->send($email);
    }
    
}
