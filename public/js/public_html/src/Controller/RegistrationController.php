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

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function registerAdmin(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // mot de pase hashé
            //($plainPassword) must be of type string, null given, 
            // called in C:\Users\daryl\apinewsymfony\src\Controller\RegistrationController.php on line 27
            $user->setPassword(
                $passwordHasher->hashPassword($user, $user->getPlainPassword())
            );
         // Le role est défini comme ROLE_ADMIN par défault
            $user->setRoles(['ROLE_ADMIN']);
            $timestamp = new \DateTime();
            $user->setTimeStamp($timestamp);

            // Persist les donées
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Admin created successfully!');
            return $this->redirectToRoute('app_adminConcerts');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}