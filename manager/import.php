<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Projects from CSV</title>
    <style>
        body { font-family: "Segoe UI", sans-serif; padding: 2rem; background: #f5f5f5; color: #333; }
        .container { 
            max-width: 600px; margin: 0 auto; 
            background: white; padding: 2rem; border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        h1 { margin-bottom: 1.5rem; color: #333; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="file"] { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; background: #fff; }
        button { 
            background-color: #4a90e2; color: white; padding: 0.75rem 2rem; 
            border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; width: 100%; 
        }
        button:hover { background-color: #357abd; }
        .note { background: #eef; padding: 10px; border-radius: 5px; font-size: 0.9rem; margin-bottom: 20px; border-left: 4px solid #4a90e2; }
        .back-link { display: block; margin-top: 15px; text-align: center; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Import CSV</h1>
        
        <div class="note">
            <strong>Expected CSV Format:</strong><br>
            Contact info, Thesis type, Title, Supervisor, Description, Duration, References, Materials, Requirements
        </div>

        <form action="process_import.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select CSV File</label>
                <input type="file" name="csv_file" accept=".csv" required>
            </div>
            <button type="submit">Import Proposals</button>
        </form>
        
        <a href="manage.php" class="back-link">Cancel</a>
    </div>
</body>
</html>