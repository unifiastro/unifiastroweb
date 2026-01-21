<?php
$dataFile = 'data.json';

$title = $_POST['title'];
$topic = $_POST['topic'];
$thesis_type = $_POST['thesis_type'];
$duration = $_POST['duration'];
$supervisor = $_POST['supervisor'];
$collaborators = $_POST['collaborators'];
$description = $_POST['description'];
$contacts = $_POST['contacts'];

// Handle file upload
$imagePath = '';
if (!empty($_FILES['image']['name'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $imagePath = $uploadDir . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
}

// Load existing proposals
$proposals = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// Add new proposal
$newProposal = [
    "id" => count($proposals) + 1,
    "title" => $title,
    "topic" => $topic,
    "thesis_type" => $thesis_type,
    "duration" => $duration,
    "supervisor" => $supervisor,
    "collaborators" => $collaborators,
    "description" => $description,
    "contacts" => $contacts,
    "image" => $imagePath
];

$proposals[] = $newProposal;

// Save back to JSON
file_put_contents($dataFile, json_encode($proposals, JSON_PRETTY_PRINT));

header("Location: index.php");
exit();
?>