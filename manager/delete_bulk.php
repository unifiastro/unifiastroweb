<?php
include 'functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['files'])) {
    
    $filesToDelete = $_POST['files'];
    
    foreach ($filesToDelete as $file) {
        // Security check: ensure only filename, no paths
        $file = basename($file);
        
        $htmlPath = '../theses/pages/' . $file;
        $id = basename($file, '.html');
        $jsonPath = '../theses/raw/' . $id . '.json';
        
        // Delete HTML file
        if (file_exists($htmlPath)) {
            unlink($htmlPath);
        }
        
        // Delete JSON file
        if (file_exists($jsonPath)) {
            unlink($jsonPath);
        }
    }
    
    // Rebuild the public index
    rebuildIndex();
}

// Redirect back to dashboard
header("Location: manage.php");
exit();
?>