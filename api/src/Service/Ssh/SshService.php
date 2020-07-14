<?php
namespace App\Service\Ssh;

use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;
use App\Service\Encrypt\Encryption;
use phpDocumentor\Reflection\Types\Boolean;

class SshService
{
    /**
     * Fonction qui execute une commande unix
     * @param string $cmd
     * @param string $host
     * @param int $port
     * @param string $login
     * @param string $password
     * @param string|null $privateKey
     * @param string|null $passphrase
     * @return false|string
     */
    public function exec(
        string $cmd,
        string $host,
        int $port,
        string $login,
        string $password,
        ?string $privateKey = null,
        ?string $passphrase = null
    ) {
        $responseService = [
            'status' => false,
            'data' => 'credentials variables not set.',
        ];
        if (isset($cmd) && isset($host) && isset($port) && isset($login) && isset($password)):
            $encryptionService = new Encryption();

            $ssh = $this->connection(
                $host,
                $port,
                $login,
                $encryptionService->decrypt($password),
                $privateKey !== null ? $encryptionService->decrypt($privateKey) : $privateKey,
                $passphrase !== null ? $encryptionService->decrypt($passphrase) : $passphrase
            );

            if (!$ssh) {
                $responseService['data'] = 'login failed.';
            } else {
                $responseService['status'] = true;
                $responseService['data'] = json_encode($ssh->exec($cmd));
            }
            $ssh = null;
        endif;

        return json_encode($responseService);
    }

    /**
     * Fonction de connexion à un serveur SSH
     * @param string $host
     * @param int $port
     * @param string $login
     * @param string $password
     * @param string|null $privateKey
     * @param string|null $passphrase
     * @return bool|SSH2
     */
    protected function connection(
        string $host,
        int $port,
        string $login,
        string $password,
        ?string $privateKey,
        ?string $passphrase
    ) {
        $ssh = new SSH2($host, $port);
        if ($privateKey !== null):
            $key = new RSA();
            if ($passphrase !== null):
                $key->setPassword($passphrase);
            endif;
            $key->loadKey($privateKey);

            if (!$ssh->login($login, $key)):
                return false;
            endif;
        else:
            if (!$ssh->login($login, $password)):
                return false;
            endif;
        endif;
        return $ssh;
    }
}
