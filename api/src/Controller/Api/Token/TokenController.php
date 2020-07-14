<?php

namespace App\Controller\Api\Token;

use App\Repository\TokenRepository;
use App\Service\Encrypt\Encryption;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TokenController extends AbstractController
{
    protected $tokenRepository;
    protected $manager;
    protected $encryption;
    public $tokenService;

    public function __construct(
        TokenRepository $tokenRepository,
        EntityManagerInterface $manager,
        TokenService $tokenService,
        Encryption $encryption
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->manager = $manager;
        $this->tokenService = $tokenService;
        $this->encryption = $encryption;
    }
}
