<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Encrypt\Encryption;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    protected $encoder;
    protected $userRepository;
    protected $manager;
    protected $requestCollection;
    protected $encryption;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        Encryption $encryption
    ) {
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->encryption = $encryption;
    }

    /**
     * Récupère tous les utilisateurs en base de données
     * @Route("/users", name="users", methods={"GET"})
     */
    public function allUsers()
    {
        $responseRequest = [
            'status' => 404,
            'data' => "You don't have the authorization.",
        ];

        if ($this->isGranted("ROLE_ADMIN")):
            $responseRequest = [
                'status' => 404,
                'data' => "Users not found.",
            ];

            $usersCollection = [];
            foreach ($this->userRepository->findAll() as $user):
                $usersCollection[] = [
                    'id' => $user->getId(),
                    'firstname' => $user->getFirstName(),
                    'lastname' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'verified' => $user->getVerified(),
                    'roles' => $user->getRoles(),
                    'created_at' => $user->getCreatedAt(),
                    'updated_at' => $user->getUpdatedAt(),
                ];
            endforeach;

            if (!empty($usersCollection)):
                $responseRequest = [
                    'status' => 200,
                    'data' => $usersCollection,
                ];
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * Récupère mon compte utilisateur
     * @Route("/me", name="user_me", methods={"GET"})
     */
    public function me()
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'user not found.',
        ];

        /** @var User */
        $user = $this->getUser();

        if (!empty($user)):
            $responseRequest = [
                'status' => 200,
                'data' => [
                    'id' => $user->getId(),
                    'firstname' => $user->getFirstName(),
                    'lastname' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'verified' => $user->getVerified(),
                    'roles' => $user->getRoles(),
                    'created_at' => $user->getCreatedAt(),
                    'updated_at' => $user->getUpdatedAt(),
                ],
            ];
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
