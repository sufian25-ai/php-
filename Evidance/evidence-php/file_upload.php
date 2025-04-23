<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with Stylish Design</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .upload-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        .upload-container h3 {
            color: #007bff;
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-upload {
            background-color: #007bff;
            color: white;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            border: none;
        }
        .btn-upload:hover {
            background-color: #0056b3;
        }
        .alert {
            border-radius: 10px;
        }
        .alert-success {
            background-color: #0a7d00 !important;
            color: #ffff;
            font-weight: bold;
        }
        .alert-danger {
            background-color: #b30000 !important;
            color: #fff;
            font-weight: bold;
        }
        .file-input {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="upload-container">
    <h3 class="text-center mb-4">Upload Your File</h3>

    <!-- File upload form -->
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3 file-input">
            <label for="file" class="form-label">Choose a file (PDF, Image, Document):</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <button type="submit" class="btn-upload">Upload File</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Allowed file types and max file size (400 KB)
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $maxFileSize = 400 * 1024; // 400 KB

        $file = $_FILES['file'];
        
        // Check if file type is valid
        if (!in_array($file['type'], $allowedTypes)) {
            echo '<div class="alert alert-danger mt-3">❌ Invalid file type. Only PDF, Images, and Documents are allowed.</div>';
        } 
        // Check file size
        elseif ($file['size'] > $maxFileSize) {
            echo '<div class="alert alert-danger mt-3">❌ File size exceeds 400 KB. Please choose a smaller file.</div>';
        } 
        // If valid, move the uploaded file
        else {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $targetFile = $uploadDir . basename($file['name']);
            
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                echo '<div class="alert alert-success mt-3">✅ File uploaded successfully: ' . htmlspecialchars($file['name']) . '</div>';
            } else {
                echo '<div class="alert alert-danger mt-3">❌ Error uploading file. Please try again.</div>';
            }
        }
    }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
