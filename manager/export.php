<?php
// Set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=thesis_proposals_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF"); // BOM for Excel

// Define Headers
fputcsv($output, [
    'Thesis type', 
    'Topics', 
    'Title', 
    'Supervisor', 
    'Contact information', 
    'Description', 
    'Duration', 
    'References', 
    'Material', 
    'Requirements',
    'Collaborators',
    'Image'
], ",", "\"", "\\");

$files = glob('../theses/raw/*.json');
natsort($files);

foreach ($files as $file) {
    $data = json_decode(file_get_contents($file), true);
    
    $type = ucfirst($data['thesis_type'] ?? '');
    $topic = $data['topic'] ?? '';
    $title = $data['title'] ?? '';
    $supervisor = $data['supervisor'] ?? '';
    $contacts = $data['contacts'] ?? '';
    $description = $data['description'] ?? '';
    $duration = $data['duration'] ?? '';
    $references = $data['references'] ?? '';
    $requirements = $data['requirements'] ?? '';
    $collaborators = $data['collaborators'] ?? '';
    $image = $data['image'] ?? ''; // Get filename

    // Write row
    fputcsv($output, [
        $type,
        $topic,
        $title,
        $supervisor,
        $contacts,
        $description,
        $duration,
        $references,
        '', // Material (Empty)
        $requirements,
        $collaborators,
        $image // Save filename
    ], ",", "\"", "\\");
}

fclose($output);
exit();
?>