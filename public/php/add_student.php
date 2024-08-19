<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                INSERT INTO user (student_no, f_name, l_name, program, year, block, email, total_points)
                VALUES (:student_no, :f_name, :l_name, :program, :year, :block, :email, :total_points)
            ");
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
                INSERT INTO user (student_no, f_name, l_name, program, year, block, email, is_officer, total_points)
                VALUES (:student_no, :f_name, :l_name, :program, :year, :block, :email, :user, :total_points)
                ");
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
                INSERT INTO user (student_no, f_name, l_name, program, year, block, email, is_superuser, total_points)
                VALUES (:student_no, :f_name, :l_name, :program, :year, :block, :email, :user, :total_points)
                ");
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
                INSERT INTO user (student_no, f_name, l_name, program, year, block, email, is_admin, total_points)
                VALUES (:student_no, :f_name, :l_name, :program, :year, :block, :email, :user, :total_points)
                ");
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
            header("Location: ../crud_student.php");
            exit(); 
        } else {
            echo "Error updating deck. Please try again.";
        }
}