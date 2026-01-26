<?php
function rebuildIndex() {
    $baseDir = '../theses/';
    $pagesDir = $baseDir . 'pages/';
    
    $files = glob($pagesDir . "*.html");
    
    // CATEGORIES
    $categories = [
        "Instrumentation",
        "Galaxies and AGNs",
        "Star Formation and Interstellar Medium",
        "Solar System, Astrobiology and Exoplanets",
        "Stars and Stellar Evolution",
        "Cosmology",
        "Plasma and Solar Physics",
        "Astroparticles and High-energy Astrophysics",
        "General"
    ];

    $bachelorProposals = [];
    $masterProposals = [];

    foreach ($categories as $cat) {
        $bachelorProposals[$cat] = [];
        $masterProposals[$cat] = [];
    }

    foreach ($files as $file) {
        $basename = basename($file);
        $content = file_get_contents($file);
        
        preg_match('/<title>(.*?)<\/title>/', $content, $titleMatch);
        $title = $titleMatch[1] ?? 'Untitled';

        preg_match('/<strong>Supervisor(?:\(s\))?:<\/strong>\s*(.*?)<br>/i', $content, $supervisorMatch);
        $supervisor = $supervisorMatch[1] ?? 'Unknown';

        preg_match('/<strong>Type:<\/strong>\s*(.*?)\s*Thesis/i', $content, $typeMatch);
        $type = strtolower(trim($typeMatch[1] ?? ''));

        preg_match('/<div class=\'proposal-topic\'>(.*?)<\/div>/', $content, $topicMatch);
        $topic = trim($topicMatch[1] ?? 'General');

        if (!in_array($topic, $categories)) {
            $topic = "General";
        }

        $id = intval(basename($file, '.html'));
        
        $data = [
            'id' => $id,
            'filename' => $basename,
            'title' => $title,
            'supervisor' => $supervisor
        ];

        if ($type === 'master') {
            $masterProposals[$topic][] = $data;
        } else {
            $bachelorProposals[$topic][] = $data;
        }
    }

    $renderCategory = function($catName, $proposals) {
        if (empty($proposals)) return "";
        
        // --- RANDOMIZE ORDER ---
        shuffle($proposals);
        // -----------------------

        $count = count($proposals);

        $html = "
            <button class='accordion-header'>
                <span class='cat-name'>$catName</span>
                <div class='header-right'>
                    <span class='count'>($count)</span>
                    <span class='icon'></span>
                </div>
            </button>
            <div class='accordion-content'>
                <ul class='proposals-list'>";
        
        foreach ($proposals as $p) {
            $linkPath = 'pages/' . $p['filename'];
            $html .= "
                <li class='proposal-item'>
                    <a href='{$linkPath}' class='proposal-link'>
                        <div class='proposal-title'>{$p['title']}</div>
                        <div class='proposal-supervisor'>Supervisor: {$p['supervisor']}</div>
                    </a>
                </li>";
        }
        $html .= "</ul></div>";
        return $html;
    };

    // UPDATED HTML STRUCTURE
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SQESPYFYL7"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-SQESPYFYL7');
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astro Thesis Projects</title>
    <style>
        /* --- GLOBAL BACKGROUND --- */
        body { 
            background-color: #f5f5f5; 
            background-image: url('images/background.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            padding: 2rem; 
            font-family: "Segoe UI", sans-serif; 
            color: #333; 
        }
        
        .container { 
            max-width: 800px; margin: 0 auto; 
            background: #ffffff; 
            padding: 2rem; border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        }
        
        /* --- HEADER LAYOUT --- */
        .header-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 3rem; 
            flex-wrap: wrap; 
        }
        
        h1 { 
            color: #333; 
            margin: 0; 
            text-align: center; 
            font-size: 2rem;
            flex-grow: 1; 
        }
        
        .header-logo {
            height: 80px; 
            width: auto;
            object-fit: contain;
        }

        .intro-text {
            text-align: justify; 
            color: #555; 
            margin-bottom: 2.5rem;
            font-size: 1.05rem; 
            line-height: 1.6;
        }
        
        h2 { 
            color: #4a90e2; border-bottom: 2px solid #eee; 
            padding-bottom: 0.5rem; margin-top: 2rem; margin-bottom: 1rem; 
        }

        /* --- ACCORDION STYLES --- */
        .accordion-header {
            background-color: #f8f9fa;
            color: #2c3e50;
            cursor: pointer;
            padding: 15px;
            width: 100%;
            border: none;
            border-left: 4px solid #f39c12;
            text-align: left;
            outline: none;
            font-size: 1.1rem;
            transition: 0.3s;
            margin-top: 10px;
            display: flex;
            justify-content: space-between; 
            align-items: center;
            font-weight: 600;
            border-radius: 4px;
        }

        .accordion-header:hover {
            background-color: #edf2f7;
        }

        .accordion-header.active {
            background-color: #e2e8f0;
            border-left-color: #e67e22;
        }

        .cat-name { flex-grow: 1; } 

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px; 
        }
        
        .count { 
            font-size: 0.95rem; 
            font-weight: normal; 
            color: #666; 
        }

        .icon:after {
            content: '+'; 
            font-size: 1.5rem;
            color: #777;
            line-height: 1rem;
            font-weight: bold;
        }

        .accordion-header.active .icon:after {
            content: '-';
        }

        .accordion-content {
            padding: 0 15px;
            background-color: white;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            border-left: 1px solid #eee; 
        }

        /* LIST STYLES */
        .proposals-list { list-style: none; padding: 0; margin-top: 15px; margin-bottom: 15px; }
        
        .proposal-item { 
            border: 1px solid #eee; 
            background: #fff;
            border-radius: 5px; margin-bottom: 0.75rem; 
            transition: transform 0.2s, box-shadow 0.2s; 
        }
        
        .proposal-item:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
        }
        
        .proposal-link { display: block; padding: 1rem; color: #333; text-decoration: none; }
        
        .proposal-title { font-weight: 600; font-size: 1.1rem; color: #2c3e50; margin-bottom: 0.1rem; }
        .proposal-supervisor { color: #666; font-size: 0.95rem; margin-bottom: 0.25rem; font-style: italic; }
        
        .empty-msg { color: #999; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header-row">
            <img src="images/logo_left.png" alt="UniFi" class="header-logo">
            <h1>Astro Thesis Projects</h1>
            <img src="images/logo_right.png" alt="INAF" class="header-logo">
        </div>
        
        <div class="intro-text">
            This page lists potential thesis projects for Bachelor’s and Master’s degrees in Physics and Astrophysics. 
            Projects may be supervised by researchers at the <strong>University of Florence</strong> and at <strong>INAF – Osservatorio Astronomico di Arcetri</strong>.
            <br><br>
            Students are encouraged to contact researchers at both UNIFI and INAF for further information on available thesis projects.
        </div>
        
        <h2>Master Theses</h2>
HTML;

    $hasMaster = false;
    foreach ($categories as $cat) {
        if (!empty($masterProposals[$cat])) {
            $html .= $renderCategory($cat, $masterProposals[$cat]);
            $hasMaster = true;
        }
    }
    if (!$hasMaster) $html .= '<div class="empty-msg">No Master theses currently available.</div>';

    $html .= <<<HTML
        <h2>Bachelor Theses</h2>
HTML;

    $hasBachelor = false;
    foreach ($categories as $cat) {
        if (!empty($bachelorProposals[$cat])) {
            $html .= $renderCategory($cat, $bachelorProposals[$cat]);
            $hasBachelor = true;
        }
    }
    if (!$hasBachelor) $html .= '<div class="empty-msg">No Bachelor theses currently available.</div>';

    $html .= <<<HTML
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var acc = document.getElementsByClassName("accordion-header");
            for (var i = 0; i < acc.length; i++) {
                acc[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var panel = this.nextElementSibling;
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + "px";
                    } 
                });
            }
        });
    </script>
</body>
</html>
HTML;

    file_put_contents($baseDir . 'index.html', $html);
}
?>