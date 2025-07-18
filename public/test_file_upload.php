<?php
// This is a standalone file upload test that doesn't rely on Symfony
error_log("Starting file upload test");

// Set error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the upload directory
$uploadDir = __DIR__ . '/images/';
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Check if the directory exists and is writable
if (!is_dir($uploadDir)) {
    error_log("Directory doesn't exist: " . $uploadDir);
    mkdir($uploadDir, 0777, true);
    error_log("Created directory: " . $uploadDir);
}

if (!is_writable($uploadDir)) {
    die("Error: Upload directory is not writable");
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        $errorMessage = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Unknown upload error';
        error_log("Upload error: " . $errorMessage);
        die("Error: " . $errorMessage);
    }
    
    // Check file size
    if ($file['size'] > $maxFileSize) {
        error_log("File too large: " . $file['size'] . " bytes");
        die("Error: File is too large");
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        error_log("Invalid file type: " . $file['type']);
        die("Error: Invalid file type. Only JPEG, PNG, and WEBP are allowed.");
    }
    
    // Generate a unique filename
    $filename = uniqid('upload-') . '-' . basename($file['name']);
    $filePath = $uploadDir . $filename;
    
    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        error_log("File uploaded successfully: " . $filePath);
        $message = "File uploaded successfully";
    } else {
        error_log("Failed to move uploaded file from " . $file['tmp_name'] . " to " . $filePath);
        $message = "Failed to upload file";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>File Upload Test</h1>
    
    <?php if (isset($message)): ?>
        <div class="<?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <?php if (isset($filename)): ?>
            <div>
                <p>Uploaded file: <?php echo $filename; ?></p>
                <img src="images/<?php echo $filename; ?>" style="max-width: 300px; height: auto;">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="file">Select image to upload:</label>
            <input type="file" name="file" id="file" required>
        </div>
        <button type="submit">Upload</button>
    </form>
    
    <hr>
    
    <h2>Server Information</h2>
    <p>Upload directory: <?php echo $uploadDir; ?></p>
    <p>Directory exists: <?php echo is_dir($uploadDir) ? 'Yes' : 'No'; ?></p>
    <p>Directory writable: <?php echo is_writable($uploadDir) ? 'Yes' : 'No'; ?></p>
    <p>Max upload size: <?php echo ini_get('upload_max_filesize'); ?></p>
    <p>PHP version: <?php echo phpversion(); ?></p>
</body>
</html>
