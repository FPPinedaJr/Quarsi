<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_no = trim($_POST['student_no']);

    // Check if student number exists in the database
    $stmt = $pdo->prepare("SELECT iduser FROM user WHERE student_no = :student_no");
    $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("
            UPDATE user 
            SET password = SHA2(?, 256) 
            WHERE student_no = ?
        ");


        if ($stmt->execute([$student_no, $student_no])) {
            echo "Password successfully resetted.";
        } else {
            echo "Error resetting password.";
        }
    } else {
        echo "Student Number not found.";
    }
}
