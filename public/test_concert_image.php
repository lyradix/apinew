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
    form { margin-top: 20px; }
    input, button { padding: 8px; margin: 5px 0; }
    table { border-collapse: collapse; width: 100%; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>';

echo '<h1>Concert Image Upload Test</h1>';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['image'];
        $concertId = isset($_POST['concert_id']) ? (int)$_POST['concert_id'] : null;
        
        if (!$concertId) {
            echo '<div class="error">No concert ID provided</div>';
        } else {
            $concert = $artistRepository->find($concertId);
            
            if (!$concert) {
                echo '<div class="error">Concert with ID ' . $concertId . ' not found</div>';
            } else {
                // Process the file
                $originalFilename = pathinfo($uploadedFile['name'], PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename
                );
                $newFilename = $safeFilename . '-' . uniqid() . '.' . pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
                
                // Move the file
                $uploadsDirectory = dirname(__DIR__) . '/public/images';
                if (!is_dir($uploadsDirectory)) {
                    mkdir($uploadsDirectory, 0777, true);
                }
                
                if (move_uploaded_file($uploadedFile['tmp_name'], $uploadsDirectory . '/' . $newFilename)) {
                    // Update the database
                    try {
                        $oldImage = $concert->getImage();
                        $concert->setImage($newFilename);
                        $entityManager->flush();
                        
                        echo '<div class="success">';
                        echo '<p>Image uploaded and database updated successfully!</p>';
                        echo '<p>Old image: ' . ($oldImage ?: '<em>NULL</em>') . '</p>';
                        echo '<p>New image: ' . $newFilename . '</p>';
                        echo '<p><img src="/images/' . htmlspecialchars($newFilename) . '" height="100"></p>';
                        echo '</div>';
                        
                        // Debug info
                        echo '<div class="test-block">';
                        echo '<h2>Database Verification</h2>';
                        
                        // Verify by refreshing from database
                        $entityManager->clear(); // Clear entity manager to force reload from database
                        $refreshedConcert = $artistRepository->find($concertId);
                        
                        echo '<p><strong>After database refresh:</strong> ' . 
                             ($refreshedConcert->getImage() === $newFilename ? 
                             '<span class="success">SUCCESS! Database shows: ' . $refreshedConcert->getImage() . '</span>' : 
                             '<span class="error">FAILURE! Database shows: ' . $refreshedConcert->getImage() . '</span>') . 
                             '</p>';
                        
                        echo '</div>';
                    } catch (\Exception $e) {
                        echo '<div class="error">';
                        echo '<p>Database update failed: ' . $e->getMessage() . '</p>';
                        echo '<pre>' . $e->getTraceAsString() . '</pre>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="error">Failed to move uploaded file</div>';
                }
            }
        }
    } else if (isset($_FILES['image'])) {
        echo '<div class="error">File upload error: ' . $_FILES['image']['error'] . '</div>';
    }
}

// Display all concerts
echo '<div class="test-block">';
echo '<h2>Available Concerts</h2>';

$concerts = $artistRepository->findAll();

echo '<table>';
echo '<tr><th>ID</th><th>Name</th><th>Image</th><th>Actions</th></tr>';

foreach ($concerts as $concert) {
    echo '<tr>';
    echo '<td>' . $concert->getId() . '</td>';
    echo '<td>' . htmlspecialchars($concert->getNom()) . '</td>';
    echo '<td>';
    
    if ($concert->getImage()) {
        echo htmlspecialchars($concert->getImage()) . '<br>';
        echo '<img src="/images/' . htmlspecialchars($concert->getImage()) . '" height="50" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
        echo '<span style="display:none; color:red;">Image not found</span>';
    } else {
        echo '<em>No image</em>';
    }
    
    echo '</td>';
    echo '<td><a href="?id=' . $concert->getId() . '">Select</a></td>';
    echo '</tr>';
}

echo '</table>';
echo '</div>';

// Display upload form for the selected concert
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : 2; // Default to ID 2
$selectedConcert = $artistRepository->find($selectedId);

if ($selectedConcert) {
    echo '<div class="test-block">';
    echo '<h2>Upload Image for: ' . htmlspecialchars($selectedConcert->getNom()) . '</h2>';
    
    echo '<p><strong>Current Image:</strong> ' . ($selectedConcert->getImage() ?: '<em>None</em>') . '</p>';
    
    if ($selectedConcert->getImage()) {
        echo '<p><img src="/images/' . htmlspecialchars($selectedConcert->getImage()) . '" height="100" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
        echo '<span style="display:none; color:red;">Image not found</span></p>';
    }
    
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="hidden" name="concert_id" value="' . $selectedConcert->getId() . '">';
    echo '<p><input type="file" name="image" required></p>';
    echo '<p><button type="submit">Upload and Update</button></p>';
    echo '</form>';
    echo '</div>';
}

// Show Entity and DB schema information
echo '<div class="test-block">';
echo '<h2>Entity and Database Information</h2>';

try {
    echo '<h3>Entity Definition</h3>';
    $reflClass = new ReflectionClass(\App\Entity\Artist::class);
    $properties = $reflClass->getProperties();
    
    echo '<table>';
    echo '<tr><th>Property</th><th>Type</th><th>Annotations</th></tr>';
    
    foreach ($properties as $property) {
        $docComment = $property->getDocComment();
        echo '<tr>';
        echo '<td>' . $property->getName() . '</td>';
        echo '<td>' . ($property->getType() ? $property->getType()->getName() : 'undefined') . '</td>';
        echo '<td>' . ($docComment ? htmlspecialchars($docComment) : 'No annotations') . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    
    echo '<h3>Database Schema</h3>';
    $schemaManager = $entityManager->getConnection()->createSchemaManager();
    $columns = $schemaManager->listTableColumns('artist');
    
    echo '<table>';
    echo '<tr><th>Column</th><th>Type</th><th>Length</th><th>Nullable</th><th>Default</th></tr>';
    
    foreach ($columns as $column) {
        echo '<tr>';
        echo '<td>' . $column->getName() . '</td>';
        echo '<td>' . $column->getType()->getName() . '</td>';
        echo '<td>' . $column->getLength() . '</td>';
        echo '<td>' . ($column->getNotnull() ? 'No' : 'Yes') . '</td>';
        echo '<td>' . ($column->getDefault() ?: 'NULL') . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
} catch (\Exception $e) {
    echo '<p class="error">Error retrieving schema information: ' . $e->getMessage() . '</p>';
}

echo '</div>';
