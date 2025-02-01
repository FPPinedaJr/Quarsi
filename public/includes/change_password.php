<?php 
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt1 = $pdo->prepare('UPDATE user SET password=SHA2(?, 256) WHERE email=?');
    $stmt1->execute([$password, $email]);
    echo 'success';
}
