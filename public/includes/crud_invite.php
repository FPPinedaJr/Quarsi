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

    $stmt = $pdo->prepare("SELECT log_time FROM event WHERE idevent=:idevent"); 
    $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT); 
    $stmt->execute();
    $log_time = $stmt->fetchColumn();

    foreach ($students as $student_id) {
        if ($log_time == 1) {
            $stmt1 = $pdo->prepare( "
            INSERT INTO attendance (event, user, morning_in) 
            VALUES (:event, :user, :morning_in)
            ");
            $stmt1->bindParam(':event', $idevent, PDO::PARAM_INT);
            $stmt1->bindParam(':user', $student_id, PDO::PARAM_INT);
            $stmt1->bindParam(':morning_in', $def_time, PDO::PARAM_STR);

        } elseif ($log_time == 2) {
            $stmt1 = $pdo->prepare( "
            INSERT INTO attendance (event, user, morning_out) 
            VALUES (:event, :user, :morning_out)
            ");
            $stmt1->bindParam(':event', $idevent, PDO::PARAM_INT);
            $stmt1->bindParam(':user', $student_id, PDO::PARAM_INT);
            $stmt1->bindParam(':morning_out', $def_time, PDO::PARAM_STR);

        } elseif ($log_time == 3) {
            $stmt1 = $pdo->prepare( "
            INSERT INTO attendance (event, user, afternoon_in) 
            VALUES (:event, :user, :afternoon_in)
            ");
            $stmt1->bindParam(':event', $idevent, PDO::PARAM_INT);
            $stmt1->bindParam(':user', $student_id, PDO::PARAM_INT);
            $stmt1->bindParam(':afternoon_in', $def_time, PDO::PARAM_STR);

        } elseif ($log_time == 4) {
            $stmt1 = $pdo->prepare( "
            INSERT INTO attendance (event, user, afternoon_out) 
            VALUES (:event, :user, :afternoon_out)
            ");
            $stmt1->bindParam(':event', $idevent, PDO::PARAM_INT);
            $stmt1->bindParam(':user', $student_id, PDO::PARAM_INT);
            $stmt1->bindParam(':afternoon_out', $def_time, PDO::PARAM_STR);
        }


        if ($stmt1->execute()) {
            echo "Recorded students";
        } else {
            echo "Error inserting attendance for student ID $student_id. Please try again.";
        }
    }

    header("Location: ../events.php");
    exit(); 
}

