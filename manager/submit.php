<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Thesis Proposal</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#description',
                plugins: 'lists link image table code help wordcount autolink emoticons',
                toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor | link image | removeformat',
                height: 400,
                menubar: true,
                statusbar: true,
                branding: false,
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; color: #333; background: #fff; text-align: justify; }',
                setup: function(editor) {
                    editor.on('change', function() { editor.save(); });
                }
            });
        });

        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function validateForm() {
            const description = tinymce.get('description').getContent().trim();
            if (!description) {
                alert('Please fill in the description field.');
                return false;
            }
            return true;
        }
    </script>
    <style>
        /* LIGHT THEME */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body { background-color: #f5f5f5; padding: 2rem; color: #333; }
        
        .container { 
            max-width: 800px; margin: 0 auto; 
            background: white; 
            padding: 2rem; border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
        }
        
        h1 { color: #333; margin-bottom: 2rem; text-align: center; font-size: 2rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        
        label { display: block; margin-bottom: 0.5rem; color: #555; font-weight: 500; }
        .required::after { content: " *"; color: #e74c3c; }
        
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem;
            background-color: #fff;
            border: 1px solid #ddd;
            color: #333;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }

        .file-upload { 
            border: 2px dashed #ddd; 
            padding: 2rem; text-align: center; border-radius: 5px; margin-bottom: 1rem; cursor: pointer; transition: border-color 0.3s; 
            background-color: #fafafa;
        }
        .file-upload:hover { border-color: #4a90e2; }
        .file-upload p { color: #666; margin-top: 1rem; }
        
        .optional-label { color: #666; font-size: 0.9rem; margin-left: 0.5rem; font-weight: normal; }
        
        button { 
            background-color: #4a90e2; 
            color: white; padding: 0.75rem 2rem; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; width: 100%; transition: background-color 0.3s; 
        }
        button:hover { background-color: #357abd; }
        
        .preview-image { max-width: 100%; margin-top: 1rem; border-radius: 5px; display: none; }
        
        .tox-tinymce { border-radius: 5px !important; border-color: #ddd !important; }
        
        select { 
            appearance: none; 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23555' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E"); 
            background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submit a Thesis Proposal</h1>
        <form action="process.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="title" class="required">Title</label>
                <input type="text" id="title" name="title" required placeholder="Enter your thesis title">
            </div>

            <div class="form-group">
                <label for="topic" class="required">Topic</label>
                <select id="topic" name="topic" required>
                    <option value="">Select a Topic</option>
                    <option value="Instrumentation">Instrumentation</option>
                    <option value="Galaxies and AGNs">Galaxies and AGNs</option>
                    <option value="Star Formation and Interstellar Medium">Star Formation and Interstellar Medium</option>
                    <option value="Solar System, Astrobiology and Exoplanets">Solar System, Astrobiology and Exoplanets</option>
                    <option value="Stars and Stellar Evolution">Stars and Stellar Evolution</option>
                    <option value="Cosmology">Cosmology</option>
                    <option value="Plasma and Solar Physics">Plasma and Solar Physics</option>
                    <option value="Astroparticles and High-energy Astrophysics">Astroparticles and High-energy Astrophysics</option>
                </select>
            </div>

            <div class="form-group">
                <label for="thesis_type" class="required">Thesis Type</label>
                <select id="thesis_type" name="thesis_type" required>
                    <option value="">Select thesis type</option>
                    <option value="bachelor">Bachelor</option>
                    <option value="master">Master</option>
                </select>
            </div>

            <div class="form-group">
                <label for="duration" class="required">Thesis Duration (months)</label>
                <input type="text" id="duration" name="duration" required placeholder="e.g. 6-9">
            </div>

            <div class="form-group">
                <label for="supervisor" class="required">Supervisor</label>
                <input type="text" id="supervisor" name="supervisor" required placeholder="Enter supervisor's name">
            </div>

            <div class="form-group">
                <label for="collaborators">Other Collaborators <span class="optional-label">(Optional)</span></label>
                <input type="text" id="collaborators" name="collaborators" placeholder="Enter names of other collaborators">
            </div>
            
            <div class="form-group">
                <label for="description" class="required">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            
            <div class="form-group">
                <label for="references">References <span class="optional-label">(Optional)</span></label>
                <textarea id="references" name="references" rows="3" placeholder="Enter relevant papers or links..."></textarea>
            </div>

            <div class="form-group">
                <label for="requirements">Requirements <span class="optional-label">(Optional)</span></label>
                <textarea id="requirements" name="requirements" rows="4" placeholder="List any specific requirements..."></textarea>
            </div>

            <div class="form-group">
                <label for="contacts" class="required">Contact Information</label>
                <input type="text" id="contacts" name="contacts" required placeholder="Enter your contact details">
            </div>

            <div class="form-group">
                <label for="image">Image <span class="optional-label">(Optional)</span></label>
                <div class="file-upload">
                    <input type="file" id="image" name="image" onchange="previewImage(this)" accept="image/*">
                    <p>Click to upload an image</p>
                </div>
                <img id="preview" class="preview-image" alt="Preview">
            </div>

            <button type="submit">Submit Proposal</button>
        </form>
    </div>
</body>
</html>