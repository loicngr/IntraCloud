<?php

namespace App\Controller\Api\Token;

use App\Entity\User;
use App\Controller\Api\Token\TokenController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewTokenController extends TokenController
{
    /**
     * Créer un nouveau Token via le JWT donnée en header
     * @Route("/token", name="new_token", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function newToken(Request $request)
    {
        $ip_adresse = $request->getClientIp();

        $responseRequest['data'] = 'user not found.';

        /** @var User $user */
        $user = $this->getUser();

        if (!empty($user)):
            $responseNewToken = json_decode($this->tokenService->newToken($user, $ip_adresse, $this->manager));
            $responseRequest['data'] = 'token not created.';

            if ($responseNewToken->status === 200):
                $responseRequest = [
                    'status' => 200,
                    'data' => 'token created.',
                ];
            endif;
        endif;
        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
