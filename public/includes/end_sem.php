<?php
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedStudents = $_POST['students'];
    $willIncreaseYear = isset($_POST['will_increase_year']) ? intval($_POST['will_increase_year']) : 0;

    if (empty($selectedStudents)) {
        http_response_code(400);
        echo 'No students selected.';
        exit;
    }

    try {
        $pdo->beginTransaction();

        $placeholders = implode(',', array_fill(0, count($selectedStudents), '?'));
        $deleteQuery = "DELETE FROM user WHERE iduser NOT IN ($placeholders) AND is_admin = 0 AND is_officer = 0";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute($selectedStudents);

        if ($willIncreaseYear === 1) {
            $pdo->exec("UPDATE user SET year = year + 1 WHERE year IN (1, 2, 3)");
        }

        $pdo->exec("TRUNCATE TABLE attendance");
        $pdo->exec("TRUNCATE TABLE event");

        $pdo->commit();

        echo 'success';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            echo 'Error: ' . $e->getMessage();
        }

    }
}
