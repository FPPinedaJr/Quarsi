<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        ini_set('max_execution_time', '900');
        
        if ($_POST['action'] == 'invite') {
            $students = $_POST['students'];
            $logtime = [];
            if (isset($_POST['logtime']) && $_POST['logtime'] != NULL) {
                $logtime = $_POST['logtime'];
            }
            $idevent = $_POST['idevent'];
            $null = NULL;
            $def = "00:00:00";

            $morning_in = NULL;
            $morning_out = NULL;
            $afternoon_in = NULL;
            $afternoon_out = NULL;

            if (in_array(1, $logtime)) {
                $morning_in = $def;
                $stmt = $pdo->prepare("UPDATE event SET morning_in = 1 WHERE idevent = :idevent");
                $stmt->execute([':idevent' => $idevent]);
            }
            if (in_array(2, $logtime)) {
                $morning_out = $def;
                $stmt = $pdo->prepare("UPDATE event SET morning_out = 1 WHERE idevent = :idevent");
                $stmt->execute([':idevent' => $idevent]);
            }
            if (in_array(3, $logtime)) {
                $afternoon_in = $def;
                $stmt = $pdo->prepare("UPDATE event SET afternoon_in = 1 WHERE idevent = :idevent");
                $stmt->execute([':idevent' => $idevent]);
            }
            if (in_array(4, $logtime)) {
                $afternoon_out = $def;
                $stmt = $pdo->prepare("UPDATE event SET afternoon_out = 1 WHERE idevent = :idevent");
                $stmt->execute([':idevent' => $idevent]);
            }

            $query = "INSERT INTO attendance (event, user, morning_in, morning_out, afternoon_in, afternoon_out) VALUES ";

            $values = [];
            $params = [];

            foreach ($students as $index => $student_id) {
                $values[] = "(?, ?, ?, ?, ?, ?)";
                array_push($params, $idevent, $student_id, $morning_in, $morning_out, $afternoon_in, $afternoon_out);
            }

            $query .= implode(", ", $values);

            $stmt = $pdo->prepare($query);

            if ($stmt->execute($params)) {
                echo "success"; 
                exit();
            } else {
                echo "Error inserting attendance. Please try again.";
            }

            exit();
        } 
        
        else if ($_POST['action'] == 'update_invite') {
            $students = $_POST['students'];
            $logtime =[];
            if (isset($_POST['logtime']) && $_POST['logtime'] != NULL) {
                $logtime = $_POST['logtime'];
            }
            $idevent = $_POST['idevent'];
            $null = NULL;
            $def = "00:00:00";

            $prev = $pdo->prepare("SELECT user FROM attendance WHERE event=:idevent");
            $prev->execute([':idevent' => $idevent]);

            $prev_students = array_column($prev->fetchAll(PDO::FETCH_ASSOC), 'user');
            
            $morning_in = NULL;
            $morning_out = NULL;
            $afternoon_in = NULL;
            $afternoon_out = NULL;

            if (in_array(1, $logtime)) {
                return;
            }
            if (in_array(2, $logtime)) {
                return;
            }
            if (in_array(3, $logtime)) {
                return;
            }
            if (in_array(4, $logtime)) {
                return;
            }

            $query1 = "INSERT INTO attendance (event, user, morning_in, morning_out, afternoon_in, afternoon_out) VALUES ";
            $values1 = [];
            $params1 = [];

            foreach ($students as $student_id):
                if (!in_array($student_id, $prev_students)) {
                    $values1[] = "(?, ?, ?, ?, ?, ?)";
                    array_push($params1, $idevent, $student_id, $morning_in, $morning_out, $afternoon_in, $afternoon_out);
                }
            endforeach;

            if (!empty($values1)) {
                $query1 .= implode(",", $values1);
                $stmt1 = $pdo->prepare($query1);
                $stmt1->execute($params1);
            } else {
                $stmt3 = $pdo->prepare("UPDATE attendance SET morning_in=?, morning_out=?, afternoon_in=?, afternoon_out=? WHERE event=?");
                $stmt3->execute([$morning_in, $morning_out, $afternoon_in, $afternoon_out, $idevent]);
            }

            $query2 = "DELETE FROM attendance WHERE event=? AND user in (";
            $values2 = [];
            $params2 = [$idevent];

            foreach ($prev_students as $student_id):
                if (!in_array($student_id, $students)) {
                    $values2[] = "?";
                    array_push($params2, $student_id);
                }
            endforeach;

            if(!empty($values2)) {
                $query2 .= implode(",", $values2) . ")";
                $stmt2 = $pdo->prepare($query2);
                $stmt2->execute($params2);
            }
            
            echo "success";
            exit();

        }

    }

}
