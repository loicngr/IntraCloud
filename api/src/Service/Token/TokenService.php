<?php
namespace App\Service\Token;

use App\Entity\Token;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Service qui gère la création, suppression des Token.
 * Par Token il faut comprendre le mot de passe temporaire pour
 * la réinitialisation d'un mot de passe
 */
class TokenService
{
    /**
     * @param User $user
     * @param string $ip_adresse
     * @param EntityManager $manager
     * @return false|string
     */
    public function newToken(User $user, string $ip_adresse, EntityManager $manager)
    {
        $token = new Token();
        try {
            $bytes = random_bytes(10);
            $generatedToken = bin2hex($bytes);

            $token
                ->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')))
                ->setUser($user)
                ->setIpAdress($ip_adresse)
                ->setToken($generatedToken);

            $manager->persist($token);
            $manager->flush();

            $responseRequest = [
                'status' => 200,
                'data' => $generatedToken,
            ];
        } catch (\Throwable $th) {
            $responseRequest = [
                'status' => 404,
                'data' => 'token not created.',
            ];
        }
        return json_encode($responseRequest);
    }

    /**
     * @param Token $token
     * @param EntityManager $tokenManager
     * @return false|string
     */
    public function removeToken(Token $token, EntityManager $tokenManager)
    {
        try {
            $tokenManager->remove($token);
            $tokenManager->flush();
            $responseRequest = [
                'status' => 200,
                'data' => 'token deleted.',
            ];
        } catch (\Throwable $th) {
            $responseRequest = [
                'status' => 404,
                'data' => 'token not deleted.',
            ];
        }
        return json_encode($responseRequest);
    }

    /**
     * @param Token $token
     * @return false|string
     */
    public function checkDateToken(Token $token)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'expired token.',
        ];

        $MAX_TIME_PROCESS_HOUR = $_ENV['MAIL_RESET_PASS_MAX_TIME_PROCESS_HOUR'];
        $MAX_TIME_PROCESS_DAYS = $_ENV['MAIL_RESET_PASS_MAX_TIME_PROCESS_DAYS'];

        if (!isset($MAX_TIME_PROCESS_DAYS)):
            $MAX_TIME_PROCESS_DAYS = 0;
        endif;
        if (!isset($MAX_TIME_PROCESS_HOUR)):
            $MAX_TIME_PROCESS_HOUR = 1;
        endif;

        $dateNow = new \DateTime('now');
        $dateToken = $token->getCreatedAt();
        $diffDate = date_diff($dateToken, $dateNow);

        if ($diffDate->days <= intval($MAX_TIME_PROCESS_DAYS) && $diffDate->h <= intval($MAX_TIME_PROCESS_HOUR)):
            $responseRequest = [
                'status' => 200,
                'data' => 'token valid.',
            ];
        endif;

        return json_encode($responseRequest);
    }
}
