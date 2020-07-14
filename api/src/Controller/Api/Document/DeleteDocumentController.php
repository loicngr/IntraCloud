<?php

namespace App\Controller\Api\Document;

use App\Entity\Document;
use App\Controller\Api\Document\DocumentController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DeleteDocumentController extends DocumentController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * Supprime un document par son Id
     * @Route("/document/{id}", name="delete_document", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteDocument($id)
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
                try {
                    $this->manager->remove($document);
                    $this->manager->flush();
                    $responseRequest = [
                        'status' => 200,
                        'data' => 'document deleted.',
                    ];
                } catch (\Throwable $th) {
                    $responseRequest = [
                        'status' => 404,
                        'data' => 'document not deleted.',
                    ];
                }
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
