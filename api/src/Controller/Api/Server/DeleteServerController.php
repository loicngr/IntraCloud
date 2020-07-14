<?php

namespace App\Controller\Api\Server;

use App\Entity\Server;
use App\Controller\Api\Server\ServerController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DeleteServerController extends ServerController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * Supprime un serveur par son ID
     * @Route("/server/{id}", name="delete_server", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteServer($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            $responseRequest['data'] = "You don't have the authorization.";

            if ($this->isGranted("ROLE_ADMIN")):
                /** @var Server */
                $server = $this->serverRepository->find(intval($id));

                $responseRequest['data'] = 'server not found.';
                if (!empty($server)):
                    try {
                        $this->manager->remove($server);
                        $this->manager->flush();
                        $responseRequest = [
                            'status' => 200,
                            'data' => 'server deleted.',
                        ];
                    } catch (\Throwable $th) {
                        $responseRequest = [
                            'status' => 404,
                            'data' => 'server not deleted.',
                        ];
                    }
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * @Route("/server/{id}/delete/privatekey", name="delete_server_private_key", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deletePrivateKey($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            /** @var Server */
            $server = $this->serverRepository->find(intval($id));

            $responseRequest['data'] = 'server not found.';
            if (!empty($server)):
                $server->setPrivateKey(null);
                try {
                    $this->manager->persist($server);
                    $this->manager->flush();
                    $responseRequest = [
                        'status' => 200,
                        'data' => 'server privateKey deleted.',
                    ];
                } catch (\Throwable $th) {
                    $responseRequest = [
                        'status' => 404,
                        'data' => 'server privateKey not deleted.',
                    ];
                }
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * @Route("/server/{id}/delete/passphrase", name="delete_server_passphrase", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deletePassphrase($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            /** @var Server */
            $server = $this->serverRepository->find(intval($id));

            $responseRequest['data'] = 'server not found.';
            if (!empty($server)):
                $server->setPassphrase(null);
                try {
                    $this->manager->persist($server);
                    $this->manager->flush();
                    $responseRequest = [
                        'status' => 200,
                        'data' => 'server passphrase deleted.',
                    ];
                } catch (\Throwable $th) {
                    $responseRequest = [
                        'status' => 404,
                        'data' => 'server passphrase not deleted.',
                    ];
                }
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
