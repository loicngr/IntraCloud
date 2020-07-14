<?php

namespace App\Encrypt;

/**
 * Source: https://gist.github.com/ve3/0f77228b174cf92a638d81fddb17189d
 * */

class Encryption
{
    /**
     * @var string La méthode Cipher
     */
    protected $encryptMethod = 'AES-256-CBC';

    /**
     * Fonction qui décrypte une chaine de caractères
     *
     * @param string $encryptedString La chaine de caractère déjà encrypté et en base64
     * @param $key
     * @return mixed
     */
    public function decrypt($encryptedString, $key)
    {
        $json = json_decode(base64_decode($encryptedString), true);

        try {
            $salt = hex2bin($json["salt"]);
            $iv = hex2bin($json["iv"]);
        } catch (Exception $e) {
            return dump($e);
        }

        $cipherText = base64_decode($json['ciphertext']);

        $iterations = intval(abs($json['iterations']));
        if ($iterations <= 0) {
            $iterations = 999;
        }

        $hashKey = hash_pbkdf2('sha512', $key, $salt, $iterations, $this->encryptMethodLength() / 4);
        unset($iterations, $json, $salt);

        $decrypted = openssl_decrypt($cipherText, $this->encryptMethod, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
        unset($cipherText, $hashKey, $iv);

        return $decrypted;
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
}
