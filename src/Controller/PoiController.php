<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Poi;
use App\Entity\Scene;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\PoiRepository;
use App\Repository\SceneRepository;
use App\Form\AddPlaceType;

final class PoiController extends AbstractController
{
    #[Route('/create-poi', name: 'create_poi', methods: ['POST'])]
    public function createPoi(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new JsonResponse(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['id'], $data['type'], $data['properties'], $data['geometry'])) {
            return new JsonResponse(['error' => 'Invalid payload'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $poi = new Poi();
        $poi->setId($data['id']);
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
     // à revoir
        $connection = $entityManager->getConnection();
        $sql = 'SELECT id, type, properties, ST_AsGeoJSON(geometry) AS geometry FROM poi';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery()->fetchAllAssociative();

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

    // route pour créer un point d'intérêt (POI) avec des coordonnées
    #[Route('/postPlace', name: 'app_Place', methods: ['POST'])]
    public function postCoordinates(
        HttpFoundationRequest $request,
        SerializerInterface $serializer,
        SceneRepository $ceneRepository,
        PoiRepository $poiRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        if (!$this->isGranted('ROLE_USER')) {
            return new JsonResponse(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

 
        error_log('Incoming request data: ' . json_encode($data));

        if (!isset($data['popup'])) {
            return new JsonResponse(['error' => 'Invalid data. "nom" is required.'], Response::HTTP_BAD_REQUEST);
        }
    
    


        if (!isset($data['longitude']) || !isset($data['latitude']) ) {
            error_log('Validation failed: Missing required fields.');
            return new JsonResponse(['error' => 'Invalid data. "longitude", "latitude", " are required.'], Response::HTTP_BAD_REQUEST);
        }

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
            $sql = 'INSERT INTO poi (type, properties, geometry, time_stamp) VALUES (:type, :properties, ST_GeomFromGeoJSON(:geometry), :time_stamp)';
            $stmt = $entityManager->getConnection()->prepare($sql);
  
   
            error_log('Executing SQL: ' . $sql);
            error_log('SQL Parameters: ' . json_encode([
                'type' => 'Feature',
                'properties' => json_encode($properties),
                'geometry' => $geoJson,
                'time_stamp' => (new \DateTime())->format('Y-m-d')
            ]));

            $stmt->executeStatement([
                'type' => 'Feature',
                'properties' => json_encode($properties),
                'geometry' => $geoJson,
                'time_stamp' => (new \DateTime())->format('Y-m-d')
            ]);
     


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
            $scene->setTimeStamp(new \DateTime());
            $entityManager->persist($scene);
            $entityManager->flush();

                // Update the poi_fk_id field in the scene table using raw SQL
            $updateSql = 'UPDATE scene SET poi_fk_id = :poiId WHERE id = :sceneId';
            $updateStmt = $entityManager->getConnection()->prepare($updateSql);

            $updateStmt->executeStatement([
                'poiId' => $lastInsertedPoiId, 
                'sceneId' => $scene->getId()  
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
    


    



    #[Route('/deletepoi', name: 'delete_poi', methods: ['DELETE'])]
    public function deletePoi(
        Request $request, 
        EntityManagerInterface $entityManager, 
        PoiRepository $poiRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!isset($data['id'])) {
                return new JsonResponse(['error' => 'Missing id in request'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $id = $data['id'];

            // First delete related scenes
            $deleteScenesSql = 'DELETE FROM scene WHERE poi_fk_id = :poiId';
            $sceneStmt = $entityManager->getConnection()->prepare($deleteScenesSql);
            $sceneStmt->executeStatement(['poiId' => $id]);

            // Then delete the POI
            $deletePoiSql = 'DELETE FROM poi WHERE id = :poiId';
            $poiStmt = $entityManager->getConnection()->prepare($deletePoiSql);
            $poiStmt->executeStatement(['poiId' => $id]);

            return new JsonResponse(['message' => 'Poi deleted successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'An error occurred while deleting the POI'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     #[Route('/render-new-place', name: 'app_render_newPlace')]
    public function renderNewPlace(): Response
    {
    return $this->render('poi/newPlace.html.twig');
    }

    #[Route('/pois', name: 'app_pois', methods: ['GET'])]
    public function pois(EntityManagerInterface $entityManager): Response
    {
    // Fetch all POIs
    $poisResult = $entityManager->getConnection()->executeQuery(
        "SELECT id, JSON_UNQUOTE(JSON_EXTRACT(properties, '$.popup')) AS popup FROM poi WHERE JSON_VALID(properties) = 1"
    )->fetchAllAssociative();

    $pois = [];
    foreach ($poisResult as $row) {
        $pois[] = (object)[
            'id' => $row['id'],
            'properties' => (object)[
                'popup' => $row['popup'],
            ],
        ];
    }


    $poi = new Poi();

    // Get unique types for the add form
    $typeConnection = $entityManager->getConnection();
    $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.type')) AS type FROM poi WHERE JSON_EXTRACT(properties, '$.type') IS NOT NULL";
    $result = $typeConnection->executeQuery($sql)->fetchAllAssociative();

    $typeChoices = [];
    foreach ($result as $row) {
    if ($row['type']) {
        $typeChoices[$row['type']] = $row['type'];
    }
    }
    $formAddPoi = $this->createForm(AddPlaceType::class, $poi, [
    'type_choices' => $typeChoices,
    'csrf_protection' => true, 
    'csrf_field_name' => '_token',
    'csrf_token_id' => 'poi_form', 
    ]);

    $poiChoices = [];
    foreach ($pois as $poi) {
    $poiChoices[$poi->properties->popup] = $poi->id;
    }
    $formModifPoi = $this->createForm(\App\Form\ModifPlaceType::class, null, [
    'poi_choices' => $poiChoices,
    ]);

    return $this->render('poi/poi.html.twig', [
        'formAddPoi' => $formAddPoi->createView(),
        'formModifPoi' => $formModifPoi->createView(),
        'pois' => $pois,
    ]);
    }

    #[Route('/render-update-poi', name: 'app_render_updatePoi')]
    public function renderUpdatePoi(EntityManagerInterface $entityManager): Response
    {
    // Fetch all POIs
    $connection = $entityManager->getConnection();
    $poisResult = $connection->executeQuery(
        "SELECT id, JSON_UNQUOTE(JSON_EXTRACT(properties, '$.popup')) AS popup 
            FROM poi 
            WHERE JSON_VALID(properties) = 1"
    )->fetchAllAssociative();

    $pois = [];
    $deleteForms = [];
    foreach ($poisResult as $row) {
        $pois[] = (object)[
            'id' => $row['id'],
            'properties' => (object)[
                'popup' => $row['popup'],
            ],
        ];
        $deleteForms[$row['id']] = $this->createForm(\App\Form\DeletePoiType::class)->createView();
    }

    return $this->render('poi/updatePoi.html.twig', [
        'pois' => $pois,
        'deleteForms' => $deleteForms,
    ]);

        return $this->render('poi/poi.html.twig', [
        'formAddPoi' => $formAddPoi->createView(),
        'formModifPoi' => $formModifPoi->createView(),
        'pois' => $pois,
    ]);
    }
    #[Route('/updatePoi', name: 'update_poi', methods: ['PUT'])]
    public function updatePoi(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        if (!isset($data['id'], $data['popup'], $data['longitude'], $data['latitude'], $data['type'])) {
            return new JsonResponse(['error' => 'Invalid data. "id", "popup", "longitude", "latitude", and "type" are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate longitude and latitude
        if ($data['longitude'] < -180 || $data['longitude'] > 180 || $data['latitude'] < -90 || $data['latitude'] > 90) {
            return new JsonResponse(['error' => 'Invalid longitude or latitude values.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            // Construct GeoJSON format
            $geoJson = json_encode([
                'type' => 'Point',
                'coordinates' => [(float)$data['longitude'], (float)$data['latitude']]
            ]);

            // Preset properties
            $presetProperties = [
                'popup' => $data['popup'],
                'type' => $data['type'],
                'marker-color' => '#d21e96',
                'marker-symbol' => 'camping',
                'image' => 'tent'
            ];

            // Update the Poi record using raw SQL
            $sql = 'UPDATE poi SET type = :type, properties = :properties, geometry = ST_GeomFromGeoJSON(:geometry) WHERE id = :id';
            $stmt = $entityManager->getConnection()->prepare($sql);

            $stmt->executeStatement([
                'id' => $data['id'],
                'type' => 'Feature',
                'properties' => json_encode($presetProperties),
                'geometry' => $geoJson
            ]);

            // Log the update
            error_log('Updated Poi ID: ' . $data['id']);

            // Return success response
            return new JsonResponse(['message' => 'Poi updated successfully.'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            // Log the exception details
            error_log('Exception occurred: ' . $e->getMessage());
            error_log('Exception trace: ' . $e->getTraceAsString());

            return new JsonResponse([
                'error' => 'An error occurred while updating the Poi.',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}