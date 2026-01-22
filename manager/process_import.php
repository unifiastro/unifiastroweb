<?php
include 'functions.php';

$baseDir = '../theses/';
$pagesDir = $baseDir . 'pages/';
$rawDir = $baseDir . 'raw/';

if (!is_dir($pagesDir)) mkdir($pagesDir, 0777, true);
if (!is_dir($rawDir)) mkdir($rawDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    
    $file = $_FILES['csv_file']['tmp_name'];
    
    if (($handle = fopen($file, "r")) !== FALSE) {
        
        fgetcsv($handle, 0, ",", "\"", "\\");

        $existingFiles = glob($pagesDir . "[0-9]*.html");
        $maxId = 0;
        foreach ($existingFiles as $f) {
            $fid = intval(basename($f, '.html'));
            if ($fid > $maxId) $maxId = $fid;
        }
        $nextId = $maxId + 1;

        $count = 0;

        while (($row = fgetcsv($handle, 0, ",", "\"", "\\")) !== FALSE) {
            
            if ($row === null || count($row) < 3) continue;

            $rawType = strtolower($row[0] ?? '');
            $topic = $row[1] ?? 'General'; 
            $title = $row[2] ?? 'Untitled';
            $supervisor = $row[3] ?? '';
            $contacts = $row[4] ?? '';
            $desc = nl2br(htmlspecialchars($row[5] ?? '')); 
            $duration = htmlspecialchars($row[6] ?? '');
            $references = htmlspecialchars($row[7] ?? '');
            $requirements = htmlspecialchars($row[9] ?? '');
            
            $collaborators = htmlspecialchars($row[10] ?? ''); 
            $image = htmlspecialchars($row[11] ?? '');

            $type = preg_match('/master|magistrale/i', $rawType) ? 'Master' : 'Bachelor';
            
            $topicMap = [
                "Plasma Astrophysics" => "Plasma and Solar Physics",
                "Planets, Exoplanets and Astro" => "Solar System, Astrobiology and Exoplanets",
                "Star formation and Interstellar Medium" => "Star Formation and Interstellar Medium",
                "Astroparticles and high-energy Astrophysics" => "Astroparticles and High-energy Astrophysics"
            ];
            
            if (array_key_exists($topic, $topicMap)) {
                $topic = $topicMap[$topic];
            }

            // Generate HTML
            $filename = $nextId . ".html";
            $filePath = $pagesDir . $filename;
            
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
            background: #ffffff; padding: 2rem; border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        }
        .back-link { display: inline-block; margin-bottom: 1rem; color: #4a90e2; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        h1 { margin-bottom: 0.5rem; color: #333; }
        .meta { 
            color: #666; margin-bottom: 2rem; line-height: 1.8; 
            background: #f9f9f9; padding: 15px; border-radius: 5px; 
        }
        .section { margin-top: 2rem; }
        h2 { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; color: #333; }
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
            <strong>Duration:</strong> $duration<br>
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

            $rawData = [
                'title' => $title,
                'topic' => $topic,
                'thesis_type' => strtolower($type),
                'duration' => $duration,
                'supervisor' => $supervisor,
                'collaborators' => $collaborators,
                'references' => $references,
                'requirements' => $requirements,
                'description' => $row[5] ?? '', 
                'contacts' => $contacts,
                'image' => $image 
            ];
            
            file_put_contents($rawDir . $nextId . '.json', json_encode($rawData, JSON_PRETTY_PRINT));

            $nextId++;
            $count++;
        }
        fclose($handle);
        
        rebuildIndex();
        
        echo "<script>alert('Successfully imported $count proposals!'); window.location.href='manage.php';</script>";
    } else {
        echo "Error opening file.";
    }
}
?>