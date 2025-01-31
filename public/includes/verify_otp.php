<?php 
session_start();
include_once "../includes/connect_db.php";

$u_otp = $_POST['code'];
$email = $_POST['email'];
$otp = $_SESSION['otp'];

if ($u_otp == $otp) {
    echo "match";
    exit;
} else {
    echo "error";
}