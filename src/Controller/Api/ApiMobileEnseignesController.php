<?php


namespace App\Controller\Api;


use App\Entity\Enseignes;
use App\Repository\EnseignesRepository;
use App\Repository\EntreprisesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ApiMobileEnseignesController extends AbstractController
{
    #[Route('/api/mobile/enseignes', name: 'mobile_create_enseignes', methods: ["POST"])]
    public function create(Request $request, SerializerInterface $serializer, EnseignesRepository $enseignesRepository, EntreprisesRepository $entreprisesRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        /// Create an new validator
        $validator = Validation::createValidator();

        $data = json_decode($request->getContent());

        if ($data == null) {
            return new JsonResponse(
                [
                    'error' => "INVALID_REQUEST_DATA",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        /// Array that represent a list of constraint
        $constraint = [
            "entreprise_id" => [
                new Assert\NotBlank(),
                new Assert\Type("integer")
            ],
            "nom_enseigne" => [
                new Assert\NotBlank(),
                new Assert\Length(["min" => 1])
            ],
            "url_image" => [
                new Assert\NotBlank(),
            ],
            "phone" => [
                new Assert\Optional(),
            ],
        ];

        // Create [Assert\Collection] to validate request data
        $constraints = new Assert\Collection($constraint);

        /// Process validation
        $errors = $validator->validate(json_decode($request->getContent(), true), $constraints);

        /// If not validated return [Bad Request] response with detected errors
        if ($errors->count()) {
            return new JsonResponse(
                [
                    "code" => Response::HTTP_BAD_REQUEST,
                    'error' => $serializer->serialize($errors, "json"),
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        $e = $enseignesRepository->findOneBy(["nom_enseigne"=>$data->nom_enseigne]);
        if ($e != null) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_NOT_ACCEPTABLE,
                    'message' => "RESOURCE_ALREADY_EXIST",
                ],
                Response::HTTP_NOT_ACCEPTABLE,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        $entreprise = $entreprisesRepository->find($data->entreprise_id);

        if ($entreprise == null) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => "ENTERPRISE_NOT_FOUND",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        $enseigne = new Enseignes();
        $enseigne->setNomEnseigne($data->nom_enseigne);
        $enseigne->setEntreprise($entreprise);
        $enseigne->setStatus(true);
        $enseigne->setUrlImage($data->url_image);
        $enseigne->setIsValidated(false);
        $enseigne->setPhone($data->phone ?? '');
        $enseigne->setCodeEnseigne("Tmp-net2all-" . $this->getRandomText(10));

        /// Save Enseignes to database
        $entityManager->persist($enseigne);

        try {
            $entityManager->flush();
            return new JsonResponse(
                [
                    'message' => "RESOURCE_CREATED",
                    'data' => json_decode($serializer->serialize($enseigne, EncoderJsonEncoder::FORMAT)),
                ],
                Response::HTTP_CREATED,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        } catch (Exception) {
            return new JsonResponse(
                [
                    "code" => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "ERROR",
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }
    }

    public function getRandomText($n): string
    {
        $characters = 'AZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}