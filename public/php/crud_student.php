<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'submit') {
            $iduser = $_POST['iduser'];
            $f_name = $_POST['f_name'];
            $l_name = $_POST['l_name'];
            $program = $_POST['program'];
            $student_no = $_POST['student_no'];
            $year = $_POST['year'];
            $block = $_POST['block'];
            $email = $_POST['email'];
            $user_type = $_POST['user_type'];
            $total_points = $_POST['total_points'];
            $user = 1;
            
            if ($user_type == 0) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, program=:program,
                        year=:year, block=:block, email=:email, total_points=:total_points
                    WHERE iduser=:iduser
                ");
                $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                $stmt->bindParam(':program', $program, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);

            } else if ($user_type == 1){
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, program=:program,
                        year=:year, block=:block, email=:email, is_officer=:user, total_points=:total_points
                    WHERE iduser=:iduser
                    ");
                    $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                    $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                    $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                    $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                    $stmt->bindParam(':program', $program, PDO::PARAM_INT);
                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                    $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);
                    $stmt->bindParam(':user', $user, PDO::PARAM_INT);
            } else if ($user_type == 2) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, program=:program,
                        year=:year, block=:block, email=:email, is_superuser=:user, total_points=:total_points
                    WHERE iduser=:iduser
                    ");
                    $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                    $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                    $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                    $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                    $stmt->bindParam(':program', $program, PDO::PARAM_INT);
                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                    $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);
                    $stmt->bindParam(':user', $user, PDO::PARAM_INT);
            } else if ($user_type == 3) {
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET student_no=:student_no, f_name=:f_name, l_name=:l_name, program=:program,
                        year=:year, block=:block, email=:email, is_admin=:user, total_points=:total_points
                    WHERE iduser=:iduser
                    ");
                    $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
                    $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
                    $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
                    $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
                    $stmt->bindParam(':program', $program, PDO::PARAM_INT);
                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                    $stmt->bindParam(':block', $block, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':total_points', $total_points, PDO::PARAM_INT);
                    $stmt->bindParam(':user', $user, PDO::PARAM_INT);
            }
            if ($stmt->execute()) {
                header("Location: ../student.php");
                exit(); 
            } else {
                echo "Error updating deck. Please try again.";
            }

        } else if ($_POST['action'] == 'delete') {
            $iduser = $_POST['iduser'];

            $stmt = $pdo->prepare("
                DELETE FROM user
                WHERE iduser=:iduser
            ");
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("Location: ../student.php");
                exit(); 
            } else {
                echo "Error updating deck. Please try again.";
            }
        } else if ($_POST['action'] == 'add') {
            $iduser = $_POST['iduser'];
            $f_name = $_POST['f_name'];
            $l_name = $_POST['l_name'];
            $program = $_POST['program'];
            $student_no = $_POST['student_no'];
            $year = $_POST['year'];
            $block = $_POST['block'];
            $email = $_POST['email'];
            $password = $_POST['student_no'];
            
            $stmt = $pdo->prepare("
                INSERT INTO user (student_no, f_name, l_name, program, year, block, email, password)
                VALUES (:student_no, :f_name, :l_name, :program, :year, :block, :email, SHA2(:password, 256))
            ");
            $stmt->bindParam(':student_no', $student_no, PDO::PARAM_STR);
            $stmt->bindParam(':f_name', $f_name, PDO::PARAM_STR);
            $stmt->bindParam(':l_name', $l_name, PDO::PARAM_STR);
            $stmt->bindParam(':program', $program, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':block', $block, PDO::PARAM_INT);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                header("Location: ../student.php");
                exit(); 
            } else {
                echo "Error updating deck. Please try again.";
            }
    }
}