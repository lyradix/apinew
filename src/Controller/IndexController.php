<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Entity\User;
use App\Entity\Poi;
use App\Entity\Artist;
use App\Entity\Info;
use App\Form\NewSceneType;
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
use Symfony\Bundle\MakerBundle\Security\Model\Authenticator;

final class IndexController extends ApiController
{
     #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/admin-concerts', name: 'app_adminConcerts')]
    public function adminConcerts(
        ArtistRepository $ArtistRepository
    ): Response
    {
        $concerts = $ArtistRepository->findAllWithScenes();
        return $this->render('index/adminConcerts.html.twig', [
        'controller_name' => 'Administration concerts',
        'concerts' => $concerts,
    ]);
     
    }

    #[Route('/concerts', name: 'app_concerts')]
    public function concerts(ArtistRepository $ArtistRepository): Response
    {
        $concerts = $ArtistRepository->findAllWithScenes();
        return $this->render('index/concerts.html.twig', [
            'concerts' => $concerts,
        ]);
    }

    #[Route('/concert', name: 'app_index')]
    public function getData(ArtistRepository $ArtistRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $ArtistRepository->findAllWithScenes();
        $jsonData = $serializer->serialize($data, 'json', ['groups' => ['artist:read', 'scene:read']]);
        return new JsonResponse($jsonData, 200, [], true);
    }

   #[Route('/concert/{id}', name: 'app_concert_show', methods: ['GET', 'POST'])]
public function showConcert(
    int $id,
    Request $request,
    ArtistRepository $artistRepository,
    EntityManagerInterface $entityManager
): Response {
    $concert = $artistRepository->find($id);

    if (!$concert) {
        $this->addFlash('danger', 'Concert introuvable.');
        return $this->redirectToRoute('app_concert_show', ['id' => $id]);
    }

    $form = $this->createForm(\App\Form\ModifConcertType::class, $concert);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Concert modifié avec succès.');
        return $this->redirectToRoute('app_concert_show', ['id' => $id]);
    }

    dump($concert->getDate());

    return $this->render('index/concert.html.twig', [
        'concert' => $concert,
        'form' => $form->createView(),
    ]);
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

