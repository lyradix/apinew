<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Simple upload form submitted');
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = __DIR__ . '/images/';
        $fileName = 'direct-upload-' . uniqid() . '-' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        
        error_log('Uploading to: ' . $targetFile);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            error_log('File uploaded successfully: ' . $fileName);
            echo "<p style='color:green'>File uploaded successfully as: $fileName</p>";
            
            // Now update artist ID 2 with this image
            require_once __DIR__ . '/../vendor/autoload.php';
            $_SERVER['APP_ENV'] = 'dev';
            $kernel = new \App\Kernel($_SERVER['APP_ENV'], true);
            $kernel->boot();
            $container = $kernel->getContainer();
            $entityManager = $container->get('doctrine.orm.entity_manager');
            $artistRepository = $container->get('doctrine')->getRepository(\App\Entity\Artist::class);
            $artist = $artistRepository->find(2);
            
            if ($artist) {
                $artist->setImage($fileName);
                $entityManager->persist($artist);
                $entityManager->flush();
                error_log('Artist ID 2 updated with image: ' . $fileName);
                echo "<p style='color:green'>Artist ID 2 updated with image: $fileName</p>";
            } else {
                error_log('Artist ID 2 not found');
                echo "<p style='color:red'>Artist ID 2 not found</p>";
            }
        } else {
            error_log('Failed to move uploaded file');
            echo "<p style='color:red'>Failed to move uploaded file</p>";
        }
    } else {
        $error = $_FILES['image']['error'] ?? 'No file uploaded';
        error_log('Upload error: ' . $error);
        echo "<p style='color:red'>Upload error: $error</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct Upload Test</title>
</head>
<body>
    <h1>Direct Upload Test</h1>
    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="image">Select image:</label>
            <input type="file" name="image" id="image">
        </div>
        <div style="margin-top: 10px;">
            <button type="submit">Upload</button>
        </div>
    </form>
</body>
</html>
