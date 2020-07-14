<?php

namespace App\Controller\Api\Token;

use App\Entity\Token;
use App\Controller\Api\Token\TokenController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DeleteTokenController extends TokenController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Supprime un Token via son ID
     * @Route("/token/{id}", name="delete_token_by_id", methods={"DELETE"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTokenByID($id, Request $request)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            /** @var Token */
            $token = $this->tokenRepository->find(intval($id));
            $ip_adresse = $request->getClientIp();

            $responseRequest['data'] = 'token not found.';
            if (!empty($token)):
                /** @var User */
                $user = $this->getUser();

                $responseRequest['data'] = 'ip adresse not valid.';
                if ($ip_adresse === $token->getIpAdress()):
                    if (!empty($user)):
                        $responseRequest['data'] = "You don't have the authorization.";

                        if ($this->getUser()->getId() !== $token->getUser()->getId() || $this->isGranted("ROLE_ADMIN")):
                            try {
                                $this->manager->remove($token);
                                $this->manager->flush();
                                $responseRequest = [
                                    'status' => 200,
                                    'data' => 'token deleted.',
                                ];
                            } catch (\Throwable $th) {
                                $responseRequest = [
                                    'status' => 404,
                                    'data' => 'token not deleted.',
                                ];
                            }
                        endif;
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
