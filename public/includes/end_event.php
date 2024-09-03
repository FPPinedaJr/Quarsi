s
<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idevent = $_POST['idEndEvent'];

    $stmt = $pdo->prepare("
        UPDATE event
        SET is_active = 0
        WHERE idevent = :idevent;
    ");
    $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);
    $stmt->execute();


    $stmt = $pdo->prepare("
        UPDATE attendance
        INNER JOIN (
        SELECT 
            idattendance,
            (CASE 
            WHEN (IF(morning_in IS NOT NULL, 1, 0) +
                    IF(morning_out IS NOT NULL, 1, 0) +
                    IF(afternoon_in IS NOT NULL, 1, 0) +
                    IF(afternoon_out IS NOT NULL, 1, 0)) > 0 
            THEN ((event.set_points / 
                    (IF(morning_in IS NOT NULL, 1, 0) +
                    IF(morning_out IS NOT NULL, 1, 0) +
                    IF(afternoon_in IS NOT NULL, 1, 0) +
                    IF(afternoon_out IS NOT NULL, 1, 0))) 
                    * 
                    (IF(morning_in = '00:00:00', 1, 0) +
                    IF(morning_out = '00:00:00', 1, 0) +
                    IF(afternoon_in = '00:00:00', 1, 0) +
                    IF(afternoon_out = '00:00:00', 1, 0))) 
                    * -1
            ELSE 0 
            END) AS score
        FROM attendance
        INNER JOIN event ON attendance.event = event.idevent
        ) AS computed_scores ON attendance.idattendance = computed_scores.idattendance
        SET attendance.points = computed_scores.score
        WHERE event = :idevent;
    ");
    $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);
    $stmt->execute();


    $stmt = $pdo->prepare("
        UPDATE user u
        JOIN (
            SELECT 
                attendance.user AS user_id,
                SUM(
                    CASE 
                        WHEN attendance_count > 0 THEN 
                        ((event.set_points / attendance_count) * absent_count) * -1
                        ELSE 0
                    END
                ) AS total_score
            FROM attendance
            INNER JOIN event ON attendance.event = event.idevent
            CROSS JOIN (
                SELECT 
                    idattendance,
                    (IF(morning_in IS NOT NULL, 1, 0) +
                    IF(morning_out IS NOT NULL, 1, 0) +
                    IF(afternoon_in IS NOT NULL, 1, 0) +
                    IF(afternoon_out IS NOT NULL, 1, 0)) AS attendance_count,
                    (IF(morning_in = '00:00:00', 1, 0) +
                    IF(morning_out = '00:00:00', 1, 0) +
                    IF(afternoon_in = '00:00:00', 1, 0) +
                    IF(afternoon_out = '00:00:00', 1, 0)) AS absent_count
                FROM attendance
            ) a
            WHERE attendance.idattendance = a.idattendance
            AND attendance.event = :idevent
            GROUP BY attendance.user
        ) AS computed_scores ON u.iduser = computed_scores.user_id
        SET u.total_points = u.total_points + computed_scores.total_score;
    ");
    $stmt->bindParam(':idevent', $idevent, PDO::PARAM_INT);
    $stmt->execute();


    header('location: ../events.php');




}

