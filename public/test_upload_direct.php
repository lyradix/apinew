<?php
// Simple test script to verify file upload data in $_FILES and $_POST

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log that the script started
error_log("Direct upload test script starting");

// Set upload directory
$uploadDir = __DIR__ . '/uploads/';
error_log("Upload directory: " . $uploadDir);

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0777, true)) {
        error_log("Created upload directory");
    } else {
        error_log("Failed to create upload directory");
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    
    // Dump the raw post data for debugging
    error_log("Raw POST data: " . file_get_contents('php://input'));
    
    // Log the POST and FILES arrays
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Check if file upload exists
    if (isset($_FILES['uploadFile']) && $_FILES['uploadFile']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES['uploadFile']['name']);
        $uploadFilePath = $uploadDir . $fileName;
        
        error_log("File received: " . $fileName);
        error_log("Temp file: " . $_FILES['uploadFile']['tmp_name']);
        error_log("File size: " . $_FILES['uploadFile']['size'] . " bytes");
        error_log("File type: " . $_FILES['uploadFile']['type']);
        
        // Attempt to move the file
        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadFilePath)) {
            error_log("File successfully moved to: " . $uploadFilePath);
            $uploadSuccess = true;
        } else {
            error_log("Failed to move uploaded file from " . $_FILES['uploadFile']['tmp_name'] . " to " . $uploadFilePath);
            $uploadError = "Failed to move the uploaded file.";
        }
    } else if (isset($_FILES['uploadFile'])) {
        // If file was attempted but had an error
        $errorCodes = [
            UPLOAD_ERR_OK => "No error",
            UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize in php.ini",
            UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE in HTML form",
            UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
            UPLOAD_ERR_NO_FILE => "No file was uploaded",
            UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the upload"
        ];
        
        $errorCode = $_FILES['uploadFile']['error'];
        $errorMessage = isset($errorCodes[$errorCode]) ? $errorCodes[$errorCode] : "Unknown error ($errorCode)";
        error_log("File upload error: " . $errorMessage);
        $uploadError = "File upload error: " . $errorMessage;
    } else {
        error_log("No file upload found in the request");
        $uploadError = "No file was submitted. Make sure the form has enctype='multipart/form-data'";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
        .form-group { margin-bottom: 15px; }
        .debug-section { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px; }
    </style>
</head>
<body>
    <h1>File Upload Test</h1>
    
    <?php if (isset($uploadSuccess)): ?>
        <div class="success">
            <h2>Upload Successful!</h2>
            <p>File uploaded to: <?php echo htmlspecialchars($uploadFilePath); ?></p>
            <p><a href="uploads/<?php echo htmlspecialchars($fileName); ?>" target="_blank">View File</a></p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($uploadError)): ?>
        <div class="error">
            <h2>Upload Failed</h2>
            <p><?php echo htmlspecialchars($uploadError); ?></p>
        </div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="uploadFile">Select File to Upload:</label><br>
            <input type="file" name="uploadFile" id="uploadFile">
        </div>
        
        <div class="form-group">
            <label for="testField">Test Text Field:</label><br>
            <input type="text" name="testField" id="testField" value="Test Value">
        </div>
        
        <div class="form-group">
            <button type="submit">Upload File</button>
        </div>
    </form>
    
    <div class="debug-section">
        <h2>Debug Information</h2>
        
        <h3>Server Information</h3>
        <pre>
PHP Version: <?php echo PHP_VERSION; ?>

upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?>
post_max_size: <?php echo ini_get('post_max_size'); ?>
max_file_uploads: <?php echo ini_get('max_file_uploads'); ?>
memory_limit: <?php echo ini_get('memory_limit'); ?>

Upload directory: <?php echo $uploadDir; ?>
Directory exists: <?php echo is_dir($uploadDir) ? 'Yes' : 'No'; ?>
Directory writable: <?php echo is_writable($uploadDir) ? 'Yes' : 'No'; ?>
        </pre>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <h3>POST Data</h3>
            <pre><?php echo htmlspecialchars(print_r($_POST, true)); ?></pre>
            
            <h3>FILES Data</h3>
            <pre><?php echo htmlspecialchars(print_r($_FILES, true)); ?></pre>
            
            <?php if (function_exists('apache_request_headers')): ?>
                <h3>Request Headers</h3>
                <pre><?php echo htmlspecialchars(print_r(apache_request_headers(), true)); ?></pre>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
