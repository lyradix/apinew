<?php

namespace App\Tests;

use App\Entity\Poi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PoiTest extends KernelTestCase

{
    //pour tester l'entrée de longitude et latitude
    public function testPoiCreation(): void
    {
        self::bootKernel(); // Démarre le kernel Symfony

        $poi = new Poi();
        $poi->setType('restaurant');
        $poi->setNom('Kebab');
        $poi->setLongitude('2.3522');
        $poi->setLatitude('48.8566');

        // Vérification que les propriétés sont correctement définies
        $this->assertEquals('restaurant', $poi->getType());
        $this->assertEquals('Kebab', $poi->getNom());
        $this->assertEquals('2.3522', $poi->getLongitude());
        $this->assertEquals('48.8566', $poi->getLatitude());
    }
    //pour tester la validation de l'entrée de longitude et latitude
    public function testPoiValidation(): void
    {
        self::bootKernel(); // Démarre le kernel Symfony

        $validator = self::getContainer()->get('validator');

        $poi = new Poi();
        $poi->setType('restaurant');
        $poi->setNom('Kebab');
        $poi->setLongitude('2.3522');
        $poi->setLatitude('48.8566');

        // Vérification que les propriétés sont correctement définies
        $errors = $validator->validate($poi);
        $this->assertCount(0, $errors, 'Le POI doit être valide');

        // Test avec des coordonnées invalides
        $poi->setLongitude('invalid-longitude');
        $errors = $validator->validate($poi);
        $this->assertCount(1, $errors, 'La longitude doit être invalide');

        // Test avec des coordonnées vides
        $poi->setLongitude('');
        $errors = $validator->validate($poi);
        $this->assertCount(1, $errors, 'La longitude ne doit pas être vide');
    }
}

