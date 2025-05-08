<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Poi;
use App\Repository\PoiRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class PoiController extends AbstractController
{
    #[Route('/create-poi', name: 'create_poi', methods: ['POST'])]
    public function createPoi(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['id'], $data['type'], $data['properties'], $data['geometry'])) {
            return new JsonResponse(['error' => 'Invalid payload'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $poi = new Poi();
        $poi->setId($data['id']); // Manually assign the ID
        $poi->setType($data['type']);
        $poi->setProperties($data['properties']);
        $poi->setGeometry($data['geometry']);

        $entityManager->persist($poi);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Poi created successfully'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/poi', name: 'app_poi', methods: ['GET'])]
    public function getData(EntityManagerInterface $entityManager): JsonResponse
    {
        // Use Doctrine's DBAL connection to execute a raw SQL query
        $connection = $entityManager->getConnection();
        $sql = 'SELECT id, type, properties, ST_AsGeoJSON(geometry) AS geometry FROM poi';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();

        // Build the GeoJSON structure
        $geoJson = [
            'type' => 'FeatureCollection',
            'features' => array_map(function ($row) {
                return [
                    'type' => 'Feature',
                    'id' => $row['id'],
                    'geometry' => json_decode($row['geometry'], true), // Decode GeoJSON
                    'properties' => json_decode($row['properties'], true), // Decode JSON properties
                ];
            }, $result),
        ];

        return new JsonResponse($geoJson, JsonResponse::HTTP_OK);
    }
}
