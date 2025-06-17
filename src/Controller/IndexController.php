<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Entity\User;
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

// Ce fichier extend la classe ApiController 
final class IndexController extends ApiController
{

// Route pour réccuperer tous les données sur les artists 
    #[Route('/concerts', name: 'app_concerts')]
    public function concerts(ArtistRepository $ArtistRepository): Response
    {
        $concerts = $ArtistRepository->findAllWithScenes();
        return $this->render('index/concerts.html.twig', [
            'concerts' => $concerts,
        ]);
    }

// Route pour réccuperer tous les données sur les artists et les scènes associées
    #[Route('/concert', name: 'app_index')]
    public function getData(ArtistRepository $ArtistRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $ArtistRepository->findAllWithScenes();
        // réccupération par groupe de données, serialisé artist:read et scene:read
        $jsonData = $serializer->serialize($data, 'json', ['groups' => ['artist:read', 'scene:read']]);
        return new JsonResponse($jsonData, 200, [], true);
    }

// Route pour créer un concert en particulier par l'id, utilisation par le back office
   #[Route('/concert/{id}', name: 'app_concert_show', methods: ['GET', 'POST'])]
public function showConcert(
    int $id,
    Request $request,
    ArtistRepository $artistRepository,
    EntityManagerInterface $entityManager
): Response {
    // Trouver le concert par son ID
 
    $concert = $artistRepository->find($id);
   // Si le concert n'existe pas, rediriger vers la liste des concerts avec un message d'erreur
    if (!$concert) {
        $this->addFlash('danger', 'Concert introuvable.');
        return $this->redirectToRoute('app_concert_show', ['id' => $id]);
    }

    // Bound concert au ModifConcertType form
    $form = $this->createForm(\App\Form\ModifConcertType::class, $concert);
    $form->handleRequest($request);

    // Si le formulaire est soumis et valide, mettre à jour le concert
    // et rediriger vers la page de détails du concert
    if ($form->isSubmitted() && $form->isValid()) {
        $concert = $form->getData();

        // GRéccuperer la date
        $date = $form->get('date')->getData();

        // Get the current times from the entity
        $currentStart = $concert->getStartTime(); // DateTime object
        $currentEnd = $concert->getEndTime();     // DateTime object

        if ($date && $currentStart) {
            // nouvelle date + heure de début
            $concert->setStartTime(new \DateTime($date->format('Y-m-d') . ' ' . $currentStart->format('H:i:s')));
        }
        if ($date && $currentEnd) {
            // nouvelle date + heure de fin

            $concert->setEndTime(new \DateTime($date->format('Y-m-d') . ' ' . $currentEnd->format('H:i:s')));
        }

        $entityManager->flush();
        $this->addFlash('success', 'Concert modifié avec succès.');
        return $this->redirectToRoute('app_concert_show', ['id' => $id]);
    }

    // dump($concert->getDate());

    return $this->render('index/concert.html.twig', [
        'concert' => $concert,
        'form' => $form->createView(),
    ]);
}


// Routes pour lire les données sur les scènes
    #[Route('/scenes', name: 'app_scenes', methods: ['GET'])]
    public function getScenes(SceneRepository $sceneRepository, SerializerInterface $serializer): JsonResponse
    {
        // FTouver toutes les scènes en utilisant le repository
        $scenes = $sceneRepository->findAll();
    
        // Serialize les groupes de données avec un json
        $jsonData = $serializer->serialize($scenes, 'json', ['groups' => ['scene:read']]);
    
        // Retourne le json
        return new JsonResponse($jsonData, 200, [], true);
    }

}



