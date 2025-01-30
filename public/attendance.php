<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true || !($_SESSION['is_officer'] == 1 || $_SESSION['is_superuser'] == 1 || $_SESSION['is_admin'] == 1)) {
    header("Location: index.php");
} else {
    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard -
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
        <link rel="stylesheet" href="./assets/css/output.css?v=1.3">
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

    function getTooltip($time)
    {
        if ($time === '00:00:00')
            return 'Absent';
        if ($time === '11:11:11')
            return 'Excused';
        if (!$time)
            return 'No attendance';
        return $time;
    }

    $fields = ['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'];

    ?>

    <body class="flex justify-center w-screen min-h-screen mt-24 overflow-x-hidden">
        <?php if (isset($_GET['student'])) { ?>
            <main class="flex flex-col items-center w-full h-full">



                <div class="w-full overflow-x-hidden overflow-y-auto md:max-w-lg mt-36">
                    <?php
                    $stmt = $pdo->prepare("
                    SELECT 
                            CONCAT(user.f_name, ' ', user.l_name) AS fullname, 
                            e.name,
                            a.morning_in,
                            a.morning_out,
                            a.afternoon_in,
                            a.afternoon_out
                        FROM attendance a 
                        INNER JOIN event e on a.event = e.idevent
                        INNER JOIN user on a.user = user.iduser
                        WHERE user = ? OR student_no = ?
                        ORDER BY e.date;
                    ");
                    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                        $student = $_GET['student'];
                    }
                    $stmt->execute([$student, $student]);
                    $rows = $stmt->fetchall(PDO::FETCH_ASSOC);

                    $stmt2 = $pdo->prepare("
                    SELECT 
                    CONCAT(user.f_name, ' ', user.l_name) AS fullname
                    
                    FROM user
                    WHERE iduser = ?;
                    ");
                    $stmt2->execute([$student]);
                    $result = $stmt2->fetch(PDO::FETCH_ASSOC);



                    if ($rows) {
                        $LogIn = 0;
                        $TotalLog = 0;


                        ?>

                        <h1 class="absolute top-0 left-0 w-full pt-20 pl-4 text-xl text-gray-500"><?= $rows[0]['fullname'] ?>'s
                            attendance</h1>

                        <!-- Points Div -->
                        <div class="absolute top-0 left-0 w-full py-16 pt-32 text-2xl text-center bg-teal-300/50">
                            <h1>
                                <span class="text-5xl font-bold points" id="totalPoints"></span>
                                <span class="text-4xl font-bold points">%</span>
                            </h1>
                            <span class="mr-2 text-base">ali score</span>
                        </div>

                        <!-- Table Div -->
                        <table class="w-full mt-8 text-center border-collapse">
                            <thead class="sticky top-0 bg-white">
                                <tr class="border border-gray-300">
                                    <th class="p-2 text-left" rowspan="2">Event</th>
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
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($rows as $row): ?>
                                    <tr class="border select-none bg-gray-50 ">
                                        <td class="p-2 text-left"><?= htmlspecialchars($row['name']) ?></td>

                                        <?php


                                        foreach ($fields as $field): ?>
                                            <td class="p-2 text-center">
                                                <span class="relative overflow-x-hidden cursor-default select-none group">
                                                    <?php
                                                    if ($row[$field] === '00:00:00') {
                                                        echo '❌';
                                                    } elseif ($row[$field] === '11:11:11') {
                                                        echo '🎉';
                                                    } elseif ($row[$field]) {
                                                        echo '✅';
                                                    } else {
                                                        echo '➖';
                                                    }
                                                    ?>


                                                    <!-- tooltip -->
                                                    <div
                                                        class="select-none absolute left-0 px-2 py-1 text-[10px] text-white transform -translate-x-1/2 bg-gray-800 rounded opacity-0 pointer-events-none bottom-full w-max group-hover:opacity-100">
                                                        <?= getTooltip($row[$field]) ?>
                                                    </div>

                                                </span>
                                            </td>


                                        <?php endforeach; ?>
                                    </tr>
                                    <?php
                                    $TotalLog += $row['morning_in'] !== null ? 1 : 0;
                                    $TotalLog += $row['morning_out'] !== null ? 1 : 0;
                                    $TotalLog += $row['afternoon_in'] !== null ? 1 : 0;
                                    $TotalLog += $row['afternoon_out'] !== null ? 1 : 0;

                                    $LogIn += ($row['morning_in'] !== null && $row['morning_in'] !== '00:00:00') ? 1 : 0;
                                    $LogIn += ($row['morning_out'] !== null && $row['morning_out'] !== '00:00:00') ? 1 : 0;
                                    $LogIn += ($row['afternoon_in'] !== null && $row['afternoon_in'] !== '00:00:00') ? 1 : 0;
                                    $LogIn += ($row['afternoon_out'] !== null && $row['afternoon_out'] !== '00:00:00') ? 1 : 0;
                                    ?>
                                <?php endforeach ?>
                            </tbody>
                        </table>

                    <?php } else { ?>
                        <div class="w-full h-full mt-10 text-center text-gray-500">No attendance recorded</div>
                    <?php } ?>
                </div>
            </main>
        <?php } else if (isset($_GET['event'])) { ?>
                <main class="flex flex-col items-center w-full h-full">



                    <!-- Table Div -->
                    <div class="w-full overflow-x-hidden overflow-y-auto md:max-w-lg">
                        <?php
                        $stmt = $pdo->prepare("
                        SELECT 
                                CONCAT(user.l_name, ', ', user.f_name) AS fullname, 
                                e.name AS event_name,
                                a.morning_in,
                                a.morning_out,
                                a.afternoon_in,
                                a.afternoon_out
                            FROM attendance a 
                            INNER JOIN event e on a.event = e.idevent
                            INNER JOIN user on a.user = user.iduser
                            WHERE e.name = ? OR event = ?
                            ORDER BY user.l_name;
                        ");
                        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                            $event = $_GET['event'];
                        }
                        $stmt->execute([$event, $event]);
                        $rows = $stmt->fetchall(PDO::FETCH_ASSOC);


                        if ($rows) {
                            $LogIn = 0;
                            $TotalLog = 0;


                            ?>

                            <h2 class="w-full p-4 mt-3 text-xl font-bold text-center "  ><span class="text-teal-600">EVENT:</span> 
                            <?= $rows[0]['event_name'] ?>
                            </h2>

                            <table class="w-full mt-3 text-center border-collapse">
                                <thead class="sticky top-0 bg-white">

                                    <tr class="border border-gray-300">
                                        <th class="p-2 text-left" rowspan="2">Name</th>
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
                                <tbody class="divide-y divide-gray-200">
                                <?php foreach ($rows as $row): ?>
                                        <tr class="border select-none bg-gray-50 ">
                                            <td class="p-2 text-left"><?= htmlspecialchars($row['fullname']) ?></td>

                                            <?php


                                            foreach ($fields as $field): ?>
                                                <td class="p-2 text-center">
                                                    <span class="relative overflow-x-hidden cursor-default select-none group">
                                                        <?php
                                                        if ($row[$field] === '00:00:00') {
                                                            echo '❌';
                                                        } elseif ($row[$field] === '11:11:11') {
                                                            echo '🎉';
                                                        } elseif ($row[$field]) {
                                                            echo '✅';
                                                        } else {
                                                            echo '➖';
                                                        }
                                                        ?>


                                                        <!-- tooltip -->
                                                        <div
                                                            class="select-none absolute left-0 px-2 py-1 text-[10px] text-white transform -translate-x-1/2 bg-gray-800 rounded opacity-0 pointer-events-none bottom-full w-max group-hover:opacity-100">
                                                        <?= getTooltip($row[$field]) ?>
                                                        </div>

                                                    </span>
                                                </td>


                                        <?php endforeach; ?>
                                        </tr>

                                <?php endforeach ?>
                                </tbody>
                            </table>

                    <?php } else { ?>
                            <div class="w-full h-full mt-10 text-center text-gray-500">No attendance recorded</div>
                    <?php } ?>
                    </div>
                </main>
        <?php } ?>



    </body>

    <script>
        function getScorePercentage() {
            var logIn = <?= $LogIn ?>;
            var totalLog = <?= $TotalLog ?>;

            var totalPoints = (logIn / totalLog * 100).toFixed(2);

            $("#totalPoints").text(totalPoints);

            if (totalPoints < 100) {
                $(".points").removeClass("text-green-500").addClass("text-red-500");
            } else {
                $(".points").removeClass("text-red-500").addClass("text-green-500");
            }
        }

        function changeHeaderTitle() {
            $('#header_title').text('Dashboard');
        }

        $(document).ready(function () {
            changeHeaderTitle();
            getScorePercentage();

        });
    </script>



    </html>
<?php } ?>