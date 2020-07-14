<?php
namespace App\Service\OpenSSL;

/**
 *
 * Le service n'est plus utilisé dabs le code,
 * Il a été remplacé par le service Encryption
 *
 * */

class OpenSslService
{
    protected $openSSL_key;
    protected $openSSL_cipher;
    protected $openSSL_iv;

    public function __construct()
    {
        $key = !isset($_ENV['OPEN_SSL_KEY']) ? hash('sha256', 'RJZ*H#!e01I$c#L82') : hash('sha256', $_ENV['OPEN_SSL_KEY']);
        $iv = !isset($_ENV['OPEN_SSL_IV'])
            ? substr(hash('sha256', 'an@aLJXl!7RIT546H'), 0, 16)
            : substr(hash('sha256', $_ENV['OPEN_SSL_IV']), 0, 16);

        $this->openSSL_key = $key;
        $this->openSSL_cipher = "aes-256-cbc";
        $this->openSSL_iv = $iv;
    }

    /**
     * Retourne une chaîne de caractères décrypté ou encrypté
     * @param string $str
     * @param bool $status True ou False, pour encrypter ou décrypté
     * @return string|null
     */
    private function getOpenSslString(string $str, bool $status): ?string
    {
        if ($status):
            return openssl_encrypt($str, $this->openSSL_cipher, $this->openSSL_key, $options = 0, $this->openSSL_iv);
        endif;
        return openssl_decrypt($str, $this->openSSL_cipher, $this->openSSL_key, $options = 0, $this->openSSL_iv);
    }

    /**
     * Fonction qui encode une chaine de caractères
     * @param string $plaintext
     * @return string|null
     */
    public function encodePassword(string $plaintext): ?string
    {
        $encodedPassword = $this->getOpenSslString($plaintext, true);

        return base64_encode($encodedPassword);
    }

    /**
     * Fonction qui décode une chaine de caractère
     * @param string $plaintext
     * @return string|null
     */
    public function decodePassword(string $plaintext): ?string
    {
        $chaine = base64_decode($plaintext);

        return $this->getOpenSslString($chaine, false);
    }
}
