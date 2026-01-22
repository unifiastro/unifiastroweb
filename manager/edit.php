<?php
$id = $_GET['id'] ?? null;
if (!$id) die("No ID specified");

$jsonFile = "../theses/raw/$id.json";
if (!file_exists($jsonFile)) die("Data file not found. Cannot edit this proposal.");

$data = json_decode(file_get_contents($jsonFile), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Proposal</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#description',
                plugins: 'lists link image table code help wordcount autolink emoticons',
                toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor | link image | removeformat',
                height: 400,
                menubar: false,
                statusbar: true,
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; color: #333; text-align: justify; }',
                setup: function(editor) { editor.on('change', function() { editor.save(); }); }
            });
        });
    </script>
    <style>
        /* LIGHT THEME */
        body { font-family: sans-serif; padding: 2rem; background: #f5f5f5; color: #333; }
        
        .container { 
            max-width: 800px; margin: 0 auto; 
            background: white; 
            padding: 2rem; border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-group { margin-bottom: 1.5rem; }
        
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #555; }
        
        input, select, textarea { 
            width: 100%; padding: 0.75rem; 
            background-color: #fff; border: 1px solid #ddd; color: #333;
            border-radius: 5px; font-family: inherit; font-size: 1rem;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: #4a90e2;
        }
        
        button { background-color: #f39c12; color: white; padding: 0.75rem 2rem; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 1rem; }
        button:hover { background-color: #e67e22; }
        
        .current-img-msg { font-size: 0.85rem; color: #666; margin-top: 5px; }
        .optional-label { color: #666; font-weight: normal; font-size: 0.9em; }
        
        select { 
            appearance: none; 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23555' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E"); 
            background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Proposal #<?= $id ?></h1>
        <form action="process.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" required>
            </div>

            <div class="form-group">
                <label>Topic</label>
                <select name="topic" required>
                    <?php 
                    $topics = [
                        "Instrumentation",
                        "Galaxies and AGNs",
                        "Star Formation and Interstellar Medium",
                        "Solar System, Astrobiology and Exoplanets",
                        "Stars and Stellar Evolution",
                        "Cosmology",
                        "Plasma and Solar Physics",
                        "Astroparticles and High-energy Astrophysics"
                    ];
                    $currentTopic = $data['topic'] ?? '';
                    foreach ($topics as $t) {
                        $selected = ($currentTopic === $t) ? 'selected' : '';
                        echo "<option value='$t' $selected>$t</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Thesis Type</label>
                <select name="thesis_type" required>
                    <option value="bachelor" <?= $data['thesis_type'] == 'bachelor' ? 'selected' : '' ?>>Bachelor</option>
                    <option value="master" <?= $data['thesis_type'] == 'master' ? 'selected' : '' ?>>Master</option>
                </select>
            </div>

            <div class="form-group">
                <label>Duration (months)</label>
                <input type="text" name="duration" value="<?= htmlspecialchars($data['duration']) ?>" required>
            </div>

            <div class="form-group">
                <label>Supervisor</label>
                <input type="text" name="supervisor" value="<?= htmlspecialchars($data['supervisor']) ?>" required>
            </div>

            <div class="form-group">
                <label>Other Collaborators <span class="optional-label">(Optional)</span></label>
                <input type="text" name="collaborators" value="<?= htmlspecialchars($data['collaborators'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea id="description" name="description"><?= $data['description'] ?></textarea>
            </div>
            
            <div class="form-group">
                <label>References <span class="optional-label">(Optional)</span></label>
                <textarea name="references" rows="3"><?= htmlspecialchars($data['references'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Requirements <span class="optional-label">(Optional)</span></label>
                <textarea name="requirements" rows="4"><?= htmlspecialchars($data['requirements'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Contact Information</label>
                <input type="text" name="contacts" value="<?= htmlspecialchars($data['contacts']) ?>" required>
            </div>

            <div class="form-group">
                <label>Image (Optional)</label>
                <input type="file" name="image" accept="image/*">
                <?php if(!empty($data['image'])): ?>
                    <div class="current-img-msg">Current image: <?= $data['image'] ?> (Leave empty to keep this)</div>
                <?php endif; ?>
            </div>

            <button type="submit">Update Proposal</button>
        </form>
        <br>
        <a href="manage.php" style="color:#666;">Cancel</a>
    </div>
</body>
</html>