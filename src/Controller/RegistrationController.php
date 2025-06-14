<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{

    // Route pour enregistrer un nouvel administrateur
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function registerAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // mot de pase hashé
            $user->setPassword(
                $passwordHasher->hashPassword($user, $user->getPassword())
            );
         // Le role est défini comme ROLE_ADMIN par défault
            $user->setRoles(['ROLE_ADMIN']);

            // Persist les donées
            $entityManager->persist($user);
            $entityManager->flush();

            return new Response('Admin created successfully!', Response::HTTP_CREATED);
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
