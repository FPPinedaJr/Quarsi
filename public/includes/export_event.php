<?php
include_once "../includes/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idevent = $_GET['idevent'];

    $stmt = $pdo->prepare(
        "SELECT 
            user.iduser AS user_id,
            user.l_name AS l_name,
            user.f_name AS f_name,
            user.year AS year,
            attendance.morning_in AS morning_in,
            attendance.morning_out AS morning_out,
            attendance.afternoon_in AS afternoon_in,
            attendance.afternoon_out AS afternoon_out
        FROM event
            INNER JOIN attendance ON event.idevent = attendance.event
            INNER JOIN user ON user.iduser = attendance.user
        WHERE event.idevent = ?
        ORDER BY user.year, user.l_name"
    );

    $stmt->execute([$idevent]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt1 = $pdo->prepare("SELECT name FROM event WHERE idevent = ?");
    $stmt1->execute([$idevent]);
    $event = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($rows) {
        $filename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $event['name']) . "_data.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');

        // Write column headers
        fputcsv($output, array_keys($rows[0]));

        // Write rows
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    } else {
        echo "No data found for this event.";
    }
} else {
    echo "Invalid request.";
}
