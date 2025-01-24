<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {
    include_once("./includes/connect_db.php");
    $query = "SELECT 
                CASE
                    WHEN attendance_percentage = 100 THEN 'PERFECT'
                    WHEN attendance_percentage >= 90 THEN '90-99'
                    WHEN attendance_percentage >= 80 THEN '80-89'
                    WHEN attendance_percentage >= 70 THEN '70-79'
                    WHEN attendance_percentage >= 60 THEN '60-69'
                    WHEN attendance_percentage >= 50 THEN '50-59'
                    WHEN attendance_percentage >= 40 THEN '40-49'
                    WHEN attendance_percentage >= 30 THEN '30-39'
                    WHEN attendance_percentage >= 20 THEN '20-29'
                    WHEN attendance_percentage >= 10 THEN '10-19'
                    ELSE '0-9'
                END AS attendance_range,
                COUNT(*) AS count_in_range
            FROM (
                SELECT 
                    SUM(
                        (CASE WHEN a.morning_in != '00:00:00' AND a.morning_in IS NOT NULL THEN 1 ELSE 0 END) +
                        (CASE WHEN a.morning_out != '00:00:00' AND a.morning_out IS NOT NULL THEN 1 ELSE 0 END) +
                        (CASE WHEN a.afternoon_in != '00:00:00' AND a.afternoon_in IS NOT NULL THEN 1 ELSE 0 END) +
                        (CASE WHEN a.afternoon_out != '00:00:00' AND a.afternoon_out IS NOT NULL THEN 1 ELSE 0 END)
                    ) * 100.0 /
                    SUM(
                        (CASE WHEN a.morning_in IS NOT NULL THEN 1 ELSE 0 END) +
                        (CASE WHEN a.morning_out IS NOT NULL THEN 1 ELSE 0 END) +
                        (CASE WHEN a.afternoon_in IS NOT NULL THEN 1 ELSE 0 END) +
                        (CASE WHEN a.afternoon_out IS NOT NULL THEN 1 ELSE 0 END)
                    ) AS attendance_percentage
                FROM attendance a
                INNER JOIN user u ON a.user = u.iduser
                GROUP BY a.user
            ) AS subquery
            GROUP BY attendance_range
            ORDER BY attendance_range DESC;";

    $stmt = $pdo->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Statistics - <?php
        if ($_SESSION['is_officer'] == 1) {
            echo "Officer";
        } elseif ($_SESSION['is_superuser'] == 1) {
            echo "President";
        } elseif ($_SESSION['is_admin'] == 1) {
            echo "Administrator";
        } else {
            echo "Student";
        }
        ?></title>

        <link rel="icon" href="./assets/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
        <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
        <link rel="stylesheet" href="./assets/css/output.css?v=1.2">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
            rel="stylesheet">
        <script src="./assets/js/jquery-3.7.1.min.js"></script>
    </head>
    <?php
    include_once("./includes/partial/sidebar.php");
    include_once("./includes/partial/header.php");
    ?>

    <body class="flex justify-center w-screen min-h-screen mt-24 overflow-x-hidden">
        <main class="flex justify-center w-full h-full ">
            <table class="w-5/6 overflow-hidden bg-white rounded-lg shadow-md ">
                <thead class="text-white bg-teal-500">
                    <tr>
                        <th class="px-4 py-2 text-center">Attendance Range</th>
                        <th class="px-4 py-2 text-center">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr class="border-b odd:bg-teal-100/60 even:bg-teal-200/60">
                            <td class="px-4 py-2 text-center"> <?php echo htmlspecialchars($row['attendance_range']); ?> </td>
                            <td class="px-4 py-2 mr-4 text-center"> <?php echo htmlspecialchars($row['count_in_range']); ?>
                                students</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>

    </body>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function changeHeaderTitle() {
            $('#header_title').text('Statistics');
        }

        $(document).ready(function () {
            changeHeaderTitle();


        });
    </script>



    </html>
<?php } ?>