    #[Route('/postPoi', name: 'post_poi', methods: ['GET', 'POST'])]
public function postPoi(
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $poi = new Poi();

    // Get unique types from feature.properties.type
    $connection = $entityManager->getConnection();
    $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.type')) AS type FROM poi WHERE JSON_EXTRACT(properties, '$.type') IS NOT NULL";
    $stmt = $connection->prepare($sql);
    $result = $stmt->executeQuery()->fetchAllAssociative();

    $typeChoices = [];
    foreach ($result as $row) {
        if ($row['type']) {
            $typeChoices[$row['type']] = $row['type'];
        }
    }

    $form = $this->createForm(\App\Form\AddPlaceType::class, $poi, [
        'type_choices' => $typeChoices,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $type = $form->get('type')->getData();
        $nom = $form->get('nom')->getData();
        $longitude = $form->get('longitude')->getData();
        $latitude = $form->get('latitude')->getData();

        if ($type === 'scène') {
            // Do nothing here! JS will handle the AJAX POST to /postScene
            return $this->render('poi/newPlace.html.twig', [
                'form' => $form->createView(),
                'scene_ajax' => true, // Optional: flag for JS
                'nom' => $nom,
                'longitude' => $longitude,
                'latitude' => $latitude,
            ]);
        }

        // Insert POI for other types (as before)
        $presetProperties = [
            'popup' => $nom,
            'type' => $type,
            'marker-color' => '#d21e96',
            'marker-symbol' => 'theatre',
            'image' => 'random'
        ];
        $geoJson = json_encode([
            'type' => 'Point',
            'coordinates' => [(float)$longitude, (float)$latitude]
        ]);
        $sql = 'INSERT INTO poi (type, properties, geometry) VALUES (:type, :properties, ST_GeomFromGeoJSON(:geometry))';
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->executeStatement([
            'type' => 'Feature',
            'properties' => json_encode($presetProperties),
            'geometry' => $geoJson
        ]);

        $this->addFlash('success', 'Lieu ajouté avec succès.');
        return $this->redirectToRoute('post_poi');
    }

    return $this->render('poi/newPlace.html.twig', [
        'form' => $form->createView(),
    ]);
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


    #[Route('/updateInfo', name: 'app-updateInfo', methods: ['PUT'])]
    public function putInfo(HttpFoundationRequest $request,
    infoRepository $infoRepository,
    SerializerInterface $serializer,
    EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['id'])) {
            return new JsonResponse(['error' => 'Info ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $info = $infoRepository->find($data['id']);
        if (!$info) {
            return new JsonResponse(['error' => 'Info introuvable'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['title'])) {
            $info->setTitle($data['title']);
        }
        if (isset($data['descriptif'])) {
            $info->setDescriptif($data['descriptif']);
        }
        if (isset($data['type'])) {
            $info->setType($data['type']);
        }

        $entityManager->persist($info);
        $entityManager->flush();

        $jsonData = $serializer->serialize($info, 'json', ['groups' => ['info:read']]);

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

    #[Route('/postScene', name: 'app_postScene', methods: ['GET', 'POST'])]
public function postScene(Request $request, EntityManagerInterface $entityManager, SceneRepository $sceneRepository): Response
{
    // Handle AJAX POST (from JS)
    if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
        $nom = $request->request->get('nom');
        $longitude = $request->request->get('longitude');
        $latitude = $request->request->get('latitude');

        if ($nom && $longitude && $latitude) {
            $presetProperties = [
                'popup' => $nom,
                'type' => 'scène',
                'marker-color' => '#d21e96',
                'marker-symbol' => 'theatre',
                'image' => 'star'
            ];

            $geoJson = json_encode([
                'type' => 'Point',
                'coordinates' => [(float)$longitude, (float)$latitude]
            ]);

            // Insert POI
            $sql = 'INSERT INTO poi (type, properties, geometry) VALUES (:type, :properties, ST_GeomFromGeoJSON(:geometry))';
            $stmt = $entityManager->getConnection()->prepare($sql);
            $stmt->executeStatement([
                'type' => 'Feature',
                'properties' => json_encode($presetProperties),
                'geometry' => $geoJson
            ]);
            $lastInsertedPoiId = $entityManager->getConnection()->lastInsertId();

            // Insert Scene
            $sqlScene = 'INSERT INTO scene (poi_FK_id, nom) VALUES (:poiId, :nom)';
            $stmtScene = $entityManager->getConnection()->prepare($sqlScene);
            $stmtScene->executeStatement([
                'poiId' => $lastInsertedPoiId,
                'nom' => $nom,
            ]);

            return new JsonResponse(['success' => true, 'message' => 'Scène ajoutée avec succès']);
        }
        return new JsonResponse(['success' => false, 'message' => 'Paramètres manquants'], 400);
    }

    // Handle Symfony form as before
    $form = $this->createForm(NewSceneType::class, null, [
        'data_class' => null // Use array data, not an entity
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $nom = $data['nom'];
        $longitude = $data['longitude'];
        $latitude = $data['latitude'];

        // Preset properties for Poi
        $presetProperties = [
            'popup' => $nom,
            'type' => 'scène',
            'marker-color' => '#d21e96',
            'marker-symbol' => 'theatre',
            'image' => 'star'
        ];

        // GeoJSON geometry
        $geoJson = json_encode([
            'type' => 'Point',
            'coordinates' => [(float)$longitude, (float)$latitude]
        ]);

        // 1. Insert Poi using raw SQL
        $sql = 'INSERT INTO poi (type, properties, geometry) VALUES (:type, :properties, ST_GeomFromGeoJSON(:geometry))';
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->executeStatement([
            'type' => 'Feature',
            'properties' => json_encode($presetProperties),
            'geometry' => $geoJson
        ]);
        $lastInsertedPoiId = $entityManager->getConnection()->lastInsertId();

        // 2. Insert Scene using raw SQL
        $sqlScene = 'INSERT INTO scene (poi_FK_id, nom) VALUES (:poiId, :nom)';
        $stmtScene = $entityManager->getConnection()->prepare($sqlScene);
        $stmtScene->executeStatement([
            'poiId' => $lastInsertedPoiId,
            'nom' => $nom
        ]);

        // Add success message and redirect
        $this->addFlash('success', 'Scène ajoutée avec succès');
        return $this->redirectToRoute('app_addConcert');
        }

    return $this->render('index/addScene.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/addConcert', name: 'app_addConcert', methods: ['GET', 'POST'])]
public function addConcert(
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $artist = new Artist();
    $form = $this->createForm(\App\Form\AddConcertType::class, $artist);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($artist);
        $entityManager->flush();

        $this->addFlash('success', 'Concert créé avec succès');
        return $this->redirectToRoute('app_adminConcerts');
    }

    return $this->render('index/addConcert.html.twig', [
        'form' => $form->createView(),
    ]);
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

     #[Route('/deleteConcert/{id}', name: 'delete_concert', methods: ['DELETE'])]
    public function deleteConcert(int $id, EntityManagerInterface $entityManager, ArtistRepository $artistRepository): JsonResponse
    {
        $concert = $artistRepository->find($id);
        if ($concert) {
            $entityManager->remove($concert);
            $entityManager->flush();
            return new JsonResponse(['success' => true]);
        }
        return new JsonResponse(['error' => 'Not found'], 404);
    }

       /**
     * Validate the token from the Authorization header and return the authenticated user.
     */
    private function validateToken(Request $request): JsonResponse|User
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Authentication failed: invalid or missing token.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = substr($authHeader, 7);

        // Find the user by the token
        $user = $this->userRepository->findOneBy(['apiToken' => $token]);

        if (!$user) {
           return new JsonResponse(['error' => 'Authentication failed: invalid user'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Check if the user has the ROLE_USER role
        if (!in_array('ROLE_USER', $user->getRoles())) {
            return new JsonResponse(['error' => 'Authentication failed: invalid roles.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    #[Route('/deletePoi/{id}', name: 'delete_poi', methods: ['GET', 'POST'])]
    public function deletePoi(
        int $id,
        EntityManagerInterface $entityManager,
        SceneRepository $sceneRepository
    ): Response {
        // 1. Find the Scene by its ID
        $scene = $sceneRepository->find($id);
        if (!$scene) {
            $this->addFlash('danger', 'Scène introuvable.');
            return $this->redirectToRoute('app_addConcert');
        }

        // 2. Get the related Poi ID
        $poi = $scene->getPoiFK();
        $poiId = $poi ? $poi->getId() : null;

        // 3. Delete the Scene
        $entityManager->remove($scene);

        // 4. Delete the Poi if it exists
        if ($poiId) {
            $poiEntity = $entityManager->getRepository(Poi::class)->find($poiId);
            if ($poiEntity) {
                $entityManager->remove($poiEntity);
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Scène et son POI supprimés avec succès.');
        return $this->redirectToRoute('app_addConcert');
    }
}
