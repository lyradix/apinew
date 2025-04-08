<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Entity\User;
use App\Repository\ArtistRepository;
use App\Repository\InfoRepository;
use App\Repository\PartnersRepository;
use App\Repository\SceneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


final class IndexController extends ApiController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/concert', name: 'app_index')]
    public function getData(ArtistRepository $ArtistRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $ArtistRepository->findAllWithScenes();
        $jsonData = $serializer->serialize($data, 'json', ['groups' => ['artist:read', 'scene:read']]);
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/concert/{id}', name: 'app_index_id')]
    public function getDataById(ArtistRepository $ArtistRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $data = $ArtistRepository->findOneBy(['id' => $id]);
        if (!$data) {
            return new JsonResponse(['error' => 'Concert not found'], Response::HTTP_NOT_FOUND);
        }
        $jsonData = $serializer->serialize($data, 'json', ['groups' => ['artist:read', 'scene:read']]);
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/scenes', name: 'app_scenes', methods: ['GET'])]
    public function getScenes(SceneRepository $sceneRepository, SerializerInterface $serializer): JsonResponse
    {
        // Fetch all scenes from the database
        $scenes = $sceneRepository->findAll();
    
        // Serialize the scenes using the 'scene:read' group
        $jsonData = $serializer->serialize($scenes, 'json', ['groups' => ['scene:read']]);
    
        // Return the serialized data as a JSON response
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/updateconcert', name: 'app_updateconcert', methods: ['PUT'])]
    public function putConcert(HttpFoundationRequest $request,
        ArtistRepository $ArtistRepository, 
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['id'])) {
            return new JsonResponse(['error' => 'Scene ID is required'], Response::HTTP_BAD_REQUEST);
        }
        $concert = $ArtistRepository->find($data['id']);

        if (!$concert) {
            return new JsonResponse(['error' => 'Concert not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['startTime'])) {
            try {
                $concert->setStartTime(new \DateTime($data['startTime']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid startTime format'], Response::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['endTime'])) {
            try {
                $concert->setEndTime(new \DateTime($data['endTime']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid endTime format'], Response::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['sceneFK'])) {
            $scene = $entityManager->getRepository(Scene::class)->find($data['sceneFK']);
            if (!$scene) {
                return new JsonResponse(['error' => 'Scene not found'], Response::HTTP_NOT_FOUND);
            }
            $concert->setSceneFK($scene); // Assuming `setSceneFK` is the setter for the Scene relationship
        }
    
        $entityManager->persist($concert);
        $entityManager->flush();

        $jsonData = $serializer->serialize($concert, 'json', ['groups' => ['artist:read', 'scene:read']]);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }


    #[Route('/partners', name: 'app_partners')]
    public function getPartners(PartnersRepository $PartnersRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $PartnersRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json', ['partners:read']);
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/info', name: 'app_info')]
    public function getInfo(InfoRepository $InfoRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $InfoRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json', ['info:read']);
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/currentuser', name: 'app_current_user', methods: ['GET'])]
    public function getCurrentUser(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not logged in'], Response::HTTP_UNAUTHORIZED);
        }

        $jsonData = $serializer->serialize($user, 'json', ['groups' => ['user:read']]);
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/users', name: 'app_all_users', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();

        $jsonData = $serializer->serialize($users, 'json', ['groups' => ['user:read']]);
        return new JsonResponse($jsonData, 200, [], true);
    }
}
