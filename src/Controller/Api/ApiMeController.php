<?php

namespace App\Controller\Api;

use App\Repository\EntreprisesRepository;
use App\Repository\ParticuliersRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiMeController extends AbstractController
{
    #[Route(path: '/api/me', name: 'api_me', methods: ['POST'])]
    public function me(Request $request, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository, SerializerInterface $serializer, ParticuliersRepository $particuliersRepository, EntreprisesRepository $entreprisesRepository): JsonResponse
    {
        try {
            $decodedJwtToken = $jwtManager->decode($tokenStorageInterface->getToken());

            $user = $userRepository->find($decodedJwtToken["id"]);
            $entreprise = $entreprisesRepository->findOneBy(["user" => $user]);
            $particulier = $particuliersRepository->findOneBy(["user" => $user]);

            $d_user = json_decode($serializer->serialize($user, EncoderJsonEncoder::FORMAT));
            $d_user->type = $user->getType()->getId();

            $data = $serializer->serialize([
                'user' => $d_user,
                "particulier" => $particulier,
                "entreprise" => $entreprise,
            ], EncoderJsonEncoder::FORMAT);

            return new JsonResponse(
                ['data' => json_decode($data)],
                Response::HTTP_OK,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        } catch (JWTDecodeFailureException) {
            return new JsonResponse(
                [
                    'error' => "HTTP_FORBIDDEN",
                ],
                Response::HTTP_FORBIDDEN,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }
    }
}