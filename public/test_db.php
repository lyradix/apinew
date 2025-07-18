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
</style>';

echo '<h1>Database Direct Update Test</h1>';

// Function to display database info
function displayDatabaseInfo($entityManager) {
    echo '<div class="test-block">';
    echo '<h2>Database Connection Info</h2>';
    
    $connection = $entityManager->getConnection();
    $params = $connection->getParams();
    
    echo '<ul>';
    echo '<li><strong>Database Type:</strong> ' . $params['driver'] . '</li>';
    if (isset($params['host'])) {
        echo '<li><strong>Host:</strong> ' . $params['host'] . '</li>';
    }
    if (isset($params['port'])) {
        echo '<li><strong>Port:</strong> ' . $params['port'] . '</li>';
    }
    if (isset($params['dbname'])) {
        echo '<li><strong>Database Name:</strong> ' . $params['dbname'] . '</li>';
    }
    if (isset($params['user'])) {
        echo '<li><strong>User:</strong> ' . $params['user'] . '</li>';
    }
    echo '</ul>';
    
    // Check if we can actually connect
    try {
        $connection->connect();
        echo '<p class="success">Connection successful!</p>';
    } catch (\Exception $e) {
        echo '<p class="error">Connection failed: ' . $e->getMessage() . '</p>';
    }
    
    echo '</div>';
}

// Function to update an artist and verify the change
function updateAndVerifyArtist($artistId, $newImageValue, $entityManager, $artistRepository) {
    echo '<div class="test-block">';
    echo '<h2>Updating Artist ID: ' . $artistId . '</h2>';
    
    try {
        // Find the artist
        $artist = $artistRepository->find($artistId);
        
        if (!$artist) {
            echo '<p class="error">Artist with ID ' . $artistId . ' not found!</p>';
            echo '</div>';
            return;
        }
        
        // Display current values
        echo '<h3>Before Update</h3>';
        echo '<ul>';
        echo '<li><strong>Name:</strong> ' . $artist->getNom() . '</li>';
        echo '<li><strong>Image:</strong> ' . ($artist->getImage() ?: '<em>NULL</em>') . '</li>';
        echo '</ul>';
        
        // Create SQL logger to capture queries
        $queries = [];
        $logger = new class($queries) implements \Doctrine\DBAL\Logging\SQLLogger {
            private $queries;
            
            public function __construct(&$queries) {
                $this->queries = &$queries;
            }
            
            public function startQuery($sql, ?array $params = null, ?array $types = null): void {
                $this->queries[] = [
                    'sql' => $sql,
                    'params' => $params,
                    'types' => $types
                ];
            }
            
            public function stopQuery(): void {}
        };
        
        // Set up logging
        $connection = $entityManager->getConnection();
        $oldLogger = $connection->getConfiguration()->getSQLLogger();
        $connection->getConfiguration()->setSQLLogger($logger);
        
        // Perform the update
        $oldImage = $artist->getImage();
        $artist->setImage($newImageValue);
        $entityManager->flush();
        
        // Restore logger
        $connection->getConfiguration()->setSQLLogger($oldLogger);
        
        // Display SQL
        echo '<h3>SQL Executed</h3>';
        echo '<pre>';
        foreach ($queries as $query) {
            echo htmlspecialchars($query['sql']) . "\n";
            if (!empty($query['params'])) {
                echo "Parameters: " . print_r($query['params'], true);
            }
            echo "\n";
        }
        echo '</pre>';
        
        // Verify the update
        $entityManager->clear(); // Clear entity manager to force reload from database
        $refreshedArtist = $artistRepository->find($artistId);
        
        echo '<h3>After Update</h3>';
        echo '<ul>';
        echo '<li><strong>Name:</strong> ' . $refreshedArtist->getNom() . '</li>';
        echo '<li><strong>Old Image Value:</strong> ' . ($oldImage ?: '<em>NULL</em>') . '</li>';
        echo '<li><strong>New Image Value:</strong> ' . ($refreshedArtist->getImage() ?: '<em>NULL</em>') . '</li>';
        echo '</ul>';
        
        if ($refreshedArtist->getImage() === $newImageValue) {
            echo '<p class="success">UPDATE SUCCESSFUL! The database was updated correctly.</p>';
        } else {
            echo '<p class="error">UPDATE FAILED! Expected: "' . $newImageValue . '", Got: "' . 
                ($refreshedArtist->getImage() ?: '<em>NULL</em>') . '"</p>';
        }
        
    } catch (\Exception $e) {
        echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    
    echo '</div>';
}

// Display connection info
displayDatabaseInfo($entityManager);

// Display all artists
echo '<div class="test-block">';
echo '<h2>All Artists</h2>';
echo '<table border="1" style="width: 100%; border-collapse: collapse;">';
echo '<tr><th>ID</th><th>Name</th><th>Image</th></tr>';

$artists = $artistRepository->findAll();
foreach ($artists as $artist) {
    echo '<tr>';
    echo '<td>' . $artist->getId() . '</td>';
    echo '<td>' . htmlspecialchars($artist->getNom()) . '</td>';
    echo '<td>' . ($artist->getImage() ?: '<em>NULL</em>') . '</td>';
    echo '</tr>';
}

echo '</table>';
echo '</div>';

// Run a test update on artist ID 2 (or another ID if specified)
$testArtistId = isset($_GET['id']) ? (int)$_GET['id'] : 2;
$newImageValue = 'test-image-' . date('YmdHis') . '.jpg';

updateAndVerifyArtist($testArtistId, $newImageValue, $entityManager, $artistRepository);

// Display link to run with different ID
echo '<p>Test with different artist ID: ';
foreach ($artists as $artist) {
    echo '<a href="?id=' . $artist->getId() . '">' . $artist->getId() . '</a> ';
}
echo '</p>';
