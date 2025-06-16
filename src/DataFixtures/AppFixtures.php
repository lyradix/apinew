<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User; 
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
     
        $user = new User();
        $user->setEmail('admin@nationsound.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'Password123')
        );
        $manager->persist($user);

        $manager->flush();

        // Exécuter le script SQL liveEvent.sql après l'insertion de l'utilisateur
        // Les données seront envoyées dans la base de données en requêtes SQL (RAW SQL)
        // En raison d'une impcompatibilité  de CrEOF\Spatial\DBAL\Types\Geometry avec mySQL 8.0,
        // bcremer/doctrine-mysql-spatial n'est pas envisageable parce que l'application utilise l'ORM version 3.2
        // une downgrade de l'ORM n'est pas envisageable non plus en raison d'imcompatibilité avec les dépendances déjà installées
        $connection = $manager->getConnection();
        $sqlFile = __DIR__ . '/data/liveEvent.sql'; // Assurez-vous que le chemin est correct

        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $connection->executeStatement($sql);
        } else {
            throw new \RuntimeException("Le fichier SQL $sqlFile est introuvable.");
        }
    }
}
