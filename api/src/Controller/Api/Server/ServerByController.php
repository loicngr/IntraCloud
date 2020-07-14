<?php

namespace App\Controller\Api\Server;

use App\Entity\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ServerByController extends ServerController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Récupère un serveur par son ID
     * @Route("/server/{id}", name="server_by_id", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function serverByID($id)
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
                $responseRequest = [
                    'status' => 200,
                    'data' => [
                        'id' => $server->getId(),
                        'name' => $server->getName(),
                        'adresse' => $server->getAdresse(),
                        'port' => $server->getPort(),
                        'default_path' => $server->getDefaultPath(),
						'accepted_roles' => $server->getAcceptedRoles(),
                        'documents_count' => count($server->getDocuments()->getValues()),
                    ],
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Récupère un serveur par son nom
     * @Route("/server/name", name="server_by_name", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function serverByName(Request $request)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        if ($request->request->get('name') !== null):
            $name = $this->encryption->decrypt($request->request->get('name'));

            /** @var Server */
            $server = $this->serverRepository->findOneBy(['name' => $name]);

            $responseRequest['data'] = 'server not found.';
            if (!empty($server)):
                $responseRequest = [
                    'status' => 200,
                    'data' => [
                        'id' => $server->getId(),
                        'name' => $server->getName(),
                        'adresse' => $server->getAdresse(),
                        'port' => $server->getPort(),
                        'default_path' => $server->getDefaultPath(),
                        'documents_count' => count($server->getDocuments()->getValues()),
						'privateKey' => $server->getPrivateKey() ? true : false,
						'passphrase' => $server->getPassphrase() ? true : false,
						'accepted_roles' => $server->getAcceptedRoles(),
                    ],
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * @Route("/server/find/{host}/{port}/{login}", name="server_by_host_port_login", methods={"GET"})
     * @param $host
     * @param $port
     * @param $login
     * @return JsonResponse
     */
    public function serverByHostPortLogin($host, $port, $login)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        if (!empty($host) && !empty($port) && !empty($login)):
            /** @var Server */
            $server = $this->serverRepository->findByAdressePortLogin($host, $port, $login);

            $responseRequest['data'] = 'server not found.';
            if (!empty($server)):
                $responseRequest = [
                    'status' => 200,
                    'data' => [
                        'id' => $server->getId(),
                        'name' => $server->getName(),
                        'login' => $server->getUsername(),
                        'adresse' => $server->getAdresse(),
                        'port' => $server->getPort(),
                        'default_path' => $server->getDefaultPath(),
                        'documents_count' => count($server->getDocuments()->getValues()),
                        'privateKey' => $server->getPrivateKey() ? true : false,
                        'passphrase' => $server->getPassphrase() ? true : false,
						'accepted_roles' => $server->getAcceptedRoles(),
                    ],
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * Supprime tous les documents d'un serveur
     * @Route("/server/{id}/documents", name="delete_documents", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteDocuments($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            /** @var Server $server */
            $server = $this->serverRepository->find(intval($id));

            $responseRequest['data'] = 'server not found.';
            if (!empty($server)):
                $documents = $server->getDocuments();
                foreach ($documents as $document):
                    $this->manager->remove($document);
                    $this->manager->flush();
                endforeach;

                $responseRequest = [
                    'status' => 200,
                    'data' => 'documents deleted.',
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
