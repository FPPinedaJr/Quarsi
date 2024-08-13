<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } 
include_once("../../__includes__/connect_db.php");




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashed_password = hash('sha256', $password);

    if ($email == ''){
        echo "err_empty_email";
        exit();
    } 
    else if ($password == '') {
        echo "err_empty_password";
        exit();
    }
    
    $stmt = $pdo->prepare("
            SELECT * FROM user WHERE email = ?;
        ");

    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        if ($hashed_password ==  $row['password']){
        $_SESSION['logged_in'] = true;
        $_SESSION['userid'] = $row['iduser'];
        $_SESSION['username'] = $row['f_name'] . ' ' . $row['l_name'];
        if ($row['is_admin'] == 1){
            $_SESSION['usertype'] = 3;
        } else if ($row['is_officer'] == 1) {
            $_SESSION['usertype'] = 2;
        } else {
            $_SESSION['usertype'] =1;
        }
        echo "success";
        exit();
        } 
        else {
        echo "err_password";
        exit();
        }
    } 

    else {
        echo "err_not_registered";
    }
}
