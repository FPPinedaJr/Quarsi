<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {
    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Attendance -
            <?php
            if ($_SESSION['is_officer'] == 1) {
                echo "Officer";
            } elseif ($_SESSION['is_superuser'] == 1) {
                echo "President";
            } elseif ($_SESSION['is_admin'] == 1) {
                echo "Administrator";
            } else {
                echo "Student";
            }
            ?>
        </title>

        <link rel="icon" href="./assets/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
        <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
        <link rel="stylesheet" href="./assets/css/output.css?v=1.1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
            rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
            rel="stylesheet">
        <script src="./assets/js/jquery-3.7.1.min.js"></script>
    </head>
    <?php
    include_once("./includes/partial/sidebar.php");
    include_once("./includes/connect_db.php");
    include_once("./includes/partial/header.php");
    ?>

    <body class="flex justify-center w-screen min-h-screen mt-24 overflow-x-hidden">
        <main class="flex flex-col items-center w-full h-full ">

            <!-- Table Div -->
            <div class="w-full max-w-sm overflow-y-auto mt-36">
                <?php
                $stmt = $pdo->prepare("
               SELECT 
                    CONCAT(user.f_name, ' ', user.l_name) AS fullname, 
                    e.name,
                    a.morning_in,
                    a.morning_out,
                    a.afternoon_in,
                    a.afternoon_out,
                    a.points
                FROM attendance a 
                INNER JOIN event e on a.event = e.idevent
                INNER JOIN user on a.user = user.iduser
                WHERE user = ?;
            ");

                if ($_SERVER['REQUEST_METHOD'] === 'GET'){
                    $student = $_GET['student'];
                }
                $stmt->execute([$student]);
                $rows = $stmt->fetchall(PDO::FETCH_ASSOC);

                if ($rows) {
                    
                    ?>
                    <table class="w-full text-center border-collapse">
                        <thead class="sticky top-0 text-white bg-gray-500/80">
                            <tr class="text-2xl ">
                                <th colspan="5"><?= $rows[0]['fullname'] ?></th>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="p-2 text-xl text-left" rowspan="2">Event</th>
                                <th class="p-2 border-l border-r border-gray-300" colspan="2">Morning</th>
                                <th class="p-2 border-l border-r border-gray-300" colspan="2">Afternoon</th>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="p-2 border-l border-r border-gray-300">In</th>
                                <th class="p-2 border-l border-r border-gray-300">Out</th>
                                <th class="p-2 border-l border-r border-gray-300">In</th>
                                <th class="p-2 border-l border-r border-gray-300">Out</th>
                            </tr>
                        </thead>
                        <tbody class="border-b divide-y divide-gray-200">
                        <?php foreach ($rows as $row): ?>
                            <tr class="border bg-pink-50">
                                <td class="p-2 text-left border-r border-gray-200"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-2 text-center border-r border-gray-200">
                                    <?= $row['morning_in'] === '00:00:00' ? '❌' : ($row['morning_in'] ? '✅' : '➖') ?></td>
                                <td class="p-2 text-center border-r border-gray-200">
                                    <?= $row['morning_out'] === '00:00:00' ? '❌' : ($row['morning_out'] ? '✅' : '➖') ?></td>
                                <td class="p-2 text-center border-r border-gray-200">
                                    <?= $row['afternoon_in'] === '00:00:00' ? '❌' : ($row['afternoon_in'] ? '✅' : '➖') ?></td>
                                <td class="p-2 text-center">
                                    <?= $row['afternoon_out'] === '00:00:00' ? '❌' : ($row['afternoon_out'] ? '✅' : '➖') ?></td>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>

                <?php } else { ?>
                    <div class="w-full h-full mt-10 text-center text-gray-500">No attendance recorded</div>
                <?php } ?>
            </div>
        </main>
    </body>

    <script>
        function changeHeaderTitle() {
            $('#header_title').text('Attendance');
        }

        $(document).ready(function () {
            changeHeaderTitle();
        });
    </script>



    </html>
<?php } ?>