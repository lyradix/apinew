<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        // User $user,
        UserProviderInterface $userProvider,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate input
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Merci de renseigner l\'email et le mot de passe'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $email = $data['email'];
        $password = $data['password'];

        try {
            // Load the user by email
            $user = $userProvider->loadUserByIdentifier($email);

            if (!$user) {
                return new JsonResponse(['error' => 'Utilisateur introuvable'], JsonResponse::HTTP_NOT_FOUND);
            }

            // Check the password
            if (!$passwordHasher->isPasswordValid($user, $password)) {
                throw new BadCredentialsException('Identifiants ou mot de passe invalides');
            }

            // Génère un token
            $token = bin2hex(random_bytes(32));
            $user->setApiToken($token); // Assuming your User entity has an `apiToken` field
            $entityManager->persist($user);
            $entityManager->flush();

            // Retour du Token dans le response
            return new JsonResponse(['token' => $token], JsonResponse::HTTP_OK);
        } catch (BadCredentialsException | AuthenticationException $e) {
            return new JsonResponse(['error' => 'Identifiants ou mot de passe invalides'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \LogicException('');
    }
}

