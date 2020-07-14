<?php

namespace App\Controller\Api\Sftp;

use App\Entity\Server;
use App\Service\Encrypt\Encryption;
use App\Service\Sftp\SftpService;
use App\Repository\ServerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SftpController extends AbstractController
{
    protected $manager;
    protected $sftpService;

    public function __construct(EntityManagerInterface $manager, SftpService $sftpService)
    {
        $this->manager = $manager;
        $this->sftpService = $sftpService;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * @Route("/sftp/zip", name="sftp_zip_folder", methods={"POST"})
     * @param Request $request
     * @param ServerRepository $serverRepository
     * @param Encryption $encryption
     * @return JsonResponse
     */
    public function zipMyFolder(Request $request, ServerRepository $serverRepository, Encryption $encryption): JsonResponse
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        if (
            $request->request->get('name') !== null &&
            $request->request->get('zipTarget') !== null &&
            $request->request->get('zipFilename') !== null &&
            $request->request->get('folderPath') !== null
        ):
            $serverName = $request->request->get('name');
            $zipTarget = $request->request->get('zipTarget');
            $zipFilename = $request->request->get('zipFilename');
            $folderPath = $request->request->get('folderPath');

            /** @var Server $server */
            $server = $serverRepository->findOneBy(['name' => $serverName]);

            $responseService = json_decode(
                $this->sftpService->zipFolder(
                    $folderPath,
                    $zipFilename,
                    $zipTarget,
                    $server->getAdresse(),
                    $server->getPort(),
                    $server->getUsername(),
                    $server->getPassword(),
                    $server->getPrivateKey(),
                    $server->getPassphrase()
                )
            );

            if ($responseService):
                $filesystem = new Filesystem();

                // Si le fichier dans le dossier temporaire existe bien
                if ($filesystem->exists($responseService->data)):
                    // On retourne le contenu du fichier en base64 et encrypté
                    $fileContent = base64_encode(file_get_contents($responseService->data));
                    $fileContentEncrypted = $encryption->encrypt($fileContent);

                    $responseRequest = [
                        'status' => 200,
                        'data' => $fileContentEncrypted,
                    ];

                    // On supprime le fichier du dossier temporaire
                    $filesystem->remove($responseService->data);
                else:
                    $responseRequest = [
                        'status' => 404,
                        'data' => '.zip file not found.',
                    ];
                endif;
            endif;
        endif;

        return $this->json($responseRequest);
    }
}
