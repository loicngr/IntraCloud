<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Service\Mail\MailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class NewUserController extends UserController
{
    /**
     * Création d'un nouveau compte utilisateur
     * @Route("/user", name="new_user", methods={"POST"})
     * @param Request $request
     * @param MailService $mailService
     * @param MailerInterface $mailer
     * @return JsonResponse
     */
    public function newUser(Request $request, MailService $mailService, MailerInterface $mailer)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        if (
            $request->request->get('firstname') !== null &&
            $request->request->get('lastname') !== null &&
            $request->request->get('email') !== null &&
            $request->request->get('password') !== null
        ):
            $FROM_EMAIL = "";
            $MAIL_ACCOUNT_CREATED_SUBJECT = "";

            try {
                $FROM_EMAIL = $_ENV['MAILER_EMAIL'];
                $MAIL_ACCOUNT_CREATED_SUBJECT = $_ENV['MAIL_ACCOUNT_CREATED_SUBJECT'];
                $statusEnv = true;
            } catch (\Throwable $th) {
                $statusEnv = false;
                $responseRequest = [
                    'status' => 404,
                    'data' => 'env variables not found.',
                ];
            }

            if ($statusEnv && !empty($FROM_EMAIL) && !empty($MAIL_ACCOUNT_CREATED_SUBJECT)):
                $user = new User();
                $user
                    ->setEmail($request->request->get('email'))
                    ->setFirstName($request->request->get('firstname'))
                    ->setLastName($request->request->get('lastname'))
                    ->setCreatedAt(new \DateTime())
                    ->setRoles(['ROLE_NOT_VERIFIED'])
                    ->setPassword($this->encoder->encodePassword($user, $request->request->get('password')));

                try {
                    $this->manager->persist($user);
                    $this->manager->flush();
                    $responseRequest = [
                        'status' => 200,
                        'data' => 'account created.',
                    ];
                } catch (\Throwable $th) {
                    $responseRequest['data'] = "account not created.";
                }

                if ($responseRequest["status"] === 200):
                    $sendMail = json_decode(
                        $mailService->send(
                            'emails/accountCreated.html.twig',
                            $FROM_EMAIL,
                            $MAIL_ACCOUNT_CREATED_SUBJECT,
                            $user,
                            $mailer
                        )
                    );

                    if ($sendMail->status !== 200):
                        $responseRequest = [
                            'status' => 200,
                            'data' => 'account created but email not send.',
                        ];
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
