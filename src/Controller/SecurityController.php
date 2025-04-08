<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserProviderInterface $userProvider,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate input
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $email = $data['email'];
        $password = $data['password'];

        try {
            // Load the user by email
            $user = $userProvider->loadUserByIdentifier($email);

            // Check the password
            if (!$passwordHasher->isPasswordValid($user, $password)) {
                throw new BadCredentialsException('Invalid credentials');
            }

            // Return a success response

            return new JsonResponse([
                'success' => true,
                'message' => 'Login successful'
            ], JsonResponse::HTTP_OK);
            
        } catch (BadCredentialsException | AuthenticationException $e) {
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
