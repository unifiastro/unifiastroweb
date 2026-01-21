<?php
$dataFile = 'data.json';
$proposals = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

$id = $_GET['id'] ?? null;
$proposal = null;

foreach ($proposals as $p) {
    if ($p['id'] == $id) {
        $proposal = $p;
        break;
    }
}

if (!$proposal) {
    die("Proposal not found.");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Details</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #4a90e2;
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .back-link:hover {
            color: #357abd;
        }

        .back-link::before {
            content: '←';
            margin-right: 0.5rem;
        }

        h1 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .metadata {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .metadata-item {
            margin-bottom: 0.5rem;
        }

        .proposal-image-container {
            margin: 1.5rem 0;
            text-align: center;
        }

        .proposal-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .section-title {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .section-content {
            color: #555;
            line-height: 1.6;
        }

        .loading {
            text-align: center;
            color: #666;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">Back to List</a>
        
        <?php if ($proposal): ?>
            <h1><?= htmlspecialchars($proposal['title']) ?></h1>
            
            <div class="metadata">
                <div class="metadata-item">Topic: <?= htmlspecialchars($proposal['topic']) ?></div>
                <div class="metadata-item">Type: <?= ucfirst(htmlspecialchars($proposal['thesis_type'])) ?> Thesis</div>
                <div class="metadata-item">Duration: <?= htmlspecialchars($proposal['duration']) ?> months</div>
                <div class="metadata-item">Supervisor: <?= htmlspecialchars($proposal['supervisor']) ?></div>
                <?php if (!empty($proposal['collaborators'])): ?>
                    <div class="metadata-item">Other Collaborators: <?= htmlspecialchars($proposal['collaborators']) ?></div>
                <?php endif; ?>
            </div>

            <?php if (!empty($proposal['image']) && file_exists($proposal['image'])): ?>
                <div class="proposal-image-container">
                    <img src="<?= htmlspecialchars($proposal['image']) ?>" alt="Proposal Image" class="proposal-image">
                </div>
            <?php endif; ?>

            <div class="section">
                <h2 class="section-title">Description</h2>
                <div class="section-content">
                    <?= $proposal['description'] ?>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Contact Information</h2>
                <div class="section-content">
                    <?= htmlspecialchars($proposal['contacts']) ?>
                </div>
            </div>
            
        <?php else: ?>
            <div class="loading">Proposal not found.</div>
        <?php endif; ?>
    </div>
</body>
</html>