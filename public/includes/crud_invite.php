<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    $students = $_POST['students'];
    $logtime = $_POST['logtime'];
    $idevent = $_POST['idevent'];
    $null = NULL;
    $def = "00:00:00";

    $morning_in = NULL;
    $morning_out = NULL;
    $afternoon_in = NULL;
    $afternoon_out = NULL;

    if (in_array(1, $logtime)) {
        $morning_in = $def;
    }
    if (in_array(2, $logtime)) {
        $morning_out = $def;
    }
    if (in_array(3, $logtime)) {
        $afternoon_in = $def;
    }
    if (in_array(4, $logtime)) {
        $afternoon_out = $def;
    }

    foreach ($students as $student_id): 
        $stmt1 = $pdo->prepare("
                INSERT INTO attendance (event, user, morning_in, morning_out, afternoon_in, afternoon_out) 
                VALUES (:event, :user, :morning_in, :morning_out, :afternoon_in, :afternoon_out)
                ");
        $stmt1->bindParam(':event', $idevent, PDO::PARAM_INT);
        $stmt1->bindParam(':user', $student_id, PDO::PARAM_INT);

        if (is_null($morning_in)) {
            $stmt1->bindParam(':morning_in', $morning_in, PDO::PARAM_NULL);
        } else {
            $stmt1->bindParam(':morning_in', $morning_in, PDO::PARAM_STR);
        }

        if (is_null($morning_out)) {
            $stmt1->bindParam(':morning_out', $morning_out, PDO::PARAM_NULL);
        } else {
            $stmt1->bindParam(':morning_out', $morning_out, PDO::PARAM_STR);
        }

        if (is_null($afternoon_in)) {
            $stmt1->bindParam(':afternoon_in', $afternoon_in, PDO::PARAM_NULL);
        } else {
            $stmt1->bindParam(':afternoon_in', $afternoon_in, PDO::PARAM_STR);
        }

        if (is_null($afternoon_out)) {
            $stmt1->bindParam(':afternoon_out', $afternoon_out, PDO::PARAM_NULL);
        } else {
            $stmt1->bindParam(':afternoon_out', $afternoon_out, PDO::PARAM_STR);
        }

        if ($stmt1->execute()) {
            echo "Recorded students";
        } else {
            echo "Error inserting attendance for student ID $student_id. Please try again.";
        }
    endforeach;
    
    header("Location: ../events.php");
    exit();
}
