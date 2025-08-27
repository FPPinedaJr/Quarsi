<?php
session_start();
include_once "../includes/connect_db.php";

function getEventDefaults(PDO $pdo, $idevent, $def = "00:00:00"): array
{
    $st = $pdo->prepare("
        SELECT morning_in, morning_out, afternoon_in, afternoon_out
        FROM event
        WHERE idevent = :idevent
        LIMIT 1
    ");
    $st->execute([':idevent' => $idevent]);
    $e = $st->fetch(PDO::FETCH_ASSOC) ?: [
        'morning_in' => 0,
        'morning_out' => 0,
        'afternoon_in' => 0,
        'afternoon_out' => 0
    ];

    return [
        'morning_in' => !empty($e['morning_in']) ? $def : NULL,
        'morning_out' => !empty($e['morning_out']) ? $def : NULL,
        'afternoon_in' => !empty($e['afternoon_in']) ? $def : NULL,
        'afternoon_out' => !empty($e['afternoon_out']) ? $def : NULL,
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    ini_set('max_execution_time', '900');

    if ($_POST['action'] == 'invite') {
        $students = $_POST['students'] ?? [];
        $logtime = $_POST['logtime'] ?? [];
        $idevent = $_POST['idevent'];
        $def = "00:00:00";

        if (in_array(1, $logtime)) {
            $stmt = $pdo->prepare("UPDATE event SET morning_in = 1 WHERE idevent = :idevent");
            $stmt->execute([':idevent' => $idevent]);
        }
        if (in_array(2, $logtime)) {
            $stmt = $pdo->prepare("UPDATE event SET morning_out = 1 WHERE idevent = :idevent");
            $stmt->execute([':idevent' => $idevent]);
        }
        if (in_array(3, $logtime)) {
            $stmt = $pdo->prepare("UPDATE event SET afternoon_in = 1 WHERE idevent = :idevent");
            $stmt->execute([':idevent' => $idevent]);
        }
        if (in_array(4, $logtime)) {
            $stmt = $pdo->prepare("UPDATE event SET afternoon_out = 1 WHERE idevent = :idevent");
            $stmt->execute([':idevent' => $idevent]);
        }

        $defaults = getEventDefaults($pdo, $idevent, $def);

        if (!empty($students)) {
            $query = "INSERT INTO attendance (event, user, morning_in, morning_out, afternoon_in, afternoon_out) VALUES ";
            $values = [];
            $params = [];
            foreach ($students as $student_id) {
                $values[] = "(?, ?, ?, ?, ?, ?)";
                array_push(
                    $params,
                    $idevent,
                    $student_id,
                    $defaults['morning_in'],
                    $defaults['morning_out'],
                    $defaults['afternoon_in'],
                    $defaults['afternoon_out']
                );
            }
            $query .= implode(", ", $values);
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            if ($stmt->rowCount() !== 1) {
                throw new Exception("Unexpected to affect row count: " . $stmt->rowCount());
            }
        }

        echo "success";
        exit();
    }

    if ($_POST['action'] == 'update_invite') {
        $students = $_POST['students'] ?? [];
        $idevent = $_POST['idevent'];
        $def = "00:00:00";

        $prev = $pdo->prepare("SELECT user FROM attendance WHERE event = :idevent");
        $prev->execute([':idevent' => $idevent]);
        $prev_students = array_column($prev->fetchAll(PDO::FETCH_ASSOC), 'user');

        $defaults = getEventDefaults($pdo, $idevent, $def);

        $values1 = [];
        $params1 = [];
        foreach ($students as $student_id) {
            if (!in_array($student_id, $prev_students)) {
                $values1[] = "(?, ?, ?, ?, ?, ?)";
                array_push(
                    $params1,
                    $idevent,
                    $student_id,
                    $defaults['morning_in'],
                    $defaults['morning_out'],
                    $defaults['afternoon_in'],
                    $defaults['afternoon_out']
                );
            }
        }
        if (!empty($values1)) {
            $query1 = "INSERT INTO attendance (event, user, morning_in, morning_out, afternoon_in, afternoon_out) VALUES "
                . implode(",", $values1);
            $stmt1 = $pdo->prepare($query1);
            $stmt1->execute($params1);
            if ($stmt1->rowCount() !== 1) {
                throw new Exception("Unexpected to affect row count: " . $stmt->rowCount());
            }
        }

        // Remove students no longer invited
        $to_delete = array_diff($prev_students, $students);
        if (!empty($to_delete)) {
            $placeholders = implode(",", array_fill(0, count($to_delete), "?"));
            $query2 = "DELETE FROM attendance WHERE event = ? AND user IN ($placeholders)";
            $params2 = array_merge([$idevent], array_values($to_delete));
            $stmt2 = $pdo->prepare($query2);
            $stmt2->execute($params2);
            if ($stmt2->rowCount() !== 1) {
                throw new Exception("Unexpected to affect row count: " . $stmt->rowCount());
            }
        }

        echo "success";
        exit();
    }
}
