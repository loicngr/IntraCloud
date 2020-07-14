<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Service\Mail\MailService;
use App\Service\Token\TokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResetPasswordController extends UserController
{
    //  * L'utilisateur demande à changer sont Mot de Passe
    //  * -    Vérification du mail
    //  * -    Génération du token (mot de passe temporaire)
    //  * -    Envoi du mail au client
    /**
     * @Route("/user/password/reset", name="reset_pass_user_by_email", methods={"POST"})
     * @param Request $request
     * @param MailerInterface $mailer
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @return JsonResponse
     */
    public function userResetPassword(
        Request $request,
        MailerInterface $mailer,
        TokenService $tokenService,
        MailService $mailService
    ) {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter not found.',
        ];

        $email = $request->request->get('email');
        if (!empty($email)):
            /** @var User */
            $user = $this->userRepository->findOneByEmail($email);
            $ip_adresse = $request->getClientIp();

            $responseRequest['data'] = 'user not found.';
            if (!empty($user)):
                $responseRequest['data'] = 'user not verified.';
                if ($user->getVerified()):
                    $responseRequest['data'] = 'env variables not found.';

                    $emailFrom = $_ENV['MAILER_EMAIL'];
                    $ADRESSE_UI = $_ENV['ADRESSE_UI'];
                    $emailPassResetSubject = $_ENV['MAIL_RESET_PASS_SUBJECT'];

                    $timer_process_hour = isset($_ENV['MAIL_RESET_PASS_MAX_TIME_PROCESS_HOUR'])
                        ? $_ENV['MAIL_RESET_PASS_MAX_TIME_PROCESS_HOUR']
                        : 1;
                    $timer_process_day = isset($_ENV['MAIL_RESET_PASS_MAX_TIME_PROCESS_DAYS'])
                        ? intval($_ENV['MAIL_RESET_PASS_MAX_TIME_PROCESS_DAYS'])
                        : 0;

                    if (!empty($emailFrom) && !empty($ADRESSE_UI) && !empty($emailPassResetSubject)):

                        $date_timer = $timer_process_day === 0 ? "Vous avez $timer_process_hour heure(s) pour finir la procédure." : "Vous avez $timer_process_day jour(s) et $timer_process_hour heure(s) pour finir la procédure.";

                        $generatedToken = json_decode($tokenService->newToken($user, $ip_adresse, $this->manager));
                        $responseRequest['data'] = 'user already has a token.';

                        if ($generatedToken->status === 200) {
                            $linkMail = $ADRESSE_UI . "/reset/" . $generatedToken->data;

                            $sendMail = json_decode(
                                $mailService->sendResetPassword(
                                    'emails/passwordReset.html.twig',
                                    $emailFrom,
                                    $emailPassResetSubject,
                                    $date_timer,
                                    $linkMail,
                                    $user,
                                    $mailer
                                )
                            );

                            $responseRequest = [
                                'status' => $sendMail->status,
                                'data' => $sendMail->data,
                            ];
                        }
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
