<?php

namespace App\Controller\Api\Document;

use App\Entity\Server;
use App\Entity\Document;
use App\Repository\ServerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Api\Document\DocumentController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DocumentByController extends DocumentController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * Récupère un Document par son ID
     * @Route("/document/{id}", name="document_by_id", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function documentByID($id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            /** @var Document */
            $document = $this->documentRepository->find(intval($id));

            $responseRequest['data'] = 'document not found.';
            if (!empty($document)):
                $responseRequest = [
                    'status' => 200,
                    'data' => [
                        'id' => $document->getId(),
                        'name' => $document->getName(),
                        'location' => $document->getLocation(),
                        'server_id' => $document->getServer()->getId(),
                        'user_id' => $document->getUser()->getId(),
                        'size' => $document->getSize(),
                        'created_at' => $document->getCreatedAt(),
                    ],
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * Recherche un document par le Nom, la localisation et le serveur ID
     * @Route("/document/search/q", name="document_by_search", methods={"POST"})
     * @param Request $request
     * @param ServerRepository $serverRepository
     * @return JsonResponse
     */
    public function documentBySearch(Request $request, ServerRepository $serverRepository)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        if (
            $request->request->get('name') !== null &&
            $request->request->get('location') !== null &&
            $request->request->get('server_id') !== null
        ):
            /** @var Server $server */
            $server = $serverRepository->find(intval($request->request->get('server_id')));
            $responseRequest['data'] = 'server not found.';

            if (!empty($server)):
                $document = $this->documentRepository->findOneByNameAndLocationAndServer(
                    $request->request->get('name'),
                    $request->request->get('location'),
                    $server
                );

                $responseRequest['data'] = 'document not found.';
                if (!empty($document)):
                    $responseRequest = [
                        'status' => 200,
                        'data' => [
                            'id' => $document->getId(),
                            'name' => $document->getName(),
                            'location' => $document->getLocation(),
                            'server_id' => $document->getServer()->getId(),
                            'user_id' => $document->getUser()->getId(),
                            'size' => $document->getSize(),
                            'created_at' => $document->getCreatedAt(),
                        ],
                    ];
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
