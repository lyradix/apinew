<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Configure environment variable
$_SERVER['APP_ENV'] = 'dev';

// Initialize Symfony kernel
$kernel = new \App\Kernel($_SERVER['APP_ENV'], true);
$kernel->boot();

// Get container
$container = $kernel->getContainer();

// Get entity manager and repository
$entityManager = $container->get('doctrine.orm.entity_manager');
$artistRepository = $container->get('doctrine')->getRepository(\App\Entity\Artist::class);

// Find artist with ID 2
$artist = $artistRepository->find(2);

error_log("Test script started for artist ID 2");

if (!$artist) {
    error_log("Artist ID 2 not found");
    echo "Artist ID 2 not found";
    exit;
}

error_log("Found artist ID 2, current image: " . $artist->getImage());
echo "Found artist ID 2: " . $artist->getNom() . "<br>";
echo "Current image: " . $artist->getImage() . "<br>";

// Update the image field
$newImageName = 'test-image-update-' . uniqid() . '.jpg';
$artist->setImage($newImageName);

// Persist changes
try {
    $entityManager->persist($artist);
    $entityManager->flush();
    error_log("Successfully updated artist ID 2 with image: " . $newImageName);
    echo "Successfully updated image to: " . $newImageName;
} catch (\Exception $e) {
    error_log("Error updating artist: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
