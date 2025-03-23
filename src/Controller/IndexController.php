<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use App\Repository\InfoRepository;
use App\Repository\PartnersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


final class IndexController extends AbstractController
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
}
