<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Entity\Token;
use App\Repository\TokenRepository;
use App\Service\Token\TokenService;
use App\Controller\Api\User\UserController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdateUserController extends UserController
{
    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * Mise un jour d'un utilisateur avec son ID
     * @Route("/user/update/{id}", name="update_user", methods={"POST"})
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateUser(Request $request, $id)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            $uid = intval($id);

            $responseRequest['data'] = "parameters not found.";

            $parametersCollection = [];
            foreach ($request->request as $key => $value) {
                if (!empty($value)):
                    $parametersCollection[] = $key;
                endif;
            }

            if (!empty($parametersCollection)):
                $responseRequest['data'] = "You don't have the authorization.";

                if ($this->getUser()->getId() === $uid || $this->isGranted("ROLE_ADMIN")):
                    /** @var User $user */
                    $user = $this->userRepository->find($uid);

                    $responseRequest['data'] = 'user not found.';
                    if (!empty($user)):
                        $checkModified = false;

                        if (in_array("password", $parametersCollection)):
                            $checkModified = true;
                            $user->setPassword($this->encoder->encodePassword($user, $request->request->get('password')));
                        endif;
                        if (in_array("firstname", $parametersCollection)):
                            $checkModified = true;
                            $user->setFirstName($request->request->get('firstname'));
                        endif;
                        if (in_array("lastname", $parametersCollection)):
                            $checkModified = true;
                            $user->setLastName($request->request->get('lastname'));
                        endif;
                        if (in_array("email", $parametersCollection)):
                            $checkModified = true;
                            $user->setEmail($request->request->get('email'));
                        endif;
                        if (in_array("role", $parametersCollection)):
                            if ($this->isGranted("ROLE_ADMIN")):
                                $checkModified = true;
                                $rqRole = $request->request->get('role');

                                if ($rqRole === 'ROLE_NOT_VERIFIED') {
                                    $user->setRoles(['ROLE_NOT_VERIFIED']);
                                } else {
                                    $user->setRoles([$rqRole]);
                                }
                            endif;
                        endif;

                        if ($checkModified):
                            try {
                                $user->setUpdatedAt(new \DateTime());
                                $this->manager->persist($user);
                                $this->manager->flush();

                                $responseRequest = [
                                    'status' => 200,
                                    'data' => 'user updated.',
                                ];
                            } catch (\Throwable $th) {
                                $responseRequest = [
                                    'status' => 404,
                                    'data' => 'user not updated.',
                                ];
                            }
                        endif;
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * L'utilisateur demande a mettre à jour sont mot de passe via la page de réinitialisation du mot de passe
     * @Route("/user/update/resetpassword/{token}", name="update_reset_password", methods={"POST"})
     * @param Request $request
     * @param $token
     * @param TokenService $tokenService
     * @param TokenRepository $tokenRepository
     * @return JsonResponse
     */
    public function updateResetPassword(Request $request, $token, TokenService $tokenService, TokenRepository $tokenRepository)
    {
        $responseRequest = [
            'status' => 404,
            'data' => 'parameters not found.',
        ];

        /** Adresse Ip du Client */
        $ip_adresse = $request->getClientIp();

        if ($request->request->get('password') != null && !empty($token) && $request->request->get('email') != null):
            $responseRequest['data'] = 'token not found.';

            /** @var Token */
            $token = $tokenRepository->findOneByTokenData($token);
            if (!empty($token)):
                $responseRequest['data'] = 'ip adresse not valid.';
                if ($ip_adresse === $token->getIpAdress()):
                    $responseRequest['data'] = 'user not found.';

                    /** @var User */
                    $user = $token->getUser();
                    if (!empty($user)):
                        $responseRequest['data'] = 'email not valid.';
                        if ($user->getEmail() === $request->request->get('email')):
                            // Vérification de la date Token
                            $responseDateToken = json_decode($tokenService->checkDateToken($token));

                            $responseRequest['data'] = $responseDateToken->data;
                            if ($responseDateToken->status === 200):
                                $password = $this->encoder->encodePassword($user, $request->request->get('password'));
                                try {
                                    $this->userRepository->upgradePassword($user, $password);
                                } catch (\Throwable $th) {
                                    $responseRequest['data'] = 'password not updated.';
                                    return $this->json($responseRequest);
                                }
                                $responseDeleteToken = json_decode($tokenService->removeToken($token, $this->manager));
                                $responseRequest['data'] = 'token not deleted.';
                                if ($responseDeleteToken->status === 200):
                                    $responseRequest = [
                                        'status' => 200,
                                        'data' => 'password updated.',
                                    ];
                                endif;
                            else:
                                $this->manager->remove($token);
                                $this->manager->flush();
                            endif;
                        endif;
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * L'utilisateur demande a mettre à jour sont mot de passe via la page des paramètres
     * @Route("/user/update/{id}/password", name="update_password", methods={"POST"})
     * @param Request $request
     * @param $id
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function updatePassword(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            $uid = intval($id);

            if ($this->getUser()->getId() === $uid || $this->isGranted("ROLE_ADMIN")):
                $responseRequest['data'] = "parameters not found.";
                if ($request->request->get('current') !== null && $request->request->get('new') !== null):
                    /** @var User */
                    $user = $this->getUser();

                    $responseRequest['data'] = "password not updated.";
                    if ($passwordEncoder->isPasswordValid($user, $request->request->get('current'))):
                        $encodedPassword = $passwordEncoder->encodePassword($user, $request->request->get('new'));
                        $this->userRepository->upgradePassword($user, $encodedPassword);
                        $responseRequest = [
                            'status' => 200,
                            'data' => 'password updated.',
                        ];
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }

    /**
     * @IsGranted("ROLE_USER", statusCode=404, message="You don't have the authorization.")
     *
     * L'utilisateur demande a mettre à jour sont Email via la page des paramètres
     * @Route("/user/update/{id}/email", name="update_email", methods={"POST"})
     * @param Request $request
     * @param $id
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function updateEmail(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder)
    {
        $responseRequest = [
            'status' => 404,
            'data' => '{ID} parameter is not a number.',
        ];

        if (is_numeric($id)):
            $uid = intval($id);

            /** @var User */
            $user = $this->userRepository->find($this->getUser()->getId());

            if ($user->getId() === $uid || $this->isGranted("ROLE_ADMIN")):
                $responseRequest['data'] = "parameters not found.";
                if ($request->request->get('current') !== null && $request->request->get('new') !== null):
                    $responseRequest['data'] = "email not updated.";
                    if ($user->getUsername() === $request->request->get('current')):
                        $user->setEmail($request->request->get('new'));
                        try {
                            $this->manager->persist($user);
                            $this->manager->flush();

                            $responseRequest = [
                                'status' => 200,
                                'data' => 'email updated.',
                            ];
                        } catch (\Exception $e) {
                            $responseRequest = [
                                'status' => 404,
                                'data' => 'email not updated.',
                                'error_code' => json_encode($e),
                            ];
                        }
                    endif;
                endif;
            endif;
        endif;

        $encryptedResponse = $this->encryption->encrypt(json_encode($responseRequest));
        return $this->json($encryptedResponse);
    }
}
