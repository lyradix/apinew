<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Entity\Poi;
use App\Entity\Artist;
use App\Entity\Info;
use App\Form\ModifPlaceType;
use App\Form\NewSceneType;
use App\Form\UpdateInfoType;
use App\Repository\ArtistRepository;
use App\Repository\InfoRepository;
use App\Repository\PartnersRepository;
use App\Repository\PoiRepository;
use App\Repository\SceneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;

class BackOfficeController extends ApiController

{
    
    // route pour la page d'administration des concerts
    #[Route('/admin-concerts', name: 'app_adminConcerts')]
    // cette méthode utilise les repositories ArtistRepository et InfoRepository
    public function adminConcerts(
        ArtistRepository $ArtistRepository,
        InfoRepository $infoRepository
        // retourne une réponse de type Response pour symfony base app
    ): Response
    {
        // Récupère tous les concerts avec leurs scènes
        $concerts = $ArtistRepository->findAllWithScenes();
        // Récupère les informations générales
        $info = $infoRepository->findOneBy([]); 

        // retourn la vue adminConcerts.html.twig avec les variables concerts et info
        return $this->render('index/adminConcerts.html.twig', [
            'concerts' => $concerts,
            'info' => $info,
            'controller_name' => 'Administration Concerts',
            'error' => null,
        ]);
     
    }

