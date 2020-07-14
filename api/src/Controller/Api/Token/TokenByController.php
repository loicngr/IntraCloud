<?php

namespace App\Controller\Api\Token;

use App\Entity\Token;
use App\Controller\Api\Token\TokenController;
use App\Service\Token\TokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TokenByController extends TokenController
{
    /**
     * Récupère un Token via son ID
     * @Route("/token/{id}", name="token_by_id", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function tokenByID($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            /** @var Token $token */
            $token = $this->tokenRepository->find(intval($id));

            $responseRequest['data'] = 'token not found.';
            if (!empty($token)):
                $responseRequest = [
                    'status' => 200,
                    'data' => [
                        'id' => $token->getId(),
                        'token' => $token->getToken(),
                        'created_at' => $token->getCreatedAt(),
                        'user_id' => $token->getUser()->getId(),
                    ],
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * Récupère les informations d'un Token par le token
     * @Route("/token/generated/{token}", name="token_by_token", methods={"GET"})
     * @param $token
     * @param TokenService $tokenService
     * @return JsonResponse
     */
    public function tokenByTOKEN($token, TokenService $tokenService)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{TOKEN} parameter not found.',
        ];

        if (!empty($token)):
            /** @var Token */
            $generatedToken = $this->tokenRepository->findOneByTokenData($token);

            $responseRequest['data'] = 'token not found.';
            if (!empty($generatedToken)):
                // Vérification de la date Token
                $responseDateToken = json_decode($tokenService->checkDateToken($generatedToken));
                $responseRequest['data'] = $responseDateToken->data;

                if ($responseDateToken->status === 200):
                    $responseRequest = [
                        'status' => 200,
                        'data' => [
                            'id' => $generatedToken->getId(),
                            'token' => $generatedToken->getToken(),
                            'created_at' => $generatedToken->getCreatedAt(),
                            'user_id' => $generatedToken->getUser()->getId(),
                        ],
                    ];
                else:
                    $this->manager->remove($generatedToken);
                    $this->manager->flush();
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
