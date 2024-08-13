<?php

echo $_SESSION['usertype'];
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == true) {
        if ($_SESSION['usertype'] == 1){
            header('Location: ./student/');
        } else if ($_SESSION['usertype'] == 2){
            header('Location: ./officer/');
        } else if ($_SESSION['usertype'] == 3){
            header('Location: ./admin/');
        }
    }
    exit();
}

header('Location: ./sign_in/');
exit();
