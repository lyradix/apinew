<?php
// Test file to verify file uploading functionality
error_log('TEST: File upload test script started');

// Create a dummy test file
$targetDirectory = __DIR__ . '/images';
$testFilename = 'test-' . uniqid() . '.txt';
$testFilePath = $targetDirectory . '/' . $testFilename;

error_log('TEST: Target directory: ' . $targetDirectory);
error_log('TEST: Directory exists: ' . (is_dir($targetDirectory) ? 'YES' : 'NO'));
error_log('TEST: Directory is writable: ' . (is_writable($targetDirectory) ? 'YES' : 'NO'));

try {
    // Try to create a test file
    if (file_put_contents($testFilePath, 'Test content')) {
        error_log('TEST: Successfully created test file: ' . $testFilePath);
        // Verify the file exists
        if (file_exists($testFilePath)) {
            error_log('TEST: File exists after creation: YES');
            // Clean up
            unlink($testFilePath);
            error_log('TEST: Test file deleted');
        } else {
            error_log('TEST: File does not exist after creation: NO');
        }
    } else {
        error_log('TEST: Failed to create test file');
    }
} catch (Exception $e) {
    error_log('TEST: Exception: ' . $e->getMessage());
}

echo "Test completed. Check error logs for results.";
