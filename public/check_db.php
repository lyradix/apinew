<?php
// Test script to directly access the database and check the Artist table

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Load .env file
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/../.env');

// Create a basic connection to the database using PDO
try {
    $dbName = $_ENV['DATABASE_NAME'] ?? $_SERVER['DATABASE_NAME'] ?? 'sounddb';
    $dbUser = $_ENV['DATABASE_USER'] ?? $_SERVER['DATABASE_USER'] ?? 'root';
    $dbPass = $_ENV['DATABASE_PASSWORD'] ?? $_SERVER['DATABASE_PASSWORD'] ?? '';
    $dbHost = $_ENV['DATABASE_HOST'] ?? $_SERVER['DATABASE_HOST'] ?? '127.0.0.1';
    $dbPort = $_ENV['DATABASE_PORT'] ?? $_SERVER['DATABASE_PORT'] ?? '3306';
    
    // Hardcoded fallback for local development
    if (empty($dbPass)) {
        $dbUser = 'root';
        $dbPass = '';
        $dbHost = 'localhost';
        $dbName = 'sounddb';
    }
    
    error_log("Connecting to MySQL: host=$dbHost, dbname=$dbName, user=$dbUser");
    
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check table structure
    $stmt = $pdo->query("DESCRIBE artist");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Artist table structure:");
    foreach ($columns as $column) {
        error_log("Column: {$column['Field']}, Type: {$column['Type']}, Null: {$column['Null']}");
    }
    
    // Query all artists to check image values
    $stmt = $pdo->query("SELECT id, nom, image FROM artist");
    $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Artist records:");
    foreach ($artists as $artist) {
        error_log("Artist ID: {$artist['id']}, Name: {$artist['nom']}, Image: " . 
            (isset($artist['image']) ? $artist['image'] : 'NULL'));
    }
    
    echo "Database check completed. See error logs for details.";
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
