<?php

namespace App\Controller\Api\Server;

use App\Entity\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Api\Server\ServerController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class NewServerController extends ServerController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * Créé un nouveau serveur
     * @Route("/server", name="new_server", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function newServer(Request $request)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        if (
            $request->request->get('name') !== null &&
            $request->request->get('adresse') !== null &&
            $request->request->get('port') !== null &&
            $request->request->get('username') !== null &&
            $request->request->get('password') !== null
        ):
            $responseRequest['data'] = "You don't have the authorization.";

            if ($this->isGranted("ROLE_ADMIN")):
                $rqName = $this->encryption->decrypt($request->request->get('name'));
                $rqAdresse = $this->encryption->decrypt($request->request->get('adresse'));
                $rqPort = $this->encryption->decrypt($request->request->get('port'));
                $rqUsername = $this->encryption->decrypt($request->request->get('username'));
                $rqAcceptedRoles = $this->encryption->decrypt($request->request->get('acceptedRoles'));
                $rqDefaultPath = ($request->request->get('defaultPath') !== null)? $this->encryption->decrypt($request->request->get('defaultPath')) : '/var/www/html';

                if ($rqAcceptedRoles) $rqAcceptedRoles = json_decode($rqAcceptedRoles);

                $server = $this->serverRepository->findBy(['name' => $rqName]);

                if (isset($server) && !empty($server)):
                    $responseRequest = [
                        'status' => 404,
                        'data' => 'server already created.',
                    ];
                else:
                    $server = new Server();

                    $privateKeyEncoded = null;
                    $passphraseEncoded = null;

                    if ($request->request->get('privateKey') !== null):
                        /*
                         * La privateKey et la Passphrase sont déjà encryptés via l'interface
                         * */
                        $privateKeyEncoded = $request->request->get('privateKey');

                        if ($request->request->get('passphrase') !== null):
                            $passphraseEncoded = $request->request->get('passphrase');
                        endif;
                    endif;

                    $server
                        ->setName($rqName)
                        ->setAdresse($rqAdresse)
                        ->setPort($rqPort)
						->setAcceptedRoles($rqAcceptedRoles)
                        ->setPassword($request->request->get('password'))
                        ->setDefaultPath($rqDefaultPath)
                        ->setPrivateKey($privateKeyEncoded)
                        ->setPassphrase($passphraseEncoded)
                        ->setUsername($rqUsername);

                    try {
                        $this->manager->persist($server);
                        $this->manager->flush();
                        $responseRequest = [
                            'status' => 200,
                            'data' => 'server created.',
                        ];
                    } catch (\Throwable $th) {
                        $responseRequest = [
                            'status' => 404,
                            'data' => 'server not created.',
                        ];
                    }
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
