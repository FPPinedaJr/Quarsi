<?php 
session_start();
include_once "../includes/connect_db.php";

$email = $_POST['email'];
$u_otp = hash('sha256', trim($_POST['code']));

$stmt1 = $pdo->prepare('SELECT otp, otp_expiry FROM user WHERE email=?');
$stmt1->execute([$email]);
$user = $stmt1->fetch(PDO::FETCH_ASSOC);

$time_diff = $pdo->prepare('SELECT TIMESTAMPDIFF(MINUTE, ?, NOW())');
$time_diff->execute([$user['otp_expiry']]);
$diff = $time_diff->fetchColumn();

if (!$user) {
    echo "error";
    exit();
}

if ($diff > 5) {
    echo 'expired';
    exit();
}

if ($u_otp === $user['otp']) {
    echo "match";
    exit();
} else {
    echo "error";
    exit();
}
