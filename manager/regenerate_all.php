<?php
include 'functions.php';

$baseDir = '../theses/';
$pagesDir = $baseDir . 'pages/';
$rawDir = $baseDir . 'raw/';

$jsonFiles = glob($rawDir . "*.json");
$count = 0;

$topicMap = [
    "Planets, Exoplanets and Astro" => "Solar System, Astrobiology and Exoplanets",
    "Astroparticles and high-energy Astrophysics" => "Astroparticles and High-energy Astrophysics",
    "Star formation and Interstellar Medium" => "Star Formation and Interstellar Medium",
    "Plasma Astrophysics" => "Plasma and Solar Physics" 
];

foreach ($jsonFiles as $file) {
    $data = json_decode(file_get_contents($file), true);
    $id = basename($file, '.json');
    $filename = $id . ".html";
    $filePath = $pagesDir . $filename;

    $currentTopic = $data['topic'] ?? 'General';
    if (array_key_exists($currentTopic, $topicMap)) {
        $data['topic'] = $topicMap[$currentTopic];
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    $title = htmlspecialchars($data['title'] ?? 'Untitled');
    $topic = htmlspecialchars($data['topic'] ?? 'General');
    $type = ucfirst(htmlspecialchars($data['thesis_type'] ?? 'Bachelor'));
    $duration = htmlspecialchars($data['duration'] ?? '');
    $supervisor = htmlspecialchars($data['supervisor'] ?? '');
    $collaborators = htmlspecialchars($data['collaborators'] ?? '');
    $contacts = htmlspecialchars($data['contacts'] ?? '');
    $image = $data['image'] ?? '';
    
    $desc = $data['description'] ?? '';
    if (strpos($desc, '<p>') === false && strpos($desc, '<br') === false && strpos($desc, '<div>') === false) {
        $desc = nl2br($desc);
    }

    $references = htmlspecialchars($data['references'] ?? '');
    $requirements = htmlspecialchars($data['requirements'] ?? '');

    $collabHtml = $collaborators ? "<strong>Collaborators:</strong> $collaborators<br>" : "";
    $refHtml = $references ? "<div class='section'><h2>References</h2><div>" . nl2br($references) . "</div></div>" : "";
    $reqHtml = $requirements ? "<div class='section'><h2>Requirements</h2><div>" . nl2br($requirements) . "</div></div>" : "";

    $imagePathForHtml = "";
    if ($image) {
        // ADDED text-align: center
        $imagePathForHtml = "<div class='section proposal-image' style='text-align: center;'><img src='../uploads/$image' style='max-width:100%; border-radius:8px; margin-top:20px;'></div>";
    }

    $htmlContent = <<<HTML
<!DOCTYPE html>
<html lang='en'>
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SQESPYFYL7"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-SQESPYFYL7');
    </script>
    
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
            <strong>Duration (months):</strong> $duration months<br>
            <strong>Supervisor(s):</strong> $supervisor<br>
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
    $count++;
}

rebuildIndex();

echo "<script>alert('Successfully regenerated and migrated $count pages!'); window.location.href='manage.php';</script>";
?>