<?php

namespace App\Controller\Api\Document;

use App\Repository\DocumentRepository;
use App\Service\Encrypt\Encryption;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentController extends AbstractController
{
    protected $documentRepository;
    protected $manager;
    protected $encryption;

    public function __construct(DocumentRepository $documentRepository, EntityManagerInterface $manager, Encryption $encryption)
    {
        $this->documentRepository = $documentRepository;
        $this->manager = $manager;
        $this->encryption = $encryption;
    }
}
