<?php

namespace App\Service\Encrypt;

class Encryption
{
    /**
     * @var string La méthode Cipher
     */
    protected $encryptMethod = 'AES-256-CBC';

    protected $envKey;

    public function __construct()
    {
        $this->envKey = isset($_ENV['ENCRYPTION_KEY']) ? $_ENV['ENCRYPTION_KEY'] : '';
    }

    /**
     * Fonction qui décrypte une chaine de caractères
     *
     * @param string $encryptedString La chaine de caractère déjà encrypté et en base64
     * @return mixed
     */
    public function decrypt($encryptedString)
    {
        $json = json_decode(base64_decode($encryptedString), true);

        try {
            $salt = hex2bin($json["salt"]);
            $iv = hex2bin($json["iv"]);
        } catch (Exception $e) {
            return null;
        }

        $cipherText = base64_decode($json['ciphertext']);

        $iterations = intval(abs($json['iterations']));
        if ($iterations <= 0) {
            $iterations = 999;
        }

        $hashKey = hash_pbkdf2('sha512', $this->envKey, $salt, $iterations, $this->encryptMethodLength() / 4);
        unset($iterations, $json, $salt);

        $decrypted = openssl_decrypt($cipherText, $this->encryptMethod, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
        unset($cipherText, $hashKey, $iv);

        return $decrypted;
    }

    /**
     * Fonction qui encrypte une chaine de caractère
     *
     * @param string $string La chaine de caractère a crypter
     * @return string
     */
    public function encrypt($string)
    {
        $ivLength = openssl_cipher_iv_length($this->encryptMethod);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $salt = openssl_random_pseudo_bytes(256);
        $iterations = 999;
        $hashKey = hash_pbkdf2('sha512', $this->envKey, $salt, $iterations, $this->encryptMethodLength() / 4);

        $encryptedString = openssl_encrypt($string, $this->encryptMethod, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);

        $encryptedString = base64_encode($encryptedString);
        unset($hashKey, $string);

        $output = ['ciphertext' => $encryptedString, 'iv' => bin2hex($iv), 'salt' => bin2hex($salt), 'iterations' => $iterations];
        unset($encryptedString, $iterations, $iv, $ivLength, $salt);

		$outputJson = json_encode($output);
		unset($output);
        $outputB64 = base64_encode($outputJson);
		unset($outputJson);

        return $outputB64;
    }

    /**
     * Retourne le nombre de la méthode Cipher (128, 192, 256)
     *
     * @return integer.
     */
    protected function encryptMethodLength()
    {
        return intval(abs(filter_var($this->encryptMethod, FILTER_SANITIZE_NUMBER_INT)));
    }

    /**
     * Défini la méthode de cryptage
     *
     * @param string $cipherMethod
     */
    public function setCipherMethod($cipherMethod)
    {
        $this->encryptMethod = $cipherMethod;
    }
}
