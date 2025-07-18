<?php
// This script will directly test saving an image filename to the Artist entity's image field

require_once __DIR__ . '/../vendor/autoload.php';

use App\Kernel;
use App\Entity\Artist;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Dotenv\Dotenv;

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();
$container = $kernel->getContainer();

$entityManager = $container->get('doctrine.orm.entity_manager');

try {
    // Create a test image file
    $targetDir = __DIR__ . '/images';
    $imageFilename = 'test-image-' . uniqid() . '.png';
    $imagePath = $targetDir . '/' . $imageFilename;
    
    // Create a simple 1x1 PNG image
    $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    file_put_contents($imagePath, $imageData);
    
    error_log("Test image created: $imagePath");
    
    // Get an existing Artist entity or create a new one
    $artistRepository = $entityManager->getRepository(Artist::class);
    $artist = $artistRepository->find(2); // Using ID 2 as mentioned in your code
    
    if (!$artist) {
        error_log("Artist with ID 2 not found, creating a new one");
        $artist = new Artist();
        $artist->setNom('Test Artist');
        $artist->setFamousSong('Test Song');
        $artist->setGenre('Test Genre');
        $artist->setDescription('Test Description');
        $artist->setSource('Test Source');
        $artist->setLien('Test Link');
        $artist->setStartTime(new \DateTime());
        $artist->setEndTime(new \DateTime('+1 hour'));
    }
    
    // Get the original image value
    $originalImage = $artist->getImage();
    error_log("Original image value: " . ($originalImage ?: 'NULL'));
    
    // Update the image field
    $artist->setImage($imageFilename);
    error_log("Image field updated to: " . $artist->getImage());
    
    // Save to database
    $entityManager->persist($artist);
    $entityManager->flush();
    
    // Verify the save worked
    $updatedArtist = $artistRepository->find($artist->getId());
    error_log("Artist ID: " . $updatedArtist->getId());
    error_log("Saved image value: " . ($updatedArtist->getImage() ?: 'NULL'));
    
    echo "Test completed. Check error logs for results.";
    
} catch (\Exception $e) {
    error_log("Error: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    echo "Error: " . $e->getMessage();
}
