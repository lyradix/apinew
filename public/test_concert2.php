<?php
// Simple test for direct form upload and database update
// with detailed debugging for concert ID 2

// Set to display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Boot Symfony kernel
$env = 'dev';
$debug = true;
$kernel = new \App\Kernel($env, $debug);
$kernel->boot();
$container = $kernel->getContainer();

// Get Doctrine EntityManager
$entityManager = $container->get('doctrine.orm.entity_manager');
$artistRepository = $entityManager->getRepository(\App\Entity\Artist::class);

echo "<h1>Concert ID 2 Testing</h1>";

// Find the concert with ID 2
$concert = $artistRepository->find(2);

if (!$concert) {
    echo "<p style='color:red'>Concert with ID 2 not found!</p>";
    exit;
}

echo "<p>Found concert with ID 2: " . htmlspecialchars($concert->getNom()) . "</p>";
echo "<p>Current image: " . ($concert->getImage() ? htmlspecialchars($concert->getImage()) : "None") . "</p>";

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Processing Upload</h2>";
    
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        echo "<p>Received file: " . htmlspecialchars($_FILES['imageFile']['name']) . "</p>";
        
        // Create a unique filename
        $originalFilename = pathinfo($_FILES['imageFile']['name'], PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-z0-9]+/', '-', strtolower($originalFilename));
        $newFilename = $safeFilename . '-' . uniqid() . '.' . pathinfo($_FILES['imageFile']['name'], PATHINFO_EXTENSION);
        
        // Get the image directory parameter
        $targetDirectory = $container->getParameter('images_directory');
        $targetFile = $targetDirectory . '/' . $newFilename;
        
        echo "<p>Target directory: " . htmlspecialchars($targetDirectory) . "</p>";
        
        // Try to move the uploaded file
        if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetFile)) {
            echo "<p style='color:green'>File uploaded successfully!</p>";
            
            // Update the database
            try {
                // Save old image for reference
                $oldImage = $concert->getImage();
                
                // Update the entity
                $concert->setImage($newFilename);
                $entityManager->persist($concert);
                $entityManager->flush();
                
                echo "<p style='color:green'>Database updated successfully!</p>";
                echo "<p>Previous image: " . ($oldImage ?: "None") . "</p>";
                echo "<p>New image: " . htmlspecialchars($newFilename) . "</p>";
                
                // Verify the update
                $entityManager->clear();
                $verifiedConcert = $artistRepository->find(2);
                echo "<p>Verified image in database: " . 
                    ($verifiedConcert->getImage() ? htmlspecialchars($verifiedConcert->getImage()) : "None") . "</p>";
                
                if ($verifiedConcert->getImage() === $newFilename) {
                    echo "<p style='color:green'>Image update confirmed in database!</p>";
                } else {
                    echo "<p style='color:red'>Database verification failed! Image name mismatch.</p>";
                }
                
                // Show the image
                echo "<p>Image preview:</p>";
                echo "<img src='/images/" . htmlspecialchars($newFilename) . "' style='max-width:300px;'>";
            } catch (\Exception $e) {
                echo "<p style='color:red'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p style='color:red'>Failed to move uploaded file!</p>";
        }
    } else {
        echo "<p style='color:red'>No file uploaded or upload error occurred.</p>";
        if (isset($_FILES['imageFile'])) {
            echo "<p>Error code: " . $_FILES['imageFile']['error'] . "</p>";
        }
    }
}

// Show upload form
?>
<form method="post" enctype="multipart/form-data">
    <p>
        <label for="imageFile">Select Image:</label><br>
        <input type="file" name="imageFile" id="imageFile" required>
    </p>
    <p>
        <button type="submit">Upload Image</button>
    </p>
</form>
