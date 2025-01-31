<?php
session_start();
include_once "./includes/connect_db.php";

// Handle Create and Update Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name']);
    $short_name = trim($_POST['short_name']);
    $program = trim($_POST['program']);
    $abbreviation = trim($_POST['abbreviation']);

    if ($id > 0) {
        // Update existing record
        $stmt = $pdo->prepare("UPDATE organization SET name=?, short_name=?, program=?, abbreviation=? WHERE idorganization=?");
        $stmt->execute([$name, $short_name, $program, $abbreviation, $id]);
        echo "Organization updated successfully.";
    } else {
        // Insert new record
        $stmt = $pdo->prepare("INSERT INTO organization (name, short_name, program, abbreviation) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $short_name, $program, $abbreviation]);
        echo "Organization added successfully.";
    }
    exit;
}

// Handle Delete Operation
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = intval($_DELETE['id']);
    $stmt = $pdo->prepare("DELETE FROM organization WHERE idorganization=?");
    $stmt->execute([$id]);
    echo "Organization deleted successfully.";
    exit;
}

// Fetch and Return Organization Data
$stmt = $pdo->query("SELECT * FROM organization ORDER BY name ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>