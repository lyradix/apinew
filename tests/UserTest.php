<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    //La méthode testEmailValidation() est utilisée pour tester la validation de l'email
    public function testEmailValidation(): void
    {
        self::bootKernel(); //afin de démarrrer le kernel Symfony
         // Récupération de l'interface UserInterface depuis le conteneur de services
        $validator = self::getContainer()->get('validator');

        $user = new User();    //création d'une nouvelle instance
        $user->setEmail('john.doe@something.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('password123');

        // Vérification que l'email est correctement défini
        $errors = $validator->validate($user);
        $this->assertCount(0, $errors, 'L\'email doit être valide');

        // Test avec un email invalide
        $user->setEmail('invalid-email');
        $errors = $validator->validate($user);
        $this->assertCount(1, $errors, 'L\'email doit être invalide');

        // Test avec un email vide
        $user->setEmail('');
        $errors = $validator->validate($user);
        $this->assertCount(1, $errors, 'L\'email ne doit pas être vide');

        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}

