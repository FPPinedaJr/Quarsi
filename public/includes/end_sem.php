<?php
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedStudents = $_POST['students'];

    if (empty($selectedStudents)) {
        http_response_code(400);
        echo 'No students selected.';
        exit;
    }

    try {
        $pdo->beginTransaction();

        $pdo->exec("UPDATE user SET must_set_blockyear = 0");

        $updateMustSetQuery = "UPDATE user SET year = 0, block = 0, must_set_blockyear = 1 WHERE iduser IN (" . implode(',', array_fill(0, count($selectedStudents), '?')) . ")";
        $stmt = $pdo->prepare($updateMustSetQuery);
        $stmt->execute($selectedStudents);
        
        $pdo->exec("DELETE FROM user WHERE must_set_blockyear = 0");
        $pdo->exec("TRUNCATE TABLE attendance");
        $pdo->exec("TRUNCATE TABLE event");

        $pdo->commit();

        echo 'success';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo 'Error: ' . $e->getMessage();
    }
}
