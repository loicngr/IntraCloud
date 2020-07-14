<?php

namespace App\Controller\Api\Server;

use App\Controller\Api\Server\ServerController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ServersController extends ServerController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Récupère tous les serveurs enregistrés en bdd
     * @Route("/servers", name="servers", methods={"GET"})
     */
    public function servers()
    {
        $responseRequest = [
            'status' => 404,
            'data' => "servers not found.",
        ];

        $serversCollection = [];
        foreach ($this->serverRepository->findAll() as $server):
            $serversCollection[] = [
                'id' => $server->getId(),
                'name' => $server->getName(),
                'adresse' => $server->getAdresse(),
                'port' => $server->getPort(),
                'documents_count' => count($server->getDocuments()->getValues()),
                'default_path' => $server->getDefaultPath(),
                'login' => $server->getUsername(),
				'accepted_roles' => $server->getAcceptedRoles(),
            ];
        endforeach;

        if (!empty($serversCollection)):
            $responseRequest = [
                'status' => 200,
                'data' => $serversCollection,
            ];
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