    // Route pour mettre à jour un concert, utilisation de la méthode PUT
     #[Route('/updateconcert', name: 'app_updateconcert', methods: ['PUT'])]
    public function putConcert(HttpFoundationRequestHandler $request,
        ArtistRepository $ArtistRepository, 
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupère les données JSON de la requête
        $data = json_decode($request->getContent(), true);


        // si l'id ne correspond pas ou si le concert n'esxist pas, retourne une erreur
        
        if (!isset($data['id'])) {
            return new JsonResponse(['error' => 'Scene ID is required'], Response::HTTP_BAD_REQUEST);
        }
        $concert = $ArtistRepository->find($data['id']);

        if (!$concert) {
            return new JsonResponse(['error' => 'Concert not found'], Response::HTTP_NOT_FOUND);
        }

        // MAJ des données du concert, startTime, endTime, sceneFK
        if (isset($data['date'])) {
            $newDate = $data['date']; // e.g. '2025-07-01'
            // Update startTime
            if ($concert->getStartTime()) {
                $startTimeString = $newDate . ' ' . $concert->getStartTime()->format('H:i:s');
                $concert->setStartTime(new \DateTime($startTimeString));
            }
            // Update endTime
            if ($concert->getEndTime()) {
                $endTimeString = $newDate . ' ' . $concert->getEndTime()->format('H:i:s');
                $concert->setEndTime(new \DateTime($endTimeString));
            }
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

        // persiter et flush des données du concert
        $entityManager->persist($concert);
        $entityManager->flush();

        // $jsonData prend la valeur du concert sérialisé en JSON
        $jsonData = $serializer->serialize($concert, 'json', ['groups' => ['artist:read', 'scene:read']]);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    
// Route pour ajouter un nouveau point d'intérêt (POI) ou une scène si l'utilisateur choisit le type "scène"
    #[Route('/postPoi', name: 'post_poi', methods: ['GET', 'POST'])]
public function postPoi(
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $poi = new Poi();

    // Requête SQL pour obtenir les types uniques de POI
    $connection = $entityManager->getConnection();
    // Extraction des types de POI depuis la colonne JSON 'properties'
    $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.type')) AS type FROM poi WHERE JSON_EXTRACT(properties, '$.type') IS NOT NULL";
    $stmt = $connection->prepare($sql);
    $result = $stmt->executeQuery()->fetchAllAssociative();

    // Construction des choix de type pour le formulaire
    $typeChoices = [];
    //Loop forEach pour parcourir les résultats 
    foreach ($result as $row) {
        // Si le type n'est pas vide, on l'ajoute au tableau des choix
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
            // la logique pour ajouter une scène
            return $this->render('poi/newPlace.html.twig', [
                'form' => $form->createView(),
                'scene_ajax' => true, // ajax pour ajouter une scène
                // l'utilisation d'ajax permet de ne pas recharger la page mais gérer l'ajout de scène en arrière plan
                'nom' => $nom,
                'longitude' => $longitude,
                'latitude' => $latitude,
            ]);
        }

        // Insertion des dpropriétés par défault, n'affecte l'output en back office ou en front end
        $presetProperties = [
            // les nom est dynamique 
            // le popup est le nom du POI
            'popup' => $nom,
            //le type est dynamique et prend la valeurdu type choisi dans le fromulaire
            'type' => $type,
            'marker-color' => '#d21e96',
            'marker-symbol' => 'theatre',
            'image' => 'random'
        ];

        // stockage des coordonnées dans un format GeoJSON
        $geoJson = json_encode([
            'type' => 'Point',
            'coordinates' => [(float)$longitude, (float)$latitude]
        ]);

        // requuête SQL pour insérer le nouveau POI avec les propriétés par défault
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


    #[Route('/updateInfo/{id}', name: 'app_updateInfo', methods: ['GET', 'POST', 'PUT'])]
public function updateInfo(
    int $id,
    Request $request,
    InfoRepository $infoRepository,
    EntityManagerInterface $entityManager,
    SerializerInterface $serializer
): Response {
    $info = $infoRepository->find($id);
    if (!$info) {
        if ($request->getContentTypeFormat() === 'json') {
            return new JsonResponse(['error' => 'Info introuvable'], Response::HTTP_NOT_FOUND);
        }
        $this->addFlash('danger', 'Info introuvable.');
        return $this->redirectToRoute('app_info');
    }
    
    // Handle JSON request (API call)
    if ($request->getContentTypeFormat() === 'json' || $request->isXmlHttpRequest() || $request->getMethod() === 'PUT') {
        $data = json_decode($request->getContent(), true);
        
        // Update fields if they exist in the request
        if (isset($data['title'])) {
            $info->setTitle($data['title']);
        }
        
        if (isset($data['type'])) {
            $info->setType($data['type']);
        }
        
        if (isset($data['descriptif'])) {
            $info->setDescriptif($data['descriptif']);
        }
        
        // Save changes
        $entityManager->persist($info);
        $entityManager->flush();
        
        // Return JSON response
        $jsonData = $serializer->serialize($info, 'json', ['info:read']);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }
    
    // Standard form handling for web interface
    $titles = $entityManager->getRepository(Info::class)
        ->createQueryBuilder('i')
        ->select('i.title')
        ->distinct()
        ->getQuery()
        ->getResult();

    $titleChoices = [];
    foreach ($titles as $row) {
        $titleChoices[$row['title']] = $row['title'];
    }

    $types = $entityManager->getRepository(Info::class)
        ->createQueryBuilder('i')
        ->select('i.type')
        ->distinct()
        ->getQuery()
        ->getResult();

    $typeChoices = [];
    foreach ($types as $row) {
        $typeChoices[$row['type']] = $row['type'];
    }

    $form = $this->createForm(UpdateInfoType::class, $info, [
        'title_choices' => $titleChoices,
        'type_choices' => $typeChoices,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Info modifiée avec succès.');
        return $this->redirectToRoute('app_info');
    }

    $infos = $infoRepository->findAll();
    $titleToId = [];
    foreach ($infos as $infoItem) {
        $titleToId[$infoItem->getTitle()] = $infoItem->getId();
    }
    return $this->render('info/update.html.twig', [
        'form' => $form->createView(),
        'title_to_id' => $titleToId,
        'info' => $info,
    ]);
}


    #[Route('/partners', name: 'app_partners')]
    public function getPartners(PartnersRepository $PartnersRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $PartnersRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json', ['partners:read']);
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/info', name: 'app_info')]
    public function getInfo(InfoRepository $InfoRepository, SerializerInterface $serializer): Response
    {
        $data = $InfoRepository->findAll();
        return $this->render('info/index.html.twig', [
            'infos' => $data
        ]);
    }
    
    #[Route('/api/info', name: 'app_api_info', methods: ['GET'])]
    public function getInfoApi(InfoRepository $InfoRepository, SerializerInterface $serializer): JsonResponse
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

    #[Route('/addInfo', name:'app_addInfo', methods:['GET', 'POST'])]
    public function addInfo(
        Request $request,
        InfoRepository $InfoRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer = null
    ): Response {
        // For JSON requests (API calls)
        if ($request->getContentTypeFormat() === 'json' || $request->headers->get('Content-Type') === 'application/json') {
            try {
                // Decode JSON data from request
                $data = json_decode($request->getContent(), true);
                if ($data === null) {
                    throw new \Exception('Invalid JSON data');
                }
                
                // Create a new Info object
                $info = new Info();
                
                // Set fields from JSON data
                if (isset($data['title'])) {
                    $info->setTitle($data['title']);
                } else {
                    throw new \Exception('Title is required');
                }
                
                if (isset($data['type'])) {
                    $info->setType($data['type']);
                } else {
                    throw new \Exception('Type is required');
                }
                
                if (isset($data['descriptif'])) {
                    $info->setDescriptif($data['descriptif']);
                } else {
                    throw new \Exception('Descriptif is required');
                }
                
                // Save new entity
                $entityManager->persist($info);
                $entityManager->flush();
                
                // Return created entity
                return new JsonResponse(
                    $serializer->serialize($info, 'json', ['info:read']),
                    Response::HTTP_CREATED,
                    [],
                    true
                );
            } catch (\Exception $e) {
                return new JsonResponse(
                    ['error' => 'Error creating info: ' . $e->getMessage()],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }
        
        // For regular form submission
        $info = new Info();
        
        // Get unique types from existing Info entities
        $types = $entityManager->createQuery('SELECT DISTINCT i.type FROM App\Entity\Info i')
            ->getResult();
            
        $typeChoices = [];
        foreach ($types as $typeData) {
            if ($typeData['type']) {
                $typeChoices[$typeData['type']] = $typeData['type'];
            }
        }
        
        // Add some default types if none exist
        if (empty($typeChoices)) {
            $typeChoices = [
                'Information' => 'Information',
                'Alerte' => 'Alerte',
                'Notification' => 'Notification'
            ];
        }

        $form = $this->createForm(\App\Form\AddInfoType::class, $info, [
            'type_choices' => $typeChoices
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($info);
            $entityManager->flush();
            $this->addFlash('success', 'Information ajoutée avec succès.');
            return $this->redirectToRoute('app_adminConcerts');
        }

        // Always return a response if form is not submitted or not valid
        return $this->render('info/add.html.twig', [
            'formAddInfo' => $form->createView(),
    ]);
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

    #[Route('/deletePoi/{id}', name: 'delete_poi', methods: ['POST'])]
    public function deletePoi(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        SceneRepository $sceneRepository
    ): Response {
        $poi = $entityManager->getRepository(Poi::class)->find($id);
        if (!$poi) {
            $this->addFlash('danger', 'POI introuvable.');
            return $this->redirectToRoute('app_render_updatePoi');
        }

        // Handle CSRF form
        $form = $this->createForm(\App\Form\DeletePoiType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Delete related scenes first
            $scenes = $sceneRepository->findBy(['poiFK' => $poi]);
            foreach ($scenes as $scene) {
                $entityManager->remove($scene);
            }
            $entityManager->remove($poi);
            $entityManager->flush();

            $this->addFlash('success', 'POI supprimé avec succès.');
            return $this->redirectToRoute('app_render_updatePoi');
        }

        $this->addFlash('danger', 'Erreur lors de la suppression du POI.');
        return $this->redirectToRoute('app_render_updatePoi');
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

    // Add this before rendering the template
    $poi = new Poi();

    // Get unique types for the add form (reuse your logic)
    $connection = $entityManager->getConnection();
    $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.type')) AS type FROM poi WHERE JSON_EXTRACT(properties, '$.type') IS NOT NULL";
    $result = $connection->executeQuery($sql)->fetchAllAssociative();

    $typeChoices = [];
    foreach ($result as $row) {
    if ($row['type']) {
        $typeChoices[$row['type']] = $row['type'];
    }
    }
    $formAddPoi = $this->createForm(\App\Form\AddPlaceType::class, $poi, [
    'type_choices' => $typeChoices,
    ]);

    // If you also need the modify form:
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
        'pois' => $pois, // <-- Add this line!
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
        HttpFoundationRequest $request,
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

    #[Route('/add-partner', name: 'app_add_partner', methods: ['GET', 'POST'])]
    public function addPartner(
    Request $request,
    EntityManagerInterface $entityManager
    ): Response {
    $partner = new \App\Entity\Partners();

    $form = $this->createForm(\App\Form\AddPartnersType::class, $partner);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $type = $form->get('type')->getData();

        // Determine prefix based on type
        $prefix = '';
        if (strtolower($type) === 'restaurent') {
            $prefix = 'a';
        } elseif (strtolower($type) === 'sponsor') {
            $prefix = 'b';
        } elseif (strtolower($type) === 'media') {
            $prefix = 'c';
        }

        // Find the last partnerId with this prefix
        $conn = $entityManager->getConnection();
        $sql = "SELECT partner_id FROM partners WHERE partner_id LIKE :prefix ORDER BY LENGTH(partner_id) DESC, partner_id DESC LIMIT 1";
        $last = $conn->executeQuery($sql, ['prefix' => $prefix . '%'])->fetchOne();

        if ($last) {
            // Extract the numeric part and increment
            $num = (int)substr($last, 1);
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $partnerId = $prefix . $newNum;
        $partner->setPartnerId($partnerId);

        $entityManager->persist($partner);
        $entityManager->flush();

        $this->addFlash('success', 'Partenaire ajouté avec succès. ID: ' . $partnerId);
        return $this->redirectToRoute('app_add_partner');
    }

    return $this->render('partners/add.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/update-partner', name: 'app_update_partner', methods: ['PUT'])]
    public function updatePartner(
    Request $request,
    EntityManagerInterface $entityManager,
    PartnersRepository $partnersRepository
    ): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!isset($data['id'])) {
        return new JsonResponse(['error' => 'Partner ID is required'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $partner = $partnersRepository->find($data['id']);
    if (!$partner) {
        return new JsonResponse(['error' => 'Partner not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    // Update fields if provided
    if (isset($data['title'])) {
        $partner->setTitle($data['title']);
    }
    if (isset($data['type'])) {
        $partner->setType($data['type']);
    }
    if (isset($data['link'])) {
        $partner->setLink($data['link']);
    }
    if (isset($data['frontPage'])) {
        $partner->setFrontPage((bool)$data['frontPage']);
    }

    $entityManager->flush();

    return new JsonResponse(['success' => true, 'message' => 'Partner updated successfully.']);
    }
}