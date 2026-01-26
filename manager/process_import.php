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

            // DECODE ENTITIES
            $rawType = htmlspecialchars_decode($row[0] ?? '', ENT_QUOTES);
            $topic = htmlspecialchars_decode($row[1] ?? 'General', ENT_QUOTES);
            $title = htmlspecialchars_decode($row[2] ?? 'Untitled', ENT_QUOTES);
            $supervisor = htmlspecialchars_decode($row[3] ?? '', ENT_QUOTES);
            $contacts = htmlspecialchars_decode($row[4] ?? '', ENT_QUOTES);
            $descRaw = htmlspecialchars_decode($row[5] ?? '', ENT_QUOTES);
            $duration = htmlspecialchars_decode($row[6] ?? '', ENT_QUOTES);
            $refRaw = htmlspecialchars_decode($row[7] ?? '', ENT_QUOTES);
            $reqRaw = htmlspecialchars_decode($row[9] ?? '', ENT_QUOTES);
            $collaborators = htmlspecialchars_decode($row[10] ?? '', ENT_QUOTES);
            $image = htmlspecialchars_decode($row[11] ?? '', ENT_QUOTES);

            $type = (stripos($rawType, 'master') !== false || stripos($rawType, 'magistrale') !== false) ? 'Master' : 'Bachelor';
            
            $topicMap = [
                "Plasma Astrophysics" => "Plasma and Solar Physics",
                "Planets, Exoplanets and Astro" => "Solar System, Astrobiology and Exoplanets",
                "Star formation and Interstellar Medium" => "Star Formation and Interstellar Medium",
                "Astroparticles and high-energy Astrophysics" => "Astroparticles and High-energy Astrophysics"
            ];
            
            if (array_key_exists($topic, $topicMap)) {
                $topic = $topicMap[$topic];
            }

            // HTML GENERATION HELPERS
            $formatRichText = function($text) {
                if (empty($text)) return '';
                if ($text != strip_tags($text)) {
                    return $text; // It's HTML, return as is
                }
                // It's plain text, convert newlines to breaks
                return nl2br(htmlspecialchars($text));
            };

            $htmlTitle = htmlspecialchars($title);
            $htmlTopic = htmlspecialchars($topic);
            $htmlSupervisor = htmlspecialchars($supervisor);
            $htmlDuration = htmlspecialchars($duration);
            $htmlContacts = htmlspecialchars($contacts); 
            $htmlCollaborators = $collaborators ? "<strong>Collaborators:</strong> " . htmlspecialchars($collaborators) . "<br>" : "";

            $htmlDesc = $formatRichText($descRaw);
            $htmlRef = $refRaw ? "<div class='section'><h2>References</h2><div>" . $formatRichText($refRaw) . "</div></div>" : "";
            $htmlReq = $reqRaw ? "<div class='section'><h2>Requirements</h2><div>" . $formatRichText($reqRaw) . "</div></div>" : "";

            $imagePathForHtml = "";
            if ($image) {
                $image = basename($image); 
                $imagePathForHtml = "<div class='section proposal-image' style='text-align: center;'><img src='../uploads/$image' style='max-width:100%; border-radius:8px; margin-top:20px;'></div>";
            }

            $filename = $nextId . ".html";
            $filePath = $pagesDir . $filename;

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
    <title>$htmlTitle</title>
    <style>
        body { background-color: #f5f5f5; background-image: url('../images/background.jpg'); background-size: cover; background-attachment: fixed; padding: 2rem; font-family: 'Segoe UI', sans-serif; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: #ffffff; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .back-link { display: inline-block; margin-bottom: 1rem; color: #4a90e2; text-decoration: none; }
        .meta { color: #666; margin-bottom: 2rem; line-height: 1.8; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .section { margin-top: 2rem; }
        h2 { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; color: #333; }
        
        /* RESTORED SPACING */
        .justified-text { text-align: justify; } 
        .justified-text p { margin-top: 0; margin-bottom: 10px; }
        
        .proposal-topic { display:none; } 
    </style>
</head>
<body>
    <div class='container'>
        <a href='../index.html' class='back-link'>&larr; Back to List</a>
        <h1>$htmlTitle</h1>
        <div class='proposal-topic'>$htmlTopic</div> 
        
        <div class='meta'>
            <strong>Topic:</strong> $htmlTopic<br>
            <strong>Type:</strong> $type Thesis<br>
            <strong>Duration (months):</strong> $htmlDuration<br>
            <strong>Supervisor(s):</strong> $htmlSupervisor<br>
            $htmlCollaborators
        </div>

        <div class='section'>
            <h2>Contact Information</h2>
            <div>$htmlContacts</div>
        </div>

        <div class='section'>
            <h2>Description</h2>
            <div class="justified-text">$htmlDesc</div>
        </div>

        $htmlRef
        $htmlReq
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
                'references' => $refRaw,
                'requirements' => $reqRaw,
                'description' => $descRaw,
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