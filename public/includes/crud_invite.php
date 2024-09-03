<?php
session_start();
include_once "../includes/connect_db.php";

if  ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    $students = $_POST['students'];
    $idevent = $_POST['idevent']; 
    $null = NULL;
    $def = "00:00:00";

    $stmt = $pdo->prepare("SELECT log_time FROM event WHERE idevent=:idevent"); 
    $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT); 
    $stmt->execute();
    $log_time = $stmt->fetchColumn();

    foreach ($students as $student_id) {
        if ($_POST['action'] == 'invite') {
            $stmt1 = $pdo->prepare( "
            INSERT INTO attendance (event, user) 
            VALUES (:event, :user)
            ");
            $stmt1->bindParam(':event', $idevent, PDO::PARAM_INT);
            $stmt1->bindParam(':user', $student_id, PDO::PARAM_INT);
            
            if ($stmt1->execute()) {
                echo "Recorded students";
            } else {
                echo "Error inserting attendance for student ID $student_id. Please try again.";
            }
            header("Location: ../events.php");
            exit(); 
        } 

    }
}