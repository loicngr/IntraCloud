<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Entity\Token;
use App\Service\Token\TokenService;
use App\Controller\Api\User\UserController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserByController extends UserController
{
    /**
     * Récupère un utilisateur avec son ID
     * @Route("/user/{id}", name="user_by_id", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function userByID($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            $uid = intval($id);
            $responseRequest['data'] = "You don't have the authorization.";

            if ($this->getUser()->getId() === $uid || $this->isGranted("ROLE_ADMIN")):
                /** @var User $user */
                $user = $this->userRepository->find($uid);

                $responseRequest['data'] = 'user not found.';
                if (!empty($user)):
                    $responseRequest = [
                        'status' => 200,
                        'data' => [
                            'id' => $user->getId(),
                            'firstname' => $user->getFirstName(),
                            'lastname' => $user->getLastName(),
                            'email' => $user->getEmail(),
                            'verified' => $user->getVerified(),
                            'roles' => $user->getRoles(),
                            'created_at' => $user->getCreatedAt(),
                            'updated_at' => $user->getUpdatedAt(),
                        ],
                    ];
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * Récupère un utilisateur avec son email
     * @Route("/user/email/{email}", name="user_by_email", methods={"GET"})
     * @param $email
     * @return JsonResponse
     */
    public function userByEMAIL($email)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{EMAIL} parameter is not a email.',
        ];

        if (filter_var($email, FILTER_VALIDATE_EMAIL)):
            $responseRequest['data'] = "You don't have the authorization.";

            if ($this->getUser()->getEmail() === $email || $this->isGranted("ROLE_ADMIN")):
                /** @var User $user */
                $user = $this->userRepository->findOneByEmail($email);

                $responseRequest['data'] = 'user not found.';
                if (!empty($user)):
                    $responseRequest = [
                        'status' => 200,
                        'data' => [
                            'id' => $user->getId(),
                            'firstname' => $user->getFirstName(),
                            'lastname' => $user->getLastName(),
                            'email' => $user->getEmail(),
                            'verified' => $user->getVerified(),
                            'roles' => $user->getRoles(),
                            'created_at' => $user->getCreatedAt(),
                            'updated_at' => $user->getUpdatedAt(),
                        ],
                    ];
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * Vérifie si un utilisateur à lancé une procédure de réinitialisation du mot de passe
     * @Route("/user/check/resetpassword/", name="user_check_reset_password", methods={"GET"})
     * @param TokenService $tokenService
     * @param Request $request
     * @return JsonResponse
     */
    public function userCheckResetPassword(TokenService $tokenService, Request $request)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'user not found.',
        ];

        /** @var User $user */
        $user = $this->getUser();
        $ip_adresse = $request->getClientIp();

        if (!empty($user)):
            $responseRequest['data'] = 'not in process.';

            /** @var Token $userToken */
            $userToken = $user->getToken();
            if (!empty($userToken)):
                $responseRequest['data'] = 'ip adress not valid.';
                if ($ip_adresse === $userToken->getIpAdress()):
                    $responseRequest['data'] = 'token not deleted.';

                    $responseDeleteToken = json_decode($tokenService->removeToken($userToken, $this->manager));
                    if ($responseDeleteToken->status === 200):
                        $responseRequest = [
                            'status' => 200,
                            'data' => 'token deleted.',
                        ];
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
