<?php
include 'functions.php';

$baseDir = '../theses/';
$pagesDir = $baseDir . 'pages/';
$uploadDir = $baseDir . 'uploads/';
$rawDir = $baseDir . 'raw/';

if (!is_dir($baseDir)) mkdir($baseDir, 0777, true);
if (!is_dir($pagesDir)) mkdir($pagesDir, 0777, true);
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($rawDir)) mkdir($rawDir, 0777, true);

$id = $_POST['id'] ?? null;
$currentImage = '';

if ($id) {
    $filename = $id . ".html";
    if (file_exists($rawDir . $id . '.json')) {
        $oldData = json_decode(file_get_contents($rawDir . $id . '.json'), true);
        $currentImage = $oldData['image'] ?? '';
    }
} else {
    $existingFiles = glob($pagesDir . "[0-9]*.html");
    $maxId = 0;
    foreach ($existingFiles as $f) {
        $fid = intval(basename($f, '.html'));
        if ($fid > $maxId) $maxId = $fid;
    }
    $id = $maxId + 1;
    $filename = $id . ".html";
}

$filePath = $pagesDir . $filename;

$imagePathForHtml = "";
$savedImageFilename = $currentImage;

if (!empty($_FILES['image']['name'])) {
    $imgName = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imgName);
    $savedImageFilename = $imgName;
}

if ($savedImageFilename) {
    // ADDED text-align: center
    $imagePathForHtml = "<div class='section proposal-image' style='text-align: center;'><img src='../uploads/$savedImageFilename' style='max-width:100%; border-radius:8px; margin-top:20px;'></div>";
}

$title = htmlspecialchars($_POST['title']);
$topic = htmlspecialchars($_POST['topic']);
$desc = $_POST['description']; 
$contacts = htmlspecialchars($_POST['contacts']);
$type = ucfirst(htmlspecialchars($_POST['thesis_type']));
$supervisor = htmlspecialchars($_POST['supervisor']);
$duration = htmlspecialchars($_POST['duration']);
$collaborators = isset($_POST['collaborators']) ? htmlspecialchars($_POST['collaborators']) : '';
$references = isset($_POST['references']) ? htmlspecialchars($_POST['references']) : '';
$requirements = isset($_POST['requirements']) ? htmlspecialchars($_POST['requirements']) : '';

$collabHtml = $collaborators ? "<strong>Collaborators:</strong> $collaborators<br>" : "";
$refHtml = $references ? "<div class='section'><h2>References</h2><div>" . nl2br($references) . "</div></div>" : "";
$reqHtml = $requirements ? "<div class='section'><h2>Requirements</h2><div>" . nl2br($requirements) . "</div></div>" : "";

$htmlContent = <<<HTML
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>$title</title>
    <style>
        /* LIGHT THEME */
        body { 
            background-color: #f5f5f5; 
            background-image: url('../images/background.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            padding: 2rem; 
            font-family: 'Segoe UI', sans-serif; 
            color: #333; 
        }
        
        .container { 
            max-width: 800px; margin: 0 auto; 
            background: #ffffff; 
            padding: 2rem; border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        }
        
        .back-link { display: inline-block; margin-bottom: 1rem; color: #4a90e2; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        
        h1 { margin-bottom: 0.5rem; color: #333; }
        
        .meta { 
            color: #666; margin-bottom: 2rem; line-height: 1.8; 
            background: #f9f9f9; 
            padding: 15px; border-radius: 5px; 
        }
        
        .section { margin-top: 2rem; }
        
        h2 { 
            border-bottom: 1px solid #eee; 
            padding-bottom: 10px; margin-bottom: 15px; 
            color: #333; 
        }
        
        .justified-text { text-align: justify; }
        
        .proposal-topic { display:none; } 
    </style>
</head>
<body>
    <div class='container'>
        <a href='../index.html' class='back-link'>&larr; Back to List</a>
        
        <h1>$title</h1>
        <div class='proposal-topic'>$topic</div> 
        
        <div class='meta'>
            <strong>Topic:</strong> $topic<br>
            <strong>Type:</strong> $type Thesis<br>
            <strong>Duration:</strong> $duration months<br>
            <strong>Supervisor:</strong> $supervisor<br>
            $collabHtml
        </div>

        <div class='section'>
            <h2>Contact Information</h2>
            <div>$contacts</div>
        </div>

        <div class='section'>
            <h2>Description</h2>
            <div class="justified-text">$desc</div>
        </div>

        $refHtml

        $reqHtml

        $imagePathForHtml

    </div>
</body>
</html>
HTML;

file_put_contents($filePath, $htmlContent);

$rawData = $_POST;
$rawData['image'] = $savedImageFilename;
$rawData['requirements'] = $requirements; 
$rawData['references'] = $references; 
file_put_contents($rawDir . $id . '.json', json_encode($rawData, JSON_PRETTY_PRINT));

rebuildIndex();

header("Location: manage.php");
exit();
?>