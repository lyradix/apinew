<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator; 
class SecurityController extends AbstractController
{

    // Route pour la page de connexion
    // Cette route est utilisée pour afficher le formulaire de connexion
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $AuthenticationUtils): Response
    {
    
        $error = $AuthenticationUtils->getLastAuthenticationError();

        $lastUsername = $AuthenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    
    #[Route(path: '/logout', name: 'app_logout', methods: ['GET','POST'])]
    public function logout(): void
    {
        throw new \LogicException('');
    }

    #[Route('/register-admin', name: 'app_register_admin')]
    public function registerAdmin(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setEmail(User::DEFAULT_ADMIN_EMAIL);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $passwordHasher->hashPassword($user, User::DEFAULT_ADMIN_PASSWORD)
        );
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Admin créé avec succés!');
    }
}

