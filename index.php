<?php
$dataFile = 'data.json';

// Initialize proposals array
$proposals = [];

// Only try to load proposals if the data file exists
if (file_exists($dataFile)) {
    $proposals = json_decode(file_get_contents($dataFile), true) ?: [];
}

// Remove the HTML content processing code since it belongs in process.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thesis Proposals</title>
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

        h1 {
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        .new-proposal-btn {
            display: inline-block;
            background-color: #4a90e2;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 2rem;
            transition: background-color 0.3s;
        }

        .new-proposal-btn:hover {
            background-color: #357abd;
        }

        .proposals-list {
            list-style: none;
        }

        .proposal-item {
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .proposal-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .proposal-link {
            display: block;
            padding: 1rem;
            color: #333;
            text-decoration: none;
        }

        .proposal-title {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .proposal-topic {
            color: #666;
            font-size: 0.9rem;
        }

        .no-proposals {
            text-align: center;
            color: #666;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thesis Proposals</h1>
        <a href="submit.php" class="new-proposal-btn">Submit a new proposal</a>

        <?php if (!empty($proposals)): ?>
            <ul class="proposals-list">
                <?php foreach ($proposals as $proposal): ?>
                    <li class="proposal-item">
                        <a href="proposal.php?id=<?= htmlspecialchars($proposal['id']) ?>" class="proposal-link">
                            <div class="proposal-title"><?= htmlspecialchars($proposal['title']) ?></div>
                            <div class="proposal-topic"><?= htmlspecialchars($proposal['topic']) ?></div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="no-proposals">No proposals found. Be the first to submit one!</div>
        <?php endif; ?>
    </div>
</body>
</html>