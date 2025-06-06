<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Poi;
use App\Entity\Scene;
use App\Entity\User;
use App\Repository\PoiRepository;
use App\Repository\SceneRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class PoiController extends AbstractController
{
    #[Route('/create-poi', name: 'create_poi', methods: ['POST'])]
    public function createPoi(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->validateToken($request);

        if (!$user instanceof User) {
            return $user; // Return the error response from validateToken()
        }

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

    
    #[Route('/postPlace', name: 'app_Place', methods: ['POST'])]
    public function postCoordinates(
        HttpFoundationRequest $request,
        SerializerInterface $serializer,
        SceneRepository $ceneRepository,
        PoiRepository $poiRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

           $user = $this->validateToken($request);

        if (!$user instanceof User) {
            return $user; // Return the error response from validateToken()
        }

        $data = json_decode($request->getContent(), true);

        // Log the incoming request data
        error_log('Incoming request data: ' . json_encode($data));

        if (!isset($data['popup'])) {
            return new JsonResponse(['error' => 'Invalid data. "nom" is required.'], Response::HTTP_BAD_REQUEST);
        }
    
    

        // Validate required fields
        if (!isset($data['longitude']) || !isset($data['latitude']) ) {
            error_log('Validation failed: Missing required fields.');
            return new JsonResponse(['error' => 'Invalid data. "longitude", "latitude", " are required.'], Response::HTTP_BAD_REQUEST);
        }

        // Validate longitude and latitude
        if ($data['longitude'] < -180 || $data['longitude'] > 180 || $data['latitude'] < -90 || $data['latitude'] > 90) {
            error_log('Validation failed: Invalid longitude or latitude values.');
            return new JsonResponse(['error' => 'Invalid longitude or latitude values.'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['type'])) {
           error_log('Validation failed: Missing required fields.');
            return new JsonResponse(['error' => 'tHe type of service is required'], Response::HTTP_BAD_REQUEST);
        }
     
         

        $lastSceneId = $sceneRepository->findOneBy([], ['id' => 'DESC'])?->getId() ?? 0;
        // $lastPoiId = $poiRepository->findOneBy([], ['id' => 'DESC'])?->getId() ?? 0;
        // Log the validated data
        error_log('Validated data: ' . json_encode($data));

        $type = $data['type'] ?? null;

        try {
         
            
            // Preset values for properties
            $presetProperties = [
                // 'popup' => $popup,
                'type' => $type,
                'marker-color' => '#d21e96',
                'marker-symbol' => 'theatre',
                'image' => 'random'
            ];

            // Merge preset properties with incoming data
            $properties = $presetProperties;

            // Construct GeoJSON format
            $geoJson = json_encode([
                'type' => 'Point',
                'coordinates' => [(float)$data['longitude'], (float)$data['latitude']]
            ]);

            // Log the GeoJSON being set
            error_log('Setting geometry as GeoJSON: ' . $geoJson);

            // Insert a new Poi record using raw SQL
            $sql = 'INSERT INTO poi (type, properties, geometry) VALUES (:type, :properties, ST_GeomFromGeoJSON(:geometry))';
            $stmt = $entityManager->getConnection()->prepare($sql);
  
            // Log the SQL query and parameters
            error_log('Executing SQL: ' . $sql);
            error_log('SQL Parameters: ' . json_encode([
                'type' => 'Feature',
                'properties' => json_encode($properties),
                'geometry' => $geoJson
            ]));

            $stmt->executeStatement([
                'type' => 'Feature',
                'properties' => json_encode($properties),
                'geometry' => $geoJson
            ]);
            // Retrieve the last inserted ID for the Poi


            $lastInsertedPoiId = $entityManager->getConnection()->lastInsertId();
            error_log('Last inserted Poi ID: ' . $lastInsertedPoiId);
            // Log successful execution
            error_log('SQL executed successfully.');
              if (isset($data['type']) && $data['type'] === 'scène') {
                        //check scene id, create a new scene
                        $scene = $sceneRepository->findOneBy(['nom' => $data['nom']]);
                        if ($scene) {
                            return new JsonResponse(['error' => 'Scene already exists.'], Response::HTTP_BAD_REQUEST);
                        }
            $scene = new Scene();
            $scene->setId($lastSceneId + 1);
            $scene->setNom($data['nom']);
            $scene->setPoiFK($poi);
            $entityManager->persist($scene);
            $entityManager->flush();

                // Update the poi_fk_id field in the scene table using raw SQL
            $updateSql = 'UPDATE scene SET poi_fk_id = :poiId WHERE id = :sceneId';
            $updateStmt = $entityManager->getConnection()->prepare($updateSql);

            $updateStmt->executeStatement([
                'poiId' => $lastInsertedPoiId, // Use the ID of the newly inserted Poi
                'sceneId' => $scene->getId()   // Use the ID of the newly created Scene
            ]);

            // Log the update
            error_log('Updated Scene ID ' . $scene->getId() . ' with Poi ID ' . $lastInsertedPoiId);
        } 

            // Return success response
            return new JsonResponse(['message' => 'Poi inserted successfully.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Log the exception details
            error_log('Exception occurred: ' . $e->getMessage());
            error_log('Exception trace: ' . $e->getTraceAsString());

            return new JsonResponse([
                'error' => 'An error occurred while inserting the coordinates.',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    


    

      /**
     * Validate the token from the Authorization header and return the authenticated user.
     */
    private function validateToken(Request $request): JsonResponse|User
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Token is required'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = substr($authHeader, 7);

        // Find the user by the token
        $user = $this->userRepository->findOneBy(['apiToken' => $token]);

        if (!$user) {
            return new JsonResponse(['error' => 'Invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Check if the user has the ROLE_USER role
        if (!in_array('ROLE_USER', $user->getRoles())) {
            return new JsonResponse(['error' => 'Access denied. User does not have the required role.'], JsonResponse::HTTP_FORBIDDEN);
        }

        return $user;
    }

}
