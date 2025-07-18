<?php
// Simple script to test file uploads
error_log("Starting image upload test script");

// Path to target directory
$targetDir = __DIR__ . '/images';
error_log("Target directory: $targetDir");
error_log("Directory exists: " . (is_dir($targetDir) ? 'YES' : 'NO'));
error_log("Directory is writable: " . (is_writable($targetDir) ? 'YES' : 'NO'));

// Create a simple test image
$testFile = $targetDir . '/test-' . uniqid() . '.png';
$imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

try {
    // Try to write the file
    error_log("Trying to write test file: $testFile");
    $result = file_put_contents($testFile, $imageData);
    
    if ($result !== false) {
        error_log("File written successfully: $testFile");
        error_log("File size: " . filesize($testFile) . " bytes");
        error_log("File exists: " . (file_exists($testFile) ? 'YES' : 'NO'));
        
        // Test reading the file
        $fileContents = file_get_contents($testFile);
        error_log("File can be read: " . ($fileContents !== false ? 'YES' : 'NO'));
        
        // Cleanup
        unlink($testFile);
        error_log("Test file deleted");
    } else {
        error_log("Failed to write file: " . error_get_last()['message']);
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
}

echo "Test completed. Check error log for results.";
