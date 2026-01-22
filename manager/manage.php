<?php
include 'functions.php';

if (!is_dir('../theses/pages')) mkdir('../theses/pages', 0777, true);
if (!is_dir('../theses/raw')) mkdir('../theses/raw', 0777, true);

$files = glob("../theses/pages/[0-9]*.html");
natsort($files); 
$files = array_reverse($files); 

$bachelorProposals = [];
$masterProposals = [];

foreach($files as $file) {
    $name = basename($file);
    $id = basename($file, '.html');
    
    $title = "Untitled";
    $supervisor = "Unknown";
    $type = "bachelor";
    
    $jsonPath = "../theses/raw/$id.json";

    if (file_exists($jsonPath)) {
        $data = json_decode(file_get_contents($jsonPath), true);
        $title = htmlspecialchars($data['title'] ?? 'Untitled');
        $supervisor = htmlspecialchars($data['supervisor'] ?? 'Unknown');
        $type = strtolower($data['thesis_type'] ?? 'bachelor');
    } else {
        $content = file_get_contents($file);
        preg_match('/<title>(.*?)<\/title>/', $content, $tMatch);
        if (!empty($tMatch[1])) $title = $tMatch[1];
        preg_match('/<strong>Supervisor:<\/strong>\s*(.*?)<br>/i', $content, $sMatch);
        if (!empty($sMatch[1])) $supervisor = $sMatch[1];
        preg_match('/<strong>Type:<\/strong>\s*(.*?)\s*Thesis/i', $content, $tyMatch);
        if (!empty($tyMatch[1])) $type = strtolower(trim($tyMatch[1]));
    }

    $item = ['name' => $name, 'id' => $id, 'title' => $title, 'supervisor' => $supervisor, 'has_json' => file_exists($jsonPath)];

    if ($type === 'master') {
        $masterProposals[] = $item;
    } else {
        $bachelorProposals[] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Thesis Projects</title>
    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('files[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
        
        function confirmBulkDelete() {
            var checked = document.querySelectorAll('input[name="files[]"]:checked').length;
            if (checked === 0) {
                alert("Please select at least one proposal to delete.");
                return false;
            }
            return confirm("Are you sure you want to delete " + checked + " proposals? This cannot be undone.");
        }
    </script>
    <style>
        /* LIGHT THEME */
        body { font-family: sans-serif; padding: 2rem; background: #eee; color: #333; }
        
        .container { max-width: 950px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        h1 { color: #333; }
        h2 { border-bottom: 2px solid #eee; color: #4a90e2; padding-bottom: 10px; margin-top: 40px; }
        
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.9rem; margin-right: 5px; border:none; color: white; cursor: pointer; }
        .btn-green { background: #28a745; }
        .btn-purple { background: #6f42c1; }
        .btn-dark { background: #343a40; }
        .btn-cyan { background: #17a2b8; } /* New Export Button Color */
        .btn-red { background: #dc3545; }
        .btn-blue { background: #007bff; }
        .btn-orange { background: #f39c12; }
        
        hr { border: 0; border-top: 1px solid #ddd; margin: 20px 0; }
        
        ul { list-style: none; padding: 0; }
        li { border-bottom: 1px solid #ddd; padding: 15px 0; display: flex; align-items: center; }
        li:last-child { border-bottom: none; }
        
        /* Updated Alignment */
        .check-col { width: 30px; text-align: left; }
        .info-col { flex-grow: 1; display: flex; flex-direction: column; text-align: left; padding-left: 5px; }
        .actions-col { display: flex; align-items: center; white-space: nowrap; }
        
        .file-name { font-weight: bold; color: #999; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px; }
        .proposal-title { font-size: 1.1rem; font-weight: 600; color: #333; }
        .supervisor-name { font-size: 0.9rem; color: #666; font-style: italic; }
        
        .empty-msg { color: #999; font-style: italic; padding: 20px 0; }
        
        .bulk-controls { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ddd; display: flex; align-items: center; gap: 15px; }
        input[type="checkbox"] { transform: scale(1.3); cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="submit.php" class="btn btn-green">+ Add New Proposal</a>
            <a href="import.php" class="btn btn-purple">↑ Import from CSV</a>
            <a href="export.php" class="btn btn-cyan">⬇ Export to CSV</a>
            <a href="regenerate_all.php" class="btn btn-dark" onclick="return confirm('This will regenerate all HTML pages for every proposal. Continue?')">↻ Update All Pages</a>
            <a href="../theses/index.html" target="_blank" class="btn btn-blue">View Public Site</a>
        </div>
        <hr>

        <form action="delete_bulk.php" method="POST" onsubmit="return confirmBulkDelete()">
            
            <div class="bulk-controls">
                <label style="cursor: pointer; font-weight: bold;">
                    <input type="checkbox" onclick="toggleAll(this)"> Select All
                </label>
                <button type="submit" class="btn btn-red">Delete Selected</button>
            </div>

            <h2>Master Theses</h2>
            <ul>
                <?php if (empty($masterProposals)): ?>
                    <li class="empty-msg">No Master theses found.</li>
                <?php else: ?>
                    <?php foreach($masterProposals as $p): ?>
                    <li>
                        <div class="check-col">
                            <input type="checkbox" name="files[]" value="<?= $p['name'] ?>">
                        </div>
                        <div class="info-col">
                            <span class="file-name">File: <?= $p['name'] ?></span>
                            <span class="proposal-title"><?= $p['title'] ?></span>
                            <span class="supervisor-name">Sup: <?= $p['supervisor'] ?></span>
                        </div>
                        <div class="actions-col">
                            <a href="../theses/pages/<?= $p['name'] ?>" target="_blank" class="btn btn-blue">View</a>
                            <?php if($p['has_json']): ?>
                                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-orange">Modify</a>
                            <?php else: ?>
                                <span style="font-size:0.8rem; color:#666; margin-right:10px;">(Legacy)</span>
                            <?php endif; ?>
                            <a href="delete.php?file=<?= $p['name'] ?>" class="btn btn-red" onclick="return confirm('Delete this proposal?')">Delete</a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <h2>Bachelor Theses</h2>
            <ul>
                <?php if (empty($bachelorProposals)): ?>
                    <li class="empty-msg">No Bachelor theses found.</li>
                <?php else: ?>
                    <?php foreach($bachelorProposals as $p): ?>
                    <li>
                        <div class="check-col">
                            <input type="checkbox" name="files[]" value="<?= $p['name'] ?>">
                        </div>
                        <div class="info-col">
                            <span class="file-name">File: <?= $p['name'] ?></span>
                            <span class="proposal-title"><?= $p['title'] ?></span>
                            <span class="supervisor-name">Sup: <?= $p['supervisor'] ?></span>
                        </div>
                        <div class="actions-col">
                            <a href="../theses/pages/<?= $p['name'] ?>" target="_blank" class="btn btn-blue">View</a>
                            <?php if($p['has_json']): ?>
                                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-orange">Modify</a>
                            <?php else: ?>
                                <span style="font-size:0.8rem; color:#666; margin-right:10px;">(Legacy)</span>
                            <?php endif; ?>
                            <a href="delete.php?file=<?= $p['name'] ?>" class="btn btn-red" onclick="return confirm('Delete this proposal?')">Delete</a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            
        </form>
    </div>
</body>
</html>