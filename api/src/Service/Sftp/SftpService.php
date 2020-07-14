<?php
namespace App\Service\Sftp;

use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
use App\Service\Encrypt\Encryption;
use Symfony\Component\Filesystem\Filesystem;

class SftpService
{
    /**
     * @param string $remotefile
     * @param string $localfile
     * @param string $host
     * @param int $port
     * @param string $login
     * @param string $password
     * @param string|null $privateKey
     * @param string|null $passphrase
     * @return false|string
     */
    public function upload(
        string $remotefile,
        string $localfile,
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
        if (isset($remotefile) && isset($localfile) && isset($host) && isset($port) && isset($login) && isset($password)):
            $encryptionService = new Encryption();
            $sftp = $this->connection(
                $host,
                $port,
                $login,
                $encryptionService->decrypt($password),
                $privateKey !== null ? $encryptionService->decrypt($privateKey) : $privateKey,
                $passphrase !== null ? $encryptionService->decrypt($passphrase) : $passphrase
            );

            if (!$sftp) {
                $responseService['data'] = 'login failed.';
            } else {
                $responseService = [
                    'status' => true,
                    'data' => $sftp->put($remotefile, $localfile),
                ];
            }
            $sftp = null;
        endif;

        return json_encode($responseService);
    }

    public function zipFolder(
        string $filePath,
        string $zipFilename,
        string $zipTarget,
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
        if (
            isset($filePath) &&
            isset($zipFilename) &&
            isset($zipTarget) &&
            isset($host) &&
            isset($port) &&
            isset($login) &&
            isset($password)
        ):
            $encryptionService = new Encryption();
            $sftp = $this->connection(
                $host,
                $port,
                $login,
                $encryptionService->decrypt($password),
                $privateKey !== null ? $encryptionService->decrypt($privateKey) : $privateKey,
                $passphrase !== null ? $encryptionService->decrypt($passphrase) : $passphrase
            );

            if (!$sftp) {
                $responseService['data'] = 'login failed.';
            } else {
                $filesystem = new Filesystem();
                $tmpFolder = sys_get_temp_dir();

                // Création du Fichier .zip dans le dossier temporaire, par défaut "/tmp"
                // il faut être sur que zip est installé dans le serveur
                $sftp->exec("cd $filePath && zip -r $tmpFolder/$zipFilename.zip $zipTarget");

                $localTmpPath = $_SERVER['DOCUMENT_ROOT'];
                $localTmpPath = explode('/', $localTmpPath);

                array_pop($localTmpPath);
                array_pop($localTmpPath);
                array_pop($localTmpPath);

                $localTmpPath = implode('/', $localTmpPath);
                $localTmpPath .= '/tmp';

                if (!$filesystem->exists($localTmpPath)):
                    $filesystem->mkdir($localTmpPath);
                endif;

                // Copie du fichier via le serveur en SFTP dans le serveur Symfony
                $sftp->get("$tmpFolder/$zipFilename.zip");
                $sftp->get("$tmpFolder/$zipFilename.zip", "$localTmpPath/$zipFilename.zip");

                $responseService = [
                    'status' => true,
                    'data' => "$localTmpPath/$zipFilename.zip",
                ];
            }
            $sftp = null;
        endif;

        return json_encode($responseService);
    }

    /**
     * Fonction de connexion à un serveur SFTP
     * @param string $host
     * @param int $port
     * @param string $login
     * @param string $password
     * @param string|null $privateKey
     * @param string|null $passphrase
     * @return bool|SFTP
     */
    protected function connection(
        string $host,
        int $port,
        string $login,
        string $password,
        ?string $privateKey,
        ?string $passphrase
    ) {
        $ssh = new SFTP($host, $port);
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
