<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        ini_set('max_execution_time', '900');
        
        if ($_POST['action'] == 'invite') {
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
                echo "Recorded students";
                header("location: ../events.php");

            } else {
                echo "Error inserting attendance. Please try again.";
            }

            exit();
        } 
        

        
        
        else if ($_POST['action'] == 'update_invite') {
            $students = $_POST['students'];
            $logtime = $_POST['logtime'];
            $idevent = $_POST['idevent'];
            $null = NULL;
            $def = "00:00:00";

            $previous_mor_in = $pdo->prepare("SELECT morning_in FROM attendance WHERE event=:event LIMIT 1");
            $previous_mor_in->execute([':event' => $idevent]);
            $previous_mor_in = $previous_mor_in->fetchColumn() ? 1 : 0;

            $previous_mor_out = $pdo->prepare("SELECT morning_out FROM attendance WHERE event=:event LIMIT 1");
            $previous_mor_out->execute([':event' => $idevent]);
            $previous_mor_out = $previous_mor_out->fetchColumn() ? 1 : 0;

            $previous_aft_in = $pdo->prepare("SELECT afternoon_in FROM attendance WHERE event=:event LIMIT 1");
            $previous_aft_in->execute([':event' => $idevent]);
            $previous_aft_in = $previous_aft_in->fetchColumn() ? 1 : 0;

            $previous_aft_out = $pdo->prepare("SELECT afternoon_out FROM attendance WHERE event=:event LIMIT 1");
            $previous_aft_out->execute([':event' => $idevent]);
            $previous_aft_out = $previous_aft_out->fetchColumn() ? 1 : 0;

            $prev_logs = [$previous_mor_in, $previous_mor_out, $previous_aft_in, $previous_aft_out];

            $logtime_flags = [0, 0, 0, 0];

            // Update flags based on provided logtime
            foreach ($logtime as $selected_log) {
                $logtime_flags[$selected_log - 1] = 1; // Adjust index (logtime starts from 1)
            }            
            
            $morning_in = NULL;
            $morning_out = NULL;
            $afternoon_in = NULL;
            $afternoon_out = NULL;

            for ($i = 0; $i < count($logtime_flags); $i++) {
                if ($i == 0) {
                    if ($logtime_flags[$i] != 0) {
                        $morning_in = $def;
                        $stmt = $pdo->prepare("UPDATE event SET morning_in = 1 WHERE idevent = :idevent");
                    } else {
                        $stmt = $pdo->prepare("UPDATE event SET morning_in = NULL WHERE idevent = :idevent");
                    }
                    $stmt->execute([':idevent' => $idevent]);
                } else if ($i == 1) {
                    if ($logtime_flags[$i] != 0) {
                        $morning_out = $def;
                        $stmt = $pdo->prepare("UPDATE event SET morning_out = 1 WHERE idevent = :idevent");
                    } else {
                        $stmt = $pdo->prepare("UPDATE event SET morning_out = NULL WHERE idevent = :idevent");
                    }
                    $stmt->execute([':idevent' => $idevent]);
                } else if ($i == 2) {
                    if ($logtime_flags[$i] != 0) {
                        $afternoon_in = $def;
                        $stmt = $pdo->prepare("UPDATE event SET afternoon_in = 1 WHERE idevent = :idevent");
                    } else {
                        $stmt = $pdo->prepare("UPDATE event SET afternoon_in = NULL WHERE idevent = :idevent");
                    }
                    $stmt->execute([':idevent' => $idevent]);
                } else {
                    if ($logtime_flags[$i] != 0) {
                        $afternoon_out = $def;
                        $stmt = $pdo->prepare("UPDATE event SET afternoon_out = 1 WHERE idevent = :idevent");
                    } else {
                        $stmt = $pdo->prepare("UPDATE event SET afternoon_out = NULL WHERE idevent = :idevent");
                    }
                    $stmt->execute([':idevent' => $idevent]);
                }
            }

            $prev = $pdo->prepare("SELECT user FROM attendance WHERE event=:idevent");
            $prev->execute([':idevent' => $idevent]);

            $prev_students = array_column($prev->fetchAll(PDO::FETCH_ASSOC), 'user');

            foreach ($students as $student_id):
                if (in_array($student_id, $prev_students)) {
                    $stmt1 = $pdo->prepare("UPDATE attendance SET 
                                                morning_in=:morning_in, morning_out=:morning_out, 
                                                afternoon_in=:afternoon_in, afternoon_out=:afternoon_out 
                                                WHERE event=:event AND user=:user");
                } else {
                    $stmt1 = $pdo->prepare("
                            INSERT INTO attendance (event, user, morning_in, morning_out, afternoon_in, afternoon_out) 
                            VALUES (:event, :user, :morning_in, :morning_out, :afternoon_in, :afternoon_out)
                            ");
                }
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
        
                $stmt1->execute();
            endforeach;

            header("Location: ../events.php");
            exit();
        }

    }

}
