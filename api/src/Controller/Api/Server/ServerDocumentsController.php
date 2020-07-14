<?php

namespace App\Controller\Api\Server;

use App\Entity\Server;
use App\Entity\Document;
use App\Controller\Api\Server\ServerController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ServerDocumentsController extends ServerController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * @Route("/server/{id}/documents", name="server_documents", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function serverDocuments($id)
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
                $documentsCollection = [];
                /** @var Document $document */
                foreach ($server->getDocuments()->getValues() as $document):
                    $documentsCollection[] = [
                        'id' => $document->getId(),
                        'name' => $document->getName(),
                        'location' => $document->getLocation(),
                        'size' => $document->getSize(),
                        'user' => [
                            'id' => $document->getUser()->getId(),
                            'firstname' => $document->getUser()->getFirstName(),
                            'lastname' => $document->getUser()->getLastName(),
                        ],
                        'created_at' => $document->getCreatedAt(),
                    ];
                endforeach;

                $responseRequest = [
                    'status' => 200,
                    'data' => $documentsCollection,
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
