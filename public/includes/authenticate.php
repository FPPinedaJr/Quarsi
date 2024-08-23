<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once ("./connect_db.php");




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashed_password = hash('sha256', $password);

    if ($email == '') {
        echo "err_empty_email";
        exit();
    } else if ($password == '') {
        echo "err_empty_password";
        exit();
    }

    $stmt = $pdo->prepare("
            SELECT 
                u.iduser, 
                u.student_no, 
                u.f_name, 
                u.l_name, 
                o.abbreviation AS program, 
                o.abbreviation AS program_short_name, 
                u.year, 
                u.block, 
                u.email, 
                u.password, 
                u.is_officer, 
                u.is_superuser, 
                u.is_admin
            FROM 
                user u
            INNER JOIN 
                organization o ON u.organization = o.idorganization
            WHERE 
                u.email = ?;
        ");

    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        if ($hashed_password == $row['password']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['userid'] = $row['iduser'];
            $_SESSION['username'] = $row['f_name'] . ' ' . $row['l_name'];
            $_SESSION['section'] = $row['program_short_name'] . $row['year'] . ' - Block ' . $row['block'];
            $_SESSION['student_number'] = $row['student_no'];

        $_SESSION['is_officer'] = 0;
        $_SESSION['is_superuser'] = 0;
        $_SESSION['is_admin'] = 0;
        
        $_SESSION['program'] = $row['program'];

        
        if ($row['is_officer'] == 1) {
            $_SESSION['is_officer'] = 1;
        } 
        
        if ($row['is_superuser'] == 1) {
            $_SESSION['is_superuser'] = 1;
        } 
        
        if ($row['is_admin'] == 1){
            $_SESSION['is_admin'] = 1;
        } 
        

            echo "success";
            exit();
        } else {
            echo "err_password";
            exit();
        }
    } else {
        echo "err_not_registered";
    }
}
