<?php
include_once("./connect_db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $event_id = $_POST['event_id'];

    $morning_in = $_POST['morning_in'] === '' ? null : $_POST['morning_in'];
    $morning_out = $_POST['morning_out'] === '' ? null : $_POST['morning_out'];
    $afternoon_in = $_POST['afternoon_in'] === '' ? null : $_POST['afternoon_in'];
    $afternoon_out = $_POST['afternoon_out'] === '' ? null : $_POST['afternoon_out'];



    $stmt = $pdo->prepare("
        UPDATE attendance 
        SET
            morning_in = :morning_in,
            morning_out = :morning_out,
            afternoon_in = :afternoon_in,
            afternoon_out = :afternoon_out
        WHERE
            event = :event_id AND user = :user_id
    ");

    $stmt->execute([
        ':morning_in' => $morning_in,
        ':morning_out' => $morning_out,
        ':afternoon_in' => $afternoon_in,
        ':afternoon_out' => $afternoon_out,
        ':event_id' => $event_id,
        ':user_id' => $user_id
    ]);
    echo "row_{$user_id}_{$event_id}";
}



