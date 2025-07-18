<?php
// This file checks if the fileinfo extension is properly loaded

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Loaded extensions:\n";

$extensions = get_loaded_extensions();
sort($extensions);
echo implode(", ", $extensions) . "\n\n";

if (extension_loaded('fileinfo')) {
    echo "fileinfo extension is LOADED\n";
    
    // Test the fileinfo functions
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $testFile = __DIR__ . '/images/test.jpg';
    
    // Create a test file if it doesn't exist
    if (!file_exists($testFile)) {
        $data = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBxdWFsaXR5ID0gOTAK/9sAQwADAgIDAgIDAwMDBAMDBAUIBQUEBAUKBwcGCAwKDAwLCgsLDQ4SEA0OEQ4LCxAWEBETFBUVFQwPFxgWFBgSFBUU/9sAQwEDBAQFBAUJBQUJFA0LDRQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU/8AAEQgAAQABAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A+t6AP//Z');
        file_put_contents($testFile, $data);
        echo "Created test file: $testFile\n";
    }
    
    if (file_exists($testFile)) {
        $mime = $finfo->file($testFile);
        echo "MIME type of test file: $mime\n";
    } else {
        echo "Could not find test file: $testFile\n";
    }
} else {
    echo "fileinfo extension is NOT LOADED\n";
    echo "Checking extension directory settings:\n";
    echo "extension_dir = " . ini_get('extension_dir') . "\n";
}

echo "\nPHP ini settings:\n";
echo "Loaded php.ini path: " . php_ini_loaded_file() . "\n";
echo "Additional .ini files loaded from: " . php_ini_scanned_files() . "\n";
