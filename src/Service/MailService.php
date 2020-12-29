<?php


namespace App\Service;


use App\Entity\User;
use RuntimeException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Class MailService
 * @package App\Service
 */
class MailService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function notifyUserEightItems(User $user): bool
    {
        $email = (new Email())
            ->from('no-reply@todolist.com')
            ->to($user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Warning ! Todolist almost full')
            ->html(
                '<p>Hello<br>,
                We are sending you this email to let you know that your todolist is almost full. More than 2 items can be added.<br>
                Thank you for your interest,</p>'
            );

        $email->getHeaders()
            ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            throw new  RuntimeException($e->getMessage());
        }
    }
}