<?php
session_start();
include_once "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'] ?? '';

    if ($method === 'add') {
        // Insert new record
        $stmt = $pdo->prepare("INSERT INTO organization (name, short_name, program, abbreviation) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['short_name'], $_POST['program'], $_POST['abbreviation']]);
        echo "Organization added successfully.";
    } elseif ($method === 'edit' && isset($_POST['id']) && intval($_POST['id']) > 0) {
        // Update existing record
        $stmt = $pdo->prepare("UPDATE organization SET name=?, short_name=?, program=?, abbreviation=? WHERE idorganization=?");
        $stmt->execute([$_POST['name'], $_POST['short_name'], $_POST['program'], $_POST['abbreviation'], $_POST['id']]);
        echo "Organization updated successfully.";
    } elseif ($method === 'delete' && isset($_POST['id']) && intval($_POST['id']) > 0) {
        // Delete record
        $stmt = $pdo->prepare("DELETE FROM organization WHERE idorganization=?");
        $stmt->execute([$_POST['id']]);
        echo "Organization deleted successfully.";
    } else {
        echo intval($_POST['id']);
        echo "Invalid request.";
    }
    exit;
}
?>