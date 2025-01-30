<?php
session_start();
include_once("./connect_db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['student'], $_POST['year'], $_POST['block'])) {
        echo "error: missing data";
        exit;
    }

    $student = intval($_POST['student']);  
    $year = intval($_POST['year']);
    $block = intval($_POST['block']);

    try {
        $stmt = $pdo->prepare("
            UPDATE user 
            SET year = :year, block = :block, must_set_blockyear = 0
            WHERE iduser = :student
        ");

        $stmt->execute([
            "year" => $year,
            "block" => $block,
            "student" => $student
        ]);
        
        $_SESSION['must_set_blockyear'] = 0;
        echo 'success';
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>
