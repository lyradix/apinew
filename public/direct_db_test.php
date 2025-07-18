<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Output HTML header and styles
echo '<!DOCTYPE html>
<html>
<head>
    <title>Direct Database Update Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h1>Direct Database Update Test</h1>';

// Get the database connection parameters from the Symfony .env file
try {
    $envFile = file_get_contents(__DIR__ . '/../.env');
    
    // Parse DATABASE_URL
    if (preg_match('/DATABASE_URL="?([^"]+)"?/', $envFile, $matches)) {
        $databaseUrl = $matches[1];
        echo '<div class="section">';
        echo '<h2>Database Connection String</h2>';
        echo '<p>' . htmlspecialchars($databaseUrl) . '</p>';
        
        // Parse the connection parameters
        $parts = parse_url($databaseUrl);
        $dbParams = [
            'driver' => $parts['scheme'] === 'mysql' ? 'mysqli' : $parts['scheme'],
            'host' => $parts['host'] ?? 'localhost',
            'port' => $parts['port'] ?? 3306,
            'dbname' => ltrim($parts['path'] ?? '', '/'),
            'user' => $parts['user'] ?? '',
            'password' => $parts['pass'] ?? ''
        ];
        
        echo '<h3>Connection Parameters</h3>';
        echo '<ul>';
        foreach ($dbParams as $key => $value) {
            if ($key !== 'password') {
                echo '<li><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
            } else {
                echo '<li><strong>' . htmlspecialchars($key) . ':</strong> ********</li>';
            }
        }
        echo '</ul>';
        echo '</div>';
        
        // Try to connect to the database directly
        echo '<div class="section">';
        echo '<h2>Direct Database Connection Test</h2>';
        
        try {
            // Create connection
            if ($dbParams['driver'] === 'mysqli') {
                $conn = new mysqli($dbParams['host'], $dbParams['user'], $dbParams['password'], $dbParams['dbname'], $dbParams['port']);
                
                // Check connection
                if ($conn->connect_error) {
                    throw new Exception("Connection failed: " . $conn->connect_error);
                }
                
                echo '<p class="success">Connected to MySQL successfully!</p>';
                
                // Select an artist with ID 2 (or ID from GET parameter)
                $artistId = isset($_GET['id']) ? (int)$_GET['id'] : 2;
                echo '<h3>Updating Artist with ID ' . $artistId . '</h3>';
                
                // First, fetch current data
                $sql = "SELECT id, nom, image FROM artist WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $artistId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo '<p><strong>Before Update:</strong></p>';
                    echo '<ul>';
                    echo '<li>ID: ' . $row['id'] . '</li>';
                    echo '<li>Name: ' . htmlspecialchars($row['nom']) . '</li>';
                    echo '<li>Image: ' . (is_null($row['image']) ? '<em>NULL</em>' : htmlspecialchars($row['image'])) . '</li>';
                    echo '</ul>';
                    
                    // Now update the image field
                    $newImageValue = 'direct-update-test-' . date('YmdHis') . '.jpg';
                    $updateSql = "UPDATE artist SET image = ? WHERE id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("si", $newImageValue, $artistId);
                    
                    if ($updateStmt->execute()) {
                        echo '<p class="success">Update successful! Affected rows: ' . $updateStmt->affected_rows . '</p>';
                        
                        // Verify the update
                        $verifySql = "SELECT id, nom, image FROM artist WHERE id = ?";
                        $verifyStmt = $conn->prepare($verifySql);
                        $verifyStmt->bind_param("i", $artistId);
                        $verifyStmt->execute();
                        $verifyResult = $verifyStmt->get_result();
                        
                        if ($verifyResult->num_rows > 0) {
                            $verifyRow = $verifyResult->fetch_assoc();
                            echo '<p><strong>After Update:</strong></p>';
                            echo '<ul>';
                            echo '<li>ID: ' . $verifyRow['id'] . '</li>';
                            echo '<li>Name: ' . htmlspecialchars($verifyRow['nom']) . '</li>';
                            echo '<li>Image: ' . (is_null($verifyRow['image']) ? '<em>NULL</em>' : htmlspecialchars($verifyRow['image'])) . '</li>';
                            echo '</ul>';
                            
                            if ($verifyRow['image'] === $newImageValue) {
                                echo '<p class="success">Verification successful! The image was updated correctly.</p>';
                            } else {
                                echo '<p class="error">Verification failed! Expected: "' . $newImageValue . '", Got: "' . 
                                    (is_null($verifyRow['image']) ? '<em>NULL</em>' : htmlspecialchars($verifyRow['image'])) . '"</p>';
                            }
                        }
                        
                        // List all artists to see changes
                        echo '<h3>All Artists in Database</h3>';
                        $allSql = "SELECT id, nom, image FROM artist ORDER BY id";
                        $allResult = $conn->query($allSql);
                        
                        if ($allResult->num_rows > 0) {
                            echo '<table border="1" style="width: 100%; border-collapse: collapse;">';
                            echo '<tr><th>ID</th><th>Name</th><th>Image</th></tr>';
                            
                            while($row = $allResult->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $row['id'] . '</td>';
                                echo '<td>' . htmlspecialchars($row['nom']) . '</td>';
                                echo '<td>' . (is_null($row['image']) ? '<em>NULL</em>' : htmlspecialchars($row['image'])) . '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</table>';
                        } else {
                            echo '<p>No artists found in database.</p>';
                        }
                    } else {
                        echo '<p class="error">Update failed: ' . $conn->error . '</p>';
                    }
                } else {
                    echo '<p class="error">Artist with ID ' . $artistId . ' not found</p>';
                }
                
                // Close connection
                $conn->close();
            } elseif ($dbParams['driver'] === 'pgsql') {
                // PostgreSQL connection code would go here
                echo '<p class="error">PostgreSQL support not implemented yet</p>';
            } else {
                echo '<p class="error">Unsupported database type: ' . $dbParams['driver'] . '</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
        }
        
        echo '</div>';
    } else {
        echo '<div class="section">';
        echo '<h2>Error</h2>';
        echo '<p class="error">Could not find DATABASE_URL in .env file</p>';
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<div class="section">';
    echo '<h2>Error</h2>';
    echo '<p class="error">Error reading .env file: ' . $e->getMessage() . '</p>';
    echo '</div>';
}

// Links for testing with different IDs
echo '<div class="section">';
echo '<h2>Test with Different Artist ID</h2>';
echo '<p>';
for ($i = 1; $i <= 10; $i++) {
    echo '<a href="?id=' . $i . '">Test with ID ' . $i . '</a> | ';
}
echo '</p>';
echo '</div>';

echo '</body></html>';
