<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Entity\Poi;
use App\Form\AddPlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SceneController extends AbstractController
{
    #[Route('/add-scene', name: 'add_scene', methods: ['POST'])]
    public function addScene(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        
        // Get field values - handle both direct and nested form fields
        $nom = null;
        $longitude = null;
        $latitude = null;
        
        // Check direct field names
        if ($request->request->has('nom')) {
            $nom = $request->request->get('nom');
        }
        if ($request->request->has('longitude')) {
            $longitude = $request->request->get('longitude');
        }
        if ($request->request->has('latitude')) {
            $latitude = $request->request->get('latitude');
        }
        
        // Check for nested form fields
        foreach ($request->request->all() as $key => $value) {
            if (is_array($value)) {
                if (isset($value['nom']) && empty($nom)) {
                    $nom = $value['nom'];
                }
                if (isset($value['longitude']) && empty($longitude)) {
                    $longitude = $value['longitude'];
                }
                if (isset($value['latitude']) && empty($latitude)) {
                    $latitude = $value['latitude'];
                }
            }
        }
        
        if (!$nom || !$longitude || !$latitude) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        try {
            // First, create the POI for the scene
            $presetProperties = [
                'popup' => $nom,
                'type' => 'scène',
                'marker-color' => '#d21e96',
                'marker-symbol' => 'theatre',
                'image' => 'random'
            ];

            // Store coordinates in GeoJSON format
            $geoJson = json_encode([
                'type' => 'Point',
                'coordinates' => [(float)$longitude, (float)$latitude]
            ]);

            // Insert the new POI for the scene
            $sql = 'INSERT INTO poi (type, properties, geometry, time_stamp) VALUES (:type, :properties, ST_GeomFromGeoJSON(:geometry), :time_stamp)';
            $stmt = $entityManager->getConnection()->prepare($sql);
            
            // Use proper parameter binding instead of passing an array
            $stmt->bindValue('type', 'Feature');
            $stmt->bindValue('properties', json_encode($presetProperties));
            $stmt->bindValue('geometry', $geoJson);
            $stmt->bindValue('time_stamp', (new \DateTime())->format('Y-m-d'));
            $stmt->executeStatement();
            
            // Get the ID of the newly inserted POI
            $poiId = $entityManager->getConnection()->lastInsertId();
            
            // Then create a Scene entity linked to this POI
            $scene = new Scene();
            $scene->setNom($nom);
            
            // Create a Poi entity reference to link to the Scene
            $poi = $entityManager->getReference(Poi::class, $poiId);
            $scene->setPoiFK($poi);
            
            // Set timestamp
            $scene->setTimeStamp(new \DateTime());
            
            $entityManager->persist($scene);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Scène ajoutée avec succès.',
                'sceneId' => $scene->getId(),
                'poiId' => $poiId
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}