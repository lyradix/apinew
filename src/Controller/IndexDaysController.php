<?php

namespace App\Controller;

use App\Entity\Days;
use App\Form\DayformType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DaysRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexDaysController extends AbstractController
{
    #[Route('/getdays', name: 'days_form', methods: ['GET'])]
    public function handleDaysForm(
        Request $request,
        EntityManagerInterface $entityManager,
        DaysRepository $daysRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return new Response('User is not logged in', Response::HTTP_UNAUTHORIZED);
        }

        // Fetch the days associated with the logged-in user
        $userDays = $daysRepository->findBy(['userFK' => $user]);

        // Create a new Days entity and form
        $days = new Days();
        $form = $this->createForm(DayformType::class, $days);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the checkboxes are selected and set the corresponding day
            if ($form->get('vendredi')->getData()) {
                $days->setJour('vendredi');
            }
            if ($form->get('samedi')->getData()) {
                $days->setJour('samedi');
            }
            if ($form->get('dimanche')->getData()) {
                $days->setJour('dimanche');
            }

            // Link the day to the logged-in user using setUserFK
            $days->setUserFK($user);
            $entityManager->persist($days);
            $entityManager->flush();

            $this->addFlash('success', 'Days saved successfully!');

            return $this->redirectToRoute('days_form');
        }

        return $this->render('index_days/index.html.twig', [
            'form' => $form->createView(),
            'userDays' => $userDays,
        ]);
    }

    #[Route('/postdays', name: 'days_api', methods: ['POST'])]
    public function handleDaysApi(
        Request $request,
        EntityManagerInterface $entityManager,
        DaysRepository $daysRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User is not logged in'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($request->isMethod('GET')) {
            // Handle GET request: Return the days associated with the logged-in user
            $userDays = $daysRepository->findBy(['userFK' => $user]);

            $daysData = array_map(function (Days $day) {
                return [
                    'id' => $day->getId(),
                    'jour' => $day->getJour(),
                ];
            }, $userDays);

            return new JsonResponse(['days' => $daysData], JsonResponse::HTTP_OK);
        }

        if ($request->isMethod('POST')) {
            // Handle POST request: Save new days for the logged-in user
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['days']) || !is_array($data['days'])) {
                return new JsonResponse(['error' => 'Invalid JSON payload or "days" field is missing'], JsonResponse::HTTP_BAD_REQUEST);
            }

            foreach ($data['days'] as $day) {
                if (!in_array($day, ['vendredi', 'samedi', 'dimanche'])) {
                    return new JsonResponse(['error' => 'Invalid day: ' . $day], JsonResponse::HTTP_BAD_REQUEST);
                }

                $days = new Days();
                $days->setJour($day);
                $days->setUserFK($user);
                $entityManager->persist($days);
            }

            $entityManager->flush();

            return new JsonResponse(['message' => 'Days saved successfully!'], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Method not allowed'], JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }
}
