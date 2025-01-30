<?php
session_start();
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    print_r($_POST);
    $id = $_POST['id'];
    $checkbox = $_POST['checkbox'];
    $state = $_POST['state'];
    $null = NULL;
    $def = "00:00:00";

    if ($checkbox == 1) {
        if ($state == 1) {
            $morning_in = $def;
        } else {
            $morning_in = $null;
        }
        $stmt1 = $pdo->prepare("UPDATE event SET morning_in = :state WHERE idevent = :id");
        $stmt1->execute([
            ':state' => $state,
            ':id'=> $id
        ]);
        $stmt2 = $pdo->prepare('UPDATE attendance SET morning_in = :morning_in WHERE event = :id');
        $stmt2->execute([
            ':morning_in' => $morning_in,
            ':id'=> $id
        ]);

    } else if ($checkbox == 2) {
        if ($state == 1) {
            $morning_out = $def;
        } else {
            $morning_out = $null;
        }
        $stmt1 = $pdo->prepare("UPDATE event SET morning_out = :state WHERE idevent = :id");
        $stmt1->execute([
            ':state' => $state,
            ':id'=> $id
        ]);
        $stmt2 = $pdo->prepare('UPDATE attendance SET morning_out = :morning_out WHERE event = :id');
        $stmt2->execute([
            ':morning_out' => $morning_out,
            ':id'=> $id
        ]);
    } else if ($checkbox == 3) {
        if ($state == 1) {
            $afternoon_in = $def;
        } else {
            $afternoon_in = $null;
        }
        $stmt1 = $pdo->prepare("UPDATE event SET afternoon_in = :state WHERE idevent = :id");
        $stmt1->execute([
            ':state' => $state,
            ':id'=> $id
        ]);
        $stmt2 = $pdo->prepare('UPDATE attendance SET afternoon_in = :afternoon_in WHERE event = :id');
        $stmt2->execute([
            ':afternoon_in' => $afternoon_in,
            ':id'=> $id
        ]);
    } else if ($checkbox == 4) {
        if ($state == 1) {
            $afternoon_out = $def;
        } else {
            $afternoon_out = $null;
        }
        $stmt1 = $pdo->prepare("UPDATE event SET afternoon_out = :state WHERE idevent = :id");
        $stmt1->execute([
            ':state' => $state,
            ':id'=> $id
        ]);
        $stmt2 = $pdo->prepare('UPDATE attendance SET afternoon_out = :afternoon_out WHERE event = :id');
        $stmt2->execute([
            ':afternoon_out' => $afternoon_out,
            ':id'=> $id
        ]);
    }
}
?>
