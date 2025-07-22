<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Entity\User;
use App\Entity\Poi;
use App\Entity\Artist;
use App\Entity\Info;
use App\Form\ModifConcertType;
use App\Dto\ConcertDto;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        // Get the new date from the form
        $date = $form->get('date')->getData();
        $startTime = $form->get('startTime')->getData();
        $endTime = $form->get('endTime')->getData();

        if ($date && $startTime) {
            $concert->setStartTime(new \DateTime($date->format('Y-m-d') . ' ' . $startTime->format('H:i:s')));
        }
        if ($date && $endTime) {
            $concert->setEndTime(new \DateTime($date->format('Y-m-d') . ' ' . $endTime->format('H:i:s')));
        }

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile */
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $imagesDirectory = $this->getParameter('images_directory');
            $imageFile->move($imagesDirectory, $newFilename);
            $concert->setImage($newFilename);
        }

        $entityManager->persist($concert);
        $entityManager->flush();
        $this->addFlash('success', 'Concert modifié avec succès.');
        return $this->redirectToRoute('app_concert_show', ['id' => $id]);
    }

    $currentImage = null;
    if ($concert->getImage()) {
        $imagePath = $this->getParameter('images_directory') . '/' . $concert->getImage();
        if (file_exists($imagePath)) {
            $currentImage = $concert->getImage();
        }
    }

    return $this->render('index/concert.html.twig', [
        'concert' => $concert,
        'form' => $form->createView(),
        'currentImage' => $currentImage,
    ]);
}

// // Route pour créer un concert en particulier par l'id, utilisation par le back office
//    #[Route('/concert/{id}', name: 'app_concert_show', methods: ['GET', 'POST'])]
// public function showConcert(
//     int $id,
//     Request $request,
//     ArtistRepository $artistRepository,
//     EntityManagerInterface $entityManager,
//     SluggerInterface $slugger
// ): Response {
//     // Trouver le concert par son ID
 
//     $concert = $artistRepository->find($id);
//     // Si le concert n'existe pas, rediriger vers la liste des concerts avec un message d'erreur
//     if (!$concert) {
//         $this->addFlash('danger', 'Concert introuvable.');
//         return $this->redirectToRoute('app_concerts'); // Redirect to concerts list instead
//     }

//     // Create a DTO to hold the data
//     $concertDto = new ConcertDto();
//     $concertDto->artist = $concert;
//     $concertDto->sceneFK = $concert->getSceneFK();
    
//     // If there's an existing image, store it
//     if ($concert->getImage()) {
//         $concertDto->concertImage = $concert->getImage();
//     }

//     // Bound DTO to the form
//     $form = $this->createForm(ModifConcertType::class, $concertDto);
//     $form->handleRequest($request);

//     // Si le formulaire est soumis et valide, mettre à jour le concert
//     // et rediriger vers la page de détails du concert
//     if ($form->isSubmitted() && $form->isValid()) {
//         // Get the data from the form
//         $concertDto = $form->getData();
//         $artist = $concertDto->artist;
        
//         // Update the artist's scene if it was changed
//         if ($concertDto->sceneFK) {
//             $artist->setSceneFK($concertDto->sceneFK);
//         }
        
//         // Réccuperer la date
//         $date = $form->get('date')->getData();

//         // Get the current times from the entity
//         $currentStart = $artist->getStartTime(); 
//         $currentEnd = $artist->getEndTime();    

//         if ($date && $currentStart) {
//             // nouvelle date + heure de début
//             $artist->setStartTime(new \DateTime($date->format('Y-m-d') . ' ' . $currentStart->format('H:i:s')));
//         }
//         if ($date && $currentEnd) {
//             // nouvelle date + heure de fin
//             $artist->setEndTime(new \DateTime($date->format('Y-m-d') . ' ' . $currentEnd->format('H:i:s')));
//         }

//          /** @var UploadedFile $imageFile */
//         $imageFile = $concertDto->imageFile;
        
//         if ($imageFile) {
//             try {
//                 $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
//                 $safeFilename = $slugger->slug($originalFilename);
//                 $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
//                 $imagesDirectory = $this->getParameter('images_directory');
                
//                 // Create directory if it doesn't exist
//                 if (!is_dir($imagesDirectory)) {
//                     $result = mkdir($imagesDirectory, 0777, true);
//                 }
                
//                 // Check if the file is valid before moving
//                 if (!$imageFile->isValid()) {
//                     throw new \Exception("Uploaded file is not valid. Error code: " . $imageFile->getError());
//                 }
                
//                 // Move the file
//                 $imageFile->move(
//                     $imagesDirectory,
//                     $newFilename
//                 );
                
//                 // Verify the file was actually moved
//                 $fullPath = $imagesDirectory . '/' . $newFilename;
                
//                 // Save filename in DB
//                 $artist->setImage($newFilename);
                
//             } catch (FileException $e) {
//                 // Handle exception if something happens during file upload
//                 $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
//                 return $this->redirectToRoute('app_concert_show', ['id' => $id]);
//             } catch (\Exception $e) {
//                 // Catch any other exceptions
//                 $this->addFlash('danger', 'Erreur lors du traitement de l\'image: ' . $e->getMessage());
//                 return $this->redirectToRoute('app_concert_show', ['id' => $id]);
//             }
//         }
        
//         // Persist and flush changes
//         try {
//             $entityManager->persist($artist);
//             $entityManager->flush();
            
//             $this->addFlash('success', 'Concert modifié avec succès.');
//             return $this->redirectToRoute('app_concert_show', ['id' => $id]);
//         } catch (\Exception $e) {
//             $this->addFlash('danger', 'Erreur lors de la sauvegarde des données: ' . $e->getMessage());
//             return $this->redirectToRoute('app_concert_show', ['id' => $id]);
//         }
//     }
    
//     return $this->render('index/concert.html.twig', [
//         'concert' => $concert,
//         'concertDto' => $concertDto,
//         'form' => $form->createView(),
//     ]);
// }


// Route API pour réccuperer les details des artist 
#[Route('/concerts/{id}', name: 'app_concert_details', methods: ['GET'])]
public function getConcertDetails(
    int $id,
    ArtistRepository $artistRepository,
    SerializerInterface $serializer
): JsonResponse {
    // Trouver l'artiste par son ID
    $artist = $artistRepository->find($id);

    // Si l'artiste n'existe pas, retourner une erreur 404
    if (!$artist) {
        return new JsonResponse(['error' => 'Artist not found'], 404);
    }

    // Sérialiser les données de l'artiste avec les scènes associées
    $jsonData = $serializer->serialize($artist, 'json', ['groups' => ['artist:read', 'scene:read']]);

    // Retourner les données en JSON
    return new JsonResponse($jsonData, 200, [], true);
}

// Routes pour lire les données sur les scènes
    #[Route('/scenes', name: 'app_scenes', methods: ['GET'])]
    public function getScenes(SceneRepository $sceneRepository, SerializerInterface $serializer): JsonResponse
    {
        // Touver toutes les scènes en utilisant le repository
        $scenes = $sceneRepository->findAll();
    
        // Serialize les groupes de données avec un json
        $jsonData = $serializer->serialize($scenes, 'json', ['groups' => ['scene:read']]);
    
        // Retourne le json
        return new JsonResponse($jsonData, 200, [], true);
    }

}



