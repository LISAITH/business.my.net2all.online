<?php

namespace App\Controller;

use App\Entity\ApiServices;
use App\Repository\AffiliationsRepository;
use App\Repository\ApiServicesRepository;
use App\Repository\ApiCommunautesRepository;
use App\Repository\EnseignesRepository;
use App\Repository\JoinEnseigneRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class AffiliationController extends AbstractController
{
    #[Route('/api/api_services/{id_entreprise}/{id_service}', name: 'get-affiliation')]
    public function index($id_entreprise, $id_service, SerializerInterface $serializer, ApiServicesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_affiliation = $affiRepo->findBy(['id_entreprise' => $id_entreprise, 'id_services'=>$id_service]);
        $f = array_map(function($value){return $value->getIdEnseigne();},$all_enseigne_affiliation);
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($f, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route('/api/api_services/all_data/{id_entreprise}/{id_service}', name: 'get-affiliation-all-data')]
    public function apiService($id_entreprise, $id_service, SerializerInterface $serializer, ApiServicesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_affiliation = $affiRepo->findBy(['id_entreprise' => $id_entreprise, 'id_services'=>$id_service]);
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($all_enseigne_affiliation, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route('/api/api_services/one/{id_enseigne}/{id_service}', name: 'get-one-affiliation')]
    public function one($id_enseigne, $id_service, SerializerInterface $serializer, ApiServicesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_affiliation = $affiRepo->findOneBy(['id_enseigne' => $id_enseigne, 'id_services' => $id_service]);

        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($all_enseigne_affiliation, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/api_services/update/{id_enseigne}/{id_service}', name: 'update-afiiliation')]
    public function update($id_enseigne, $id_service, SerializerInterface $serializer, ApiServicesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $ens = $affiRepo->findBy(['id_entreprise' => $id_enseigne]);
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($ens, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/api_services/install/{id_enseigne}/{id_service}/{type}', name: 'afiiliation-installation-demande')]
    public function install($id_enseigne, $id_service, $type, ManagerRegistry $doctrine, SerializerInterface $serializer, ApiServicesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $ens = $affiRepo->findOneBy(['id_enseigne' => $id_enseigne, 'id_services' => $id_service]);
        $ens->setIsInstalled(true);
        $ens->setInstallationStatus($type);
        $em = $doctrine->getManager();
        $em->persist($ens);
        $em->flush();
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize("200", EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/treat/{id}', name: 'afiiliation-treate-demande', methods: ["GET"])]
    public function treat($id, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer, ApiServicesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $data = json_decode($request->getContent());
        if ($data->magmaapikey && $data->api_url) {
            $ens = $affiRepo->find($id);
            $ens->setInstallationStatus(3);
            $ens->setIsTreated(true);
            $ens->setBaseurl('' .$data->api_url);
            $ens->setApiKey('' . $data->magmaapikey);
            $em = $doctrine->getManager();
            $em->persist($ens);
            $em->flush();
        }
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($data, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/admin_services/{id_service}', name: 'apiservice-type')]
    public function type($id_service, SerializerInterface $serializer, ApiServicesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_api_services = $affiRepo->findBy(['id_services' => $id_service]);
        $all_enseignes = [];
        foreach ($all_api_services as $api_service) {
            array_push($all_enseignes, $ensRepo->find($api_service->getIdEnseigne()));
        }


        return new JsonResponse(
            [
                'enseignes' => json_decode($serializer->serialize($all_enseignes, EncoderJsonEncoder::FORMAT)),
                'api_services' => json_decode($serializer->serialize($all_api_services, EncoderJsonEncoder::FORMAT))
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/join_enseignes/{id_user}/{type_user}/{id_service}', name: 'get-joined')]
    public function joined($id_user, $type_user, $id_service, SerializerInterface $serializer, JoinEnseigneRepository $joinRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_joined = $joinRepo->findBy(['user_id' => $id_user, 'service_id' => $id_service, 'type_user' => '' . $type_user]);
        $all_enseigne_founded = [];
        foreach ($all_enseigne_joined as $joined) {
            if ($joined->getUserId() == $id_user) {
                array_push($all_enseigne_founded, $joined->getEnseigneId());
            }
        }
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($all_enseigne_founded, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }


    #communaute


    #[Route('/api/api_communautes/{id_entreprise}/{id_communaute}', name: 'get-affiliation-communaute')]
    public function index_c($id_entreprise, $id_communaute, SerializerInterface $serializer, ApiCommunautesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_affiliation = $affiRepo->findBy(['id_entreprise' => $id_entreprise]);
        $all_enseigne_founded = [];
        foreach ($all_enseigne_affiliation as $affilie) {
            if ($affilie->getIdCommunautes() == $id_communaute) {
                array_push($all_enseigne_founded, $affilie->getIdEnseigne());
            }
        }
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($all_enseigne_founded, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/api_communautes/all_data/{id_entreprise}/{id_communaute}', name: 'get-communautes-affiliation-all-data')]
    public function apiCommunautes($id_entreprise, $id_communaute, SerializerInterface $serializer, ApiCommunautesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_affiliation = $affiRepo->findBy(['id_entreprise' => $id_entreprise, 'id_communautes'=>$id_communaute]);
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($all_enseigne_affiliation, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route('/api/api_communautes/one/{id_enseigne}/{id_communaute}', name: 'get-one-affiliation-communaute')]
    public function one_c($id_enseigne, $id_communaute, SerializerInterface $serializer, ApiCommunautesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_affiliation = $affiRepo->findOneBy(['id_enseigne' => $id_enseigne, 'id_communautes' => $id_communaute]);

        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($all_enseigne_affiliation, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/api_communautes/update/{id_enseigne}/{id_communaute}', name: 'update-afiiliation-communaute')]
    public function update_c($id_enseigne, $id_communaute, SerializerInterface $serializer, ApiCommunautesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $ens = $affiRepo->findBy(['id_entreprise' => $id_enseigne]);
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($ens, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/api_communautes/install/{id_enseigne}/{id_communaute}/{type}', name: 'afiiliation-installation-demande-communaute')]
    public function install_c($id_enseigne, $id_communaute, $type, ManagerRegistry $doctrine, SerializerInterface $serializer, ApiCommunautesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $ens = $affiRepo->findOneBy(['id_enseigne' => $id_enseigne, 'id_communautes' => $id_communaute]);
        $ens->setIsInstalled(true);
        $ens->setInstallationStatus($type);
        $em = $doctrine->getManager();
        $em->persist($ens);
        $em->flush();
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize("200", EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/treat_c/{id}', name: 'afiiliation-treate-demande-communaute', methods: 'GET')]
    public function treate_c($id, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer, ApiCommunautesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        if ($request->query->get("magmaapikey") && $request->query->get("api_url")) {
            $ens = $affiRepo->find($id);
            $ens->setInstallationStatus(3);
            $ens->setIsTreated(true);
            $ens->setBaseurl('' . $request->query->get("api_url"));
            $ens->setApiKey('' . $request->query->get("magmaapikey"));
            $em = $doctrine->getManager();
            $em->persist($ens);
            $em->flush();
        }
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($request->query->get("magmaapikey"), EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/admin_communautes/{id_communaute}', name: 'apiservice-type-communaute')]
    public function type_c($id_communaute, SerializerInterface $serializer, ApiCommunautesRepository $affiRepo, EnseignesRepository $ensRepo): Response
    {
        $all_api_communautes = $affiRepo->findBy(['id_communautes' => $id_communaute]);
        $all_enseignes = [];
        foreach ($all_api_communautes as $api_communaute) {
            array_push($all_enseignes, $ensRepo->find($api_communaute->getIdEnseigne()));
        }


        return new JsonResponse(
            [
                'enseignes' => json_decode($serializer->serialize($all_enseignes, EncoderJsonEncoder::FORMAT)),
                'api_communautes' => json_decode($serializer->serialize($all_api_communautes, EncoderJsonEncoder::FORMAT))
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }

    #[Route('/api/join_enseignes_communaute/{id_user}/{type_user}/{id_communaute}', name: 'get-joined-communaute')]
    public function joined_c($id_user, $type_user, $id_communaute, SerializerInterface $serializer, JoinEnseigneRepository $joinRepo, EnseignesRepository $ensRepo): Response
    {
        $all_enseigne_joined = $joinRepo->findBy(['user_id' => $id_user, 'service_id' => $id_communaute, 'type_user' => '' . $type_user]);
        $all_enseigne_founded = [];
        foreach ($all_enseigne_joined as $joined) {
            if ($joined->getUserId() == $id_user) {
                array_push($all_enseigne_founded, $joined->getEnseigneId());
            }
        }
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($all_enseigne_founded, EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );

    }
}
