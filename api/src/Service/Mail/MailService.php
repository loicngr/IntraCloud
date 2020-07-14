<?php
namespace App\Service\Mail;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailService
{
    /**
     * @param string $twigTemplate
     * @param string $from
     * @param string $subject
     * @param string $date_timer
     * @param string $link_mail
     * @param User $user
     * @param $mailer
     * @return false|string
     */
    public function sendResetPassword(
        string $twigTemplate,
        string $from,
        string $subject,
        string $date_timer,
        string $link_mail,
        User $user,
        $mailer
    ) {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->htmlTemplate($twigTemplate)
            ->context([
                'link_reset' => $link_mail,
                'expiration_message' => $date_timer,
                'username' => $user->getFirstName(),
            ]);

        try {
            $mailer->send($email);
            $responseRequest = [
                'status' => 200,
                'data' => 'email send',
            ];
        } catch (\Throwable $th) {
            $responseRequest = [
                'status' => 404,
                'data' => 'email not send',
            ];
        }
        return json_encode($responseRequest);
    }

    /**
     * @param string $twigTemplate
     * @param string $from
     * @param string $subject
     * @param User $user
     * @param $mailer
     * @return false|string
     */
    public function send(string $twigTemplate, string $from, string $subject, User $user, $mailer)
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->htmlTemplate($twigTemplate)
            ->context([
                'username' => $user->getFirstName(),
            ]);

        try {
            $mailer->send($email);
            $responseRequest = [
                'status' => 200,
                'data' => 'email send',
            ];
        } catch (\Throwable $th) {
            $responseRequest = [
                'status' => 404,
                'data' => 'email not send',
            ];
        }
        return json_encode($responseRequest);
    }
}
