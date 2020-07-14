<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Controller\Api\User\UserController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DeleteUserController extends UserController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Suppression d'un compte utilisateur
     * @Route("/user/{id}", name="user_delete_by_id", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteUserByID($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            $uid = intval($id);

            $responseRequest['data'] = "You don't have the authorization.";

            if ($this->getUser()->getId() !== $uid && $this->isGranted("ROLE_ADMIN")):
                /** @var User */
                $user = $this->userRepository->find($uid);

                $responseRequest['data'] = 'user not found.';
                if (!empty($user)):
                    try {
                        $this->manager->remove($user);
                        $this->manager->flush();
                        $responseRequest = [
                            'status' => 200,
                            'data' => 'user deleted.',
                        ];
                    } catch (\Throwable $th) {
                        $responseRequest = [
                            'status' => 404,
                            'data' => 'user not deleted.',
                        ];
                    }
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
