<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Decode the JSON payload from the request
        $data = json_decode($request->getContent(), true);

        // Validate the input data
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(['error' => 'Invalid data. Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        // Check if the user already exists
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['error' => 'User with this email already exists.'], Response::HTTP_CONFLICT);
        }

        // Create a new user
        $user = new User();
        $user->setEmail($data['email']);
        $hashedPassword = $userPasswordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']); // Assign default role

        // Persist the user to the database
        $entityManager->persist($user);
        $entityManager->flush();

        // Return a success response
        return $this->json(['message' => 'User registered successfully.'], Response::HTTP_CREATED);
    }
}
