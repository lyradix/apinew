<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Entity\User;
use App\Entity\Poi;
use App\Entity\Artist;
use App\Entity\Info;
use App\Repository\ArtistRepository;
use App\Repository\InfoRepository;
use App\Repository\PartnersRepository;
use App\Repository\PoiRepository;
use App\Repository\SceneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use CrEOF\Spatial\PHP\Types\Geometry\Point;


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
                return new JsonResponse(['error' => 'Scene introuvable'], Response::HTTP_NOT_FOUND);
            }
            $concert->setSceneFK($scene); 
        }
    
        $entityManager->persist($concert);
        $entityManager->flush();

        $jsonData = $serializer->serialize($concert, 'json', ['groups' => ['artist:read', 'scene:read']]);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/updateScene', name: 'app_updateScene', methods: ['PUT'])]  
    public function putScene(HttpFoundationRequest $request,
    SceneRepository $sceneRepository,
    SerializerInterface $serializer,
    EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['id'])) {
            return new JsonResponse(['error' => 'Scene ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $scene = $sceneRepository->find($data['id']);
        if (!$scene) {
            return new JsonResponse(['error' => 'Scenes introuvables'], Response::HTTP_NOT_FOUND);
        }
        if (isset($data['nom'])) {
            $scene->setNom($data['nom']);
        }

        if (isset($data['longitude'])) {
            $scene->setLongitude($data['longitude']);
        }
        if (isset($data['latitude'])) {
            $scene->setLatitude($data['latitude']);
        }

        $entityManager->persist($scene);
        $entityManager->flush();

        $jsonData = $serializer->serialize($scene, 'json', ['groups' => ['poi:read', 'scene:read']]);

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

    #[Route('/postScene', name: 'app_postScene', methods: ['POST'])]
    public function postCoordinates(
        HttpFoundationRequest $request,
        SerializerInterface $serializer,
        sceneRepository $sceneRepository,
        PoiRepository $poiRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Log the incoming request data
        error_log('Incoming request data: ' . json_encode($data));

        if (!isset($data['nom'])) {
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

        $lastSceneId = $sceneRepository->findOneBy([], ['id' => 'DESC'])?->getId() ?? 0;
        // $lastPoiId = $poiRepository->findOneBy([], ['id' => 'DESC'])?->getId() ?? 0;
        // Log the validated data
        error_log('Validated data: ' . json_encode($data));

        

        try {
            $popup = $data['nom'];
            
            // Preset values for properties
            $presetProperties = [
                'popup' => $popup,
                'type' => 'scène',
                'marker-color' => '#d21e96',
                'marker-symbol' => 'theatre',
                'image' => 'star'
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
            $scene = new Scene();
            $scene->setId($lastSceneId + 1);
            $scene->setNom($data['nom']);
            // $scene->setPoiFK($poi);
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

    #[Route('/addConcert', name:'app_addConcert', methods:['POST'])]
    public function addConcert(
        Request $request,
        EntityManagerInterface $entityManager,
        ArtistRepository $ArtistRepository,
        SceneRepository $sceneRepository
    ):JsonResponse {
         $data = json_decode($request->getContent(), true);

         if(!isset($data['nom'], 
         $data['startTime'], 
         $data['endTime'],
         $data['famousSong'],
         $data['genre'], 
         $data['description'],
         $data['source'],
         $data['lien'],
          $data['sceneFK']
         )) { return new JsonResponse(['error' => 'Eléments manquant.'], Response::HTTP_BAD_REQUEST);}

  
      $scene = $entityManager->getRepository(Scene::class)->find($data['sceneFK']);
    if (!$scene) {
        return new JsonResponse(['error' => 'Scene introuvable'], Response::HTTP_NOT_FOUND);
    }
    
         $lastArtistId = $ArtistRepository->findOneBy([], ['id' => 'DESC'])?->getId() ?? 0;

        $artist = new Artist();
        $artist -> setId($lastArtistId + 1);
        $artist -> setNom($data['nom']);
        $artist -> setStartTime(new \DateTime($data['startTime']));
        $artist -> setEndTime(new \DateTime($data['endTime']));
        $artist -> setFamousSong($data['famousSong']);
        $artist -> setGenre($data['genre']);
        $artist -> setDescription($data['description']);
        $artist -> setSource($data['source']);
        $artist -> setLien($data['lien']);
       $artist->setSceneFK($scene);

        $entityManager -> persist($artist);
        $entityManager -> flush();

         return new JsonResponse(['message' => 'Concert créé avec succès'], JsonResponse::HTTP_CREATED);
         
    }

    #[Route('/addInfo', name:'app_addInfo', methods:['POST'])]
    public function addInfo(
        Request $request,
        InfoRepository $InfoRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse{
     
        $data = Json_decode($request->getContent(), true);

        if(!isset($data['title'],
            $data['descriptif'],
            $data['type']
            )){ return new JsonResponse(['error' => 'Eléments manquant.'], Response::HTTP_BAD_REQUEST);}

   
        $lastInsertedId = $InfoRepository->findOneBy([],['id' => 'DESC'])?->getId() ?? 0;

        $info = new Info();
        $info -> setId($lastInsertedId + 1);
        $info -> setTitle($data['title']);
        $info -> setDescriptif($data['descriptif']);
        $info -> setType($data['type']);

        $entityManager -> persist($info);
        $entityManager -> flush();

        return new JsonResponse(['message' => 'info générale ajoutée avec succès'], JsonResponse::HTTP_CREATED);
    }


}
