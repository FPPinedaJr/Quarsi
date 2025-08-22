<?php
require_once "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['students'])) {
    $students = $_POST['students'];
    $eventid = $_POST['event_id'];

    if (!empty($students)) {
        try {
            $pdo->beginTransaction();

            $placeholders = implode(',', array_fill(0, count($students), '?'));

            // Step 1: Get active event and logtime flags
            $sql = "SELECT * FROM event WHERE idevent = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$eventid]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                // Step 2: Build dynamic update query
                $updates = [];
                if ($event['morning_in'] == 1)
                    $updates[] = "a.morning_in = '23:23:23'";
                if ($event['morning_out'] == 1)
                    $updates[] = "a.morning_out = '23:23:23'";
                if ($event['afternoon_in'] == 1)
                    $updates[] = "a.afternoon_in = '23:23:23'";
                if ($event['afternoon_out'] == 1)
                    $updates[] = "a.afternoon_out = '23:23:23'";

                if (!empty($updates)) {
                    $updateStr = implode(", ", $updates);

                    $sql = "UPDATE attendance a 
                            INNER JOIN event e ON e.idevent = a.event 
                            SET $updateStr
                            WHERE a.user IN ($placeholders) AND a.event = $eventid";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($students);

                    // Commit transaction
                    $pdo->commit();

                    echo "Updated " . $stmt->rowCount() . " rows.";
                } else {
                    $pdo->rollBack();
                    echo "No logtimes active in event.";
                }
            } else {
                $pdo->rollBack();
                echo "No active event found.";
            }
        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "No students provided.";
    }
}
