<?php

namespace App\Controller\Api\Server;

use App\Entity\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Api\Server\ServerController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UpdateServerController extends ServerController
{
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="You don't have the authorization.")
     *
     * Mise à jour d'un serveur
     * @Route("/server/{id}/update", name="update_server", methods={"POST"})
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateServer(Request $request, $id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            $server_id = intval($id);

            if (
                $request->request->get('username') !== null ||
                $request->request->get('password') !== null ||
                $request->request->get('adresse') !== null ||
                $request->request->get('privateKey') !== null ||
                $request->request->get('defaultPath') !== null ||
                $request->request->get('passphrase') !== null ||
                $request->request->get('name') !== null ||
                $request->request->get('port') !== null ||
				$request->request->get('acceptedRoles') !== null
            ):
                /** @var Server $server */
                $server = $this->serverRepository->findOneBy(['id' => $server_id]);

                $responseRequest = [
                    'status' => 404,
                    'data' => 'server no found.',
                ];

                /**
                    Vérification si un serveur à déjà le même nom
                 */
                if (isset($server) && !empty($server)):
                    if ($request->request->get('name') !== null):
                        $responseRequest = [
                            'status' => 404,
                            'data' => 'server already created.',
                        ];
                        $nameServer = $this->encryption->decrypt($request->request->get('name'));
                        $occServer = $this->serverRepository->findOneBy(['name' => $nameServer]);
                        if (!empty($occServer)):
                            $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
                            return $this->json($encryptedResponse);
                        endif;
                    endif;

                    if ($request->request->get('name') !== null):
                        $server->setName($this->encryption->decrypt($request->request->get('name')));
                    endif;

                    if ($request->request->get('username') !== null):
                        $server->setUsername($this->encryption->decrypt($request->request->get('username')));
                    endif;

                    if ($request->request->get('acceptedRoles') !== null):
						$rqAcceptedRoles = $this->encryption->decrypt($request->request->get('acceptedRoles'));
						if ($rqAcceptedRoles) $rqAcceptedRoles = json_decode($rqAcceptedRoles);
						$server->setAcceptedRoles($rqAcceptedRoles);
					endif;

                    if ($request->request->get('password') !== null):
                        $server->setPassword($request->request->get('password'));
                    endif;

                    if ($request->request->get('privateKey') !== null):
                        $server->setPrivateKey($request->request->get('privateKey'));
                    endif;

                    if ($request->request->get('passphrase') !== null):
                        $server->setPassphrase($request->request->get('passphrase'));
                    endif;

                    if ($request->request->get('port') !== null):
                        $server->setPort($this->encryption->decrypt($request->request->get('port')));
                    endif;

                    if ($request->request->get('adresse') !== null):
                        $server->setAdresse($this->encryption->decrypt($request->request->get('adresse')));
                    endif;

                    if ($request->request->get('defaultPath') !== null):
                        $server->setDefaultPath($this->encryption->decrypt($request->request->get('defaultPath')));
                    endif;

                    try {
                        $this->manager->persist($server);
                        $this->manager->flush();
                        $responseRequest = [
                            'status' => 200,
                            'data' => 'server updated.',
                        ];
                    } catch (\Throwable $th) {
                        $responseRequest = [
                            'status' => 404,
                            'data' => 'server not updated.',
                        ];
                    }
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
