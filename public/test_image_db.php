<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Symfony
require_once dirname(__DIR__) . '/vendor/autoload.php';
$_SERVER['APP_ENV'] = 'dev';
$kernel = new \App\Kernel($_SERVER['APP_ENV'], true);
$kernel->boot();
$container = $kernel->getContainer();

// Get services
$entityManager = $container->get('doctrine.orm.entity_manager');
$artistRepository = $entityManager->getRepository(\App\Entity\Artist::class);

// CSS for better display
echo '<style>
    body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    .test-block { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    h2 { color: #333; }
    table { border-collapse: collapse; width: 100%; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>';

echo '<h1>Artist Image Field Test</h1>';

// 1. List all artists
echo '<div class="test-block">';
echo '<h2>1. All Artists in Database</h2>';
$artists = $artistRepository->findAll();

echo '<table>';
echo '<tr><th>ID</th><th>Name</th><th>Image Field Value</th><th>Image Preview</th></tr>';
foreach ($artists as $artist) {
    echo '<tr>';
    echo '<td>' . $artist->getId() . '</td>';
    echo '<td>' . htmlspecialchars($artist->getNom()) . '</td>';
    echo '<td>' . ($artist->getImage() ? htmlspecialchars($artist->getImage()) : '<em>NULL</em>') . '</td>';
    echo '<td>';
    if ($artist->getImage()) {
        echo '<img src="/images/' . htmlspecialchars($artist->getImage()) . '" height="50" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
        echo '<span style="display:none; color:red;">Image not found</span>';
    } else {
        echo 'No image';
    }
    echo '</td>';
    echo '</tr>';
}
echo '</table>';
echo '</div>';

// 2. Direct update test
echo '<div class="test-block">';
echo '<h2>2. Direct Update Test</h2>';

// Process form submission
if (isset($_POST['update'])) {
    $artistId = (int)$_POST['artist_id'];
    $newImageValue = $_POST['image_value'];
    
    $artist = $artistRepository->find($artistId);
    
    if (!$artist) {
        echo '<p class="error">Artist with ID ' . $artistId . ' not found!</p>';
    } else {
        try {
            $oldValue = $artist->getImage();
            $artist->setImage($newImageValue);
            $entityManager->flush();
            
            echo '<p class="success">Successfully updated image field for artist ID ' . $artistId . '</p>';
            echo '<p>Old value: ' . ($oldValue ?: '<em>NULL</em>') . '</p>';
            echo '<p>New value: ' . $newImageValue . '</p>';
            
            // Verify update worked
            $entityManager->clear();
            $verifiedArtist = $artistRepository->find($artistId);
            echo '<p>Verified value from database: ' . $verifiedArtist->getImage() . '</p>';
            
            if ($verifiedArtist->getImage() !== $newImageValue) {
                echo '<p class="error">Warning: The verified value does not match what we set!</p>';
            }
        } catch (\Exception $e) {
            echo '<p class="error">Error updating artist: ' . $e->getMessage() . '</p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
    }
}

// Show the form
echo '<form method="post">';
echo '<p><label for="artist_id">Select Artist:</label><br>';
echo '<select name="artist_id" id="artist_id" required>';
foreach ($artists as $artist) {
    echo '<option value="' . $artist->getId() . '">' . htmlspecialchars($artist->getNom()) . ' (ID: ' . $artist->getId() . ')</option>';
}
echo '</select></p>';

echo '<p><label for="image_value">New Image Field Value:</label><br>';
echo '<input type="text" name="image_value" id="image_value" required value="image-9.jpg" style="width: 300px;"></p>';

echo '<p><button type="submit" name="update" value="1">Update Image Field</button></p>';
echo '</form>';
echo '</div>';

// 3. Database schema information
echo '<div class="test-block">';
echo '<h2>3. Database Schema Information</h2>';

try {
    $connection = $entityManager->getConnection();
    $platform = $connection->getDatabasePlatform();
    $schema = $connection->createSchemaManager();
    
    $artistTable = $schema->listTableDetails('artist');
    $imageColumn = $artistTable->getColumn('image');
    
    echo '<p>Table: artist</p>';
    echo '<p>Column: image</p>';
    echo '<p>Type: ' . $imageColumn->getType()->getName() . '</p>';
    echo '<p>Length: ' . $imageColumn->getLength() . '</p>';
    echo '<p>Nullable: ' . ($imageColumn->getNotnull() ? 'No' : 'Yes') . '</p>';
    echo '<p>Default: ' . ($imageColumn->getDefault() ?: 'NULL') . '</p>';
} catch (\Exception $e) {
    echo '<p class="error">Error retrieving schema information: ' . $e->getMessage() . '</p>';
}

echo '</div>';
