<?php

namespace App\Controller\Api\Document;

use DateTime;
use App\Entity\Document;
use App\Service\Sftp\SftpService;
use App\Repository\UserRepository;
use App\Repository\ServerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class NewDocumentController extends DocumentController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Créé un nouveau Document
     * @Route("/document", name="new_document", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ServerRepository $serverRepository
     * @param SftpService $sftpService
     * @return JsonResponse
     */
    public function newDocument(
        Request $request,
        UserRepository $userRepository,
        ServerRepository $serverRepository,
        SftpService $sftpService
    ) {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];
        if (
            $request->request->get('name') !== null &&
            $request->request->get('location') !== null &&
            $request->files->get('file') !== null &&
            $request->request->get('user_id') !== null &&
            $request->request->get('size') !== null &&
            $request->request->get('server_id') !== null
        ):
            $FileName = $request->request->get('name');
            $FileLocation = $request->request->get('location');
            $File = $request->files->get('file');
            $UserID = $request->request->get('user_id');
            $FileSize = $request->request->get('size');
            $ServerID = $request->request->get('server_id');

            if (!$File && !is_readable($File)):
                $responseRequest['data'] = "Can't read this file: $FileName ... .";
            else:
                $user = $userRepository->find(intval($UserID));

                $server = $serverRepository->find(intval($ServerID));

                $responseRequest['data'] = "user or server not found.";
                if (!empty($user) && !empty($server) && !empty($File) && isset($File) && is_file($File)):
                    $responseSftp = json_decode(
                        $sftpService->upload(
                            $FileLocation . '/' . $FileName,
                            file_get_contents($File),
                            $server->getAdresse(),
                            $server->getPort(),
                            $server->getUsername(),
                            $server->getPassword(),
                            $server->getPrivateKey(),
                            $server->getPassphrase()
                        )
                    );

                    $responseRequest['data'] = $responseSftp->data;
                    if ($responseSftp->status):
                        $responseRequest['data'] = "document not upload.";
                        if ($responseSftp->data):
                            $documentFind = $this->documentRepository->findOneByNameAndLocationAndServer(
                                $request->request->get('name'),
                                $request->request->get('location'),
                                $server
                            );

                            if (isset($documentFind)):
                                $document = $documentFind;
                                $document->setUpdatedAt(new DateTime());
                            else:
                                $document = new Document();
                            endif;

                            $document
                                ->setName($FileName)
                                ->setLocation($FileLocation)
                                ->setSize(floatval($FileSize))
                                ->setUser($user)
                                ->setCreatedAt(new DateTime())
                                ->setServer($server);

                            $this->manager->persist($document);
                            $this->manager->flush();

                            $responseRequest = [
                                'status' => 200,
                                'data' => 'document upload.',
                            ];
                        endif;
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
