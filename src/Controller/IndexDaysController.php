<?php

namespace App\Controller;

use App\Entity\Days;
use App\Entity\User;
use App\Repository\DaysRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexDaysController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/getdays', name: 'get_days', methods: ['GET'])]
    public function getDays(
        Request $request,
        DaysRepository $daysRepository
    ): JsonResponse {
        // Validate the token and get the authenticated user
        $user = $this->validateToken($request);

        if (!$user instanceof User) {
            return $user; // Return the error response from validateToken()
        }

        // Fetch the days associated with the authenticated user
        $userDays = $daysRepository->findBy(['userFK' => $user]);

        // Format the response
        $daysData = array_map(function (Days $day) {
            return [
                'id' => $day->getId(),
                'jour' => $day->getJour(),
            ];
        }, $userDays);

        return new JsonResponse(['days' => $daysData], JsonResponse::HTTP_OK);
    }

    #[Route('/postdays', name: 'post_days', methods: ['POST'])]
    public function postDays(
        Request $request,
        EntityManagerInterface $entityManager,
        DaysRepository $daysRepository
    ): JsonResponse {
        // Validate the token and get the authenticated user
        $user = $this->validateToken($request);

        if (!$user instanceof User) {
            return $user; // Return the error response from validateToken()
        }

        // Decode the JSON payload
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['days']) || !is_array($data['days'])) {
            return new JsonResponse(['error' => 'Invalid JSON payload or "days" field is missing'], JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($data['days'] as $jour) {
            if (!in_array($jour, ['vendredi', 'samedi', 'dimanche'])) {
                return new JsonResponse(['error' => 'Invalid day: ' . $jour], JsonResponse::HTTP_BAD_REQUEST);
            }

            $days = new Days();
            $days->setJour($jour);
            $days->setUserFK($user);
            $entityManager->persist($days);
        }

        $entityManager->flush();

        // Retrieve the token from the authenticated user
        $token = $user->getApiToken();

        return new JsonResponse([
            'message' => 'Days saved successfully!',
            'token' => $token, // Include the token in the response
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/formdays', name: 'form_days', methods: ['GET'])]
    public function formDays(
        Request $request,
        DaysRepository $daysRepository
    ): JsonResponse {
        // Validate the token and get the authenticated user
        $user = $this->validateToken($request);

        if (!$user instanceof User) {
            return $user; // Return the error response from validateToken()
        }

        // Fetch the days associated with the authenticated user
        $userDays = $daysRepository->findBy(['userFK' => $user]);

        // Extract the days (e.g., ['vendredi', 'samedi'])
        $associatedDays = array_map(function (Days $day) {
            return $day->getJour();
        }, $userDays);

        // Define all possible days
        $allDays = ['vendredi', 'samedi', 'dimanche'];

        // Build the response structure
        $formDays = array_map(function ($day) use ($associatedDays) {
            return [
                'day' => $day,
                'selected' => in_array($day, $associatedDays), // Mark as selected if associated with the user
            ];
        }, $allDays);

        return new JsonResponse([
            'formDays' => $formDays,
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/choosedays', name: 'choose_days', methods: ['POST'])]
    public function chooseDays(
        Request $request,
        EntityManagerInterface $entityManager,
        DaysRepository $daysRepository
    ): JsonResponse {
        // Validate the token and get the authenticated user
        $user = $this->validateToken($request);

        if (!$user instanceof User) {
            return $user; // Return the error response from validateToken()
        }

        // Decode the JSON payload
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['days']) || !is_array($data['days'])) {
            return new JsonResponse(['error' => 'Invalid JSON payload or "days" field is missing'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate the days
        $validDays = ['vendredi', 'samedi', 'dimanche'];
        foreach ($data['days'] as $day) {
            if (!in_array($day, $validDays)) {
                return new JsonResponse(['error' => 'Invalid day: ' . $day], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // Remove all existing days associated with the user
        $existingDays = $daysRepository->findBy(['userFK' => $user]);
        foreach ($existingDays as $existingDay) {
            $entityManager->remove($existingDay);
        }

        // Add the new days
        foreach ($data['days'] as $day) {
            $newDay = new Days();
            $newDay->setJour($day);
            $newDay->setUserFK($user);
            $entityManager->persist($newDay);
        }

        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Days updated successfully!',
        ], JsonResponse::HTTP_OK);
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
