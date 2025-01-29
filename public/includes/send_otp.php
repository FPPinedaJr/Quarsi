<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'verify_email') {
        $email = $_POST['email'];

        $stmt1 = $pdo->prepare('SELECT * from user where email=?');
        $stmt1->execute([$email]);

        if ($stmt1->rowCount() > 0) {
            echo 'exists';
        } else {
            echo 'not found';
        }

    }
}
