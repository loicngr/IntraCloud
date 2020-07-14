<?php

namespace App\Controller\Api\Ssh;

use App\Entity\Server;
use App\Service\Encrypt\Encryption;
use App\Service\Ssh\SshService;
use App\Repository\ServerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SshController extends AbstractController
{
    protected $manager;
    protected $sshService;

    public function __construct(EntityManagerInterface $manager, SshService $sshService)
    {
        $this->manager = $manager;
        $this->sshService = $sshService;
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Exécute une commande UNIX dans un serveur
     * @Route("/ssh/exec", name="ssh_exec_command", methods={"POST"})
     * @param Request $request
     * @param ServerRepository $serverRepository
     * @param Encryption $encryption
     * @return JsonResponse
     */
    public function execCommand(Request $request, ServerRepository $serverRepository, Encryption $encryption): JsonResponse
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        if (
            $request->request->get('name') !== null &&
            $request->request->get('command') !== null
        ):
            $serverName = $request->request->get('name');
            $command = $request->request->get('command');

            $ssh_available_cmd = isset($_ENV['SSH_AVAILABLE_CMD'])
                ? $_ENV['SSH_AVAILABLE_CMD']
                : "ls /,\ pwd /,\ mkdir /,\ touch /,\ cat /,\ mv /,\ echo /,\ rm /,\ && /,\ stat /,\ base64 /,\ file";

            $ssh_not_available_cmd = isset($_ENV['SSH_NOT_AVAILABLE_CMD'])
                ? $_ENV['SSH_NOT_AVAILABLE_CMD']
                : "grep /,\ sed /,\ awk /,\ sudo";

            /**
             * Vérification de la commande
             */
            $available = array_map('trim', explode("/,\\", $ssh_available_cmd));
            $not_available = array_map('trim', explode("/,\\", $ssh_not_available_cmd));

            $check = function ($value) use ($command) {
                return strpos($command, $value) === 0;
            };
            $checkNotAvailable = array_map($check, $not_available);
            $checkAvailable = array_map($check, $available);

            $responseRequest['data'] = 'command not valid.';

            if (!array_search(true, $checkNotAvailable) && array_search(true, $checkAvailable) !== false):
                /** @var Server $server */
                $server = $serverRepository->findOneBy(['name' => $serverName]);

                $responseService = json_decode(
                    $this->sshService->exec(
                        $command,
                        $server->getAdresse(),
                        $server->getPort(),
                        $server->getUsername(),
                        $server->getPassword(),
                        $server->getPrivateKey(),
                        $server->getPassphrase()
                    )
                );

                if ($responseService):
                    $fileContentEncrypted = $encryption->encrypt($responseService->data);

                    $responseRequest = [
                        'status' => 200,
                        'data' => $fileContentEncrypted,
                    ];
                endif;
            endif;
        endif;

        return $this->json($responseRequest);
    }
}
