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
        $user->setTimeStamp(new \DateTime());
        $manager->persist($user);

        $manager->flush();

        // Exécuter le script SQL liveEvent.sql après l'insertion de l'utilisateur
 
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
