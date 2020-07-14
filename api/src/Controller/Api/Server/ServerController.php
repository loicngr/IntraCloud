<?php

namespace App\Controller\Api\Server;

use App\Service\Encrypt\Encryption;
use App\Service\Ssh\SshService;
use App\Repository\ServerRepository;
use App\Service\OpenSSL\OpenSslService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServerController extends AbstractController
{
    protected $serverRepository;
    protected $manager;
    protected $sshService;
    protected $encryption;

    public function __construct(
        ServerRepository $serverRepository,
        EntityManagerInterface $manager,
        SshService $sshService,
        OpenSslService $openSslService,
        Encryption $encryption
    ) {
        $this->serverRepository = $serverRepository;
        $this->manager = $manager;
        $this->sshService = $sshService;
        $this->encryption = $encryption;
    }
}
