<?php


namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class ApiFileUploader extends AbstractController
{
    private $targetDirectory;
    private $slugger;
    private $params;

    public function __construct(ParameterBagInterface $params, SluggerInterface $slugger)
    {
        $this->params = $params;
        $this->targetDirectory = $this->params->get('default_directory');
        $this->slugger = $slugger;
    }

    #[Route('/api/upload', name: 'upload', methods: ["POST"])]
    public function upload(Request $request, SerializerInterface $serializer): JsonResponse
    {
        /// Create an new validator
        $validator = Validation::createValidator();

        $constraints = new Assert\Collection([
            "target_directory" => [
                new Assert\Optional([new Assert\Choice(["enseignes_directory", "default_directory", "profile_directory" ])]),
            ]
        ]);

        /// Process validation
        $errors = $validator->validate($request->request->all(), $constraints);

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

        if (count($request->files) == 0) {
            return new JsonResponse(
                [
                    "code" => Response::HTTP_BAD_REQUEST,
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        $this->targetDirectory = $request->get('target_directory') ? $this->params->get($request->get('target_directory')) : $this->targetDirectory;

        $filesUrl = [];
        foreach ($request->files as $key => $file) {
            $filesUrl[$key] = "/uploads/" . $this->getTargetDirectoryName() . "/" . $this->_upload($file);
        }

        return new JsonResponse(
            [
                'data' => [
                    'urls' => $filesUrl
                ],
            ],
            Response::HTTP_CREATED,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    public function _upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    private function getTargetDirectoryName(): string
    {
        return basename($this->targetDirectory);
    }
}