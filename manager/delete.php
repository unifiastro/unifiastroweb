<?php
include 'functions.php';

$file = $_GET['file'] ?? '';
$id = basename($file, '.html');

$htmlPath = '../theses/pages/' . $file;
$jsonPath = '../theses/raw/' . $id . '.json';

if (file_exists($htmlPath)) {
    // Delete HTML
    unlink($htmlPath);
    
    // Delete RAW Data if it exists
    if (file_exists($jsonPath)) {
        unlink($jsonPath);
    }
    
    // Update index
    rebuildIndex();
}

header("Location: manage.php");
exit();
?>