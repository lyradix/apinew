<?php

// This is a direct test for file uploads with minimal dependencies
error_log("Direct upload test starting");

// Define the target directory for uploads
$targetDir = __DIR__ . '/images/';
error_log("Target directory: " . $targetDir);

// Check if directory exists and is writable
if (!is_dir($targetDir)) {
    error_log("Directory does not exist, creating...");
    if (!mkdir($targetDir, 0777, true)) {
        error_log("Failed to create directory: " . $targetDir);
        die("Failed to create upload directory");
    }
}

if (!is_writable($targetDir)) {
    error_log("Directory is not writable: " . $targetDir);
    die("Upload directory is not writable");
}

error_log("Directory exists and is writable");

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Detailed logging of upload parameters
    error_log("POST request received");
    error_log("Files array: " . print_r($_FILES, true));
    
    if (isset($_FILES["uploadFile"]) && $_FILES["uploadFile"]["error"] == 0) {
        error_log("File uploaded successfully");
        
        $fileName = basename($_FILES["uploadFile"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        error_log("Original filename: " . $fileName);
        error_log("Target path: " . $targetFilePath);
        error_log("File type: " . $fileType);
        error_log("File size: " . $_FILES["uploadFile"]["size"] . " bytes");
        error_log("Temp name: " . $_FILES["uploadFile"]["tmp_name"]);
        
        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
        if (in_array($fileType, $allowTypes)) {
            // Generate a unique filename
            $newFileName = uniqid() . '.' . $fileType;
            $newFilePath = $targetDir . $newFileName;
            
            error_log("New filename: " . $newFileName);
            error_log("New path: " . $newFilePath);
            
            try {
                // Upload file to server
                if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $newFilePath)) {
                    error_log("File moved successfully to: " . $newFilePath);
                    $uploadStatus = "<h3 class='success'>The file " . $newFileName . " has been uploaded successfully.</h3>";
                    $imagePath = "images/" . $newFileName;
                } else {
                    error_log("Failed to move uploaded file from " . $_FILES["uploadFile"]["tmp_name"] . " to " . $newFilePath);
                    $uploadStatus = "<h3 class='error'>Sorry, there was an error uploading your file.</h3>";
                    error_log("move_uploaded_file failed");
                }
            } catch (Exception $e) {
                error_log("Exception during file upload: " . $e->getMessage());
                $uploadStatus = "<h3 class='error'>Error: " . $e->getMessage() . "</h3>";
            }
        } else {
            error_log("Invalid file type: " . $fileType);
            $uploadStatus = "<h3 class='error'>Sorry, only JPG, JPEG, PNG, GIF, & WEBP files are allowed.</h3>";
        }
    } else {
        error_log("File upload error: " . $_FILES["uploadFile"]["error"]);
        $uploadStatus = "<h3 class='error'>Upload error: " . $_FILES["uploadFile"]["error"] . "</h3>";
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
            max-width: 600px;
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
        .btn {
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        img {
            max-width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Simple File Upload Test</h2>
    
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Select Image File:</label>
            <input type="file" name="uploadFile">
        </div>
        <div class="form-group">
            <input type="submit" class="btn" value="Upload">
        </div>
    </form>
    
    <?php if(isset($uploadStatus)): ?>
        <div>
            <?php echo $uploadStatus; ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($imagePath)): ?>
        <div>
            <h3>Uploaded Image:</h3>
            <img src="<?php echo $imagePath; ?>" alt="Uploaded Image">
        </div>
    <?php endif; ?>
    
    <h3>Server Information:</h3>
    <pre>
    upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?>
    
    post_max_size: <?php echo ini_get('post_max_size'); ?>
    
    max_file_uploads: <?php echo ini_get('max_file_uploads'); ?>
    
    upload_tmp_dir: <?php echo ini_get('upload_tmp_dir') ?: 'Default system temp directory'; ?>
    </pre>
</body>
</html>
