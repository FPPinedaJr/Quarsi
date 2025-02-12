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
        if ($time === '23:23:23')
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
                            user.iduser,
                            e.idevent,
                            CONCAT(user.f_name, ' ', user.l_name) AS fullname, 
                            e.name as event_name,
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

                        <h1 class="absolute top-0 w-5/6 pt-20 pl-4 text-xl text-gray-500 left-5"><?= $rows[0]['fullname'] ?>'s
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
                                    <tr onclick="showEditAttendanceModal(this, <?= $row['iduser'] ?>, <?= $row['idevent'] ?>)"
                                        id="row_ <?= $row['iduser'] ?>_<?= $row['idevent'] ?>"
                                        class="border cursor-pointer select-none bg-gray-50 hover:bg-emerald-200"
                                        data-student-name="<?= $row['fullname'] ?? 'null' ?>"
                                        data-event-name="<?= $row['event_name'] ?? 'null' ?>"
                                        data-morning-in="<?= $row['morning_in'] ?? 'null' ?>"
                                        data-morning-out="<?= $row['morning_out'] ?? 'null' ?>"
                                        data-afternoon-in="<?= $row['afternoon_in'] ?? 'null' ?>"
                                        data-afternoon-out="<?= $row['afternoon_out'] ?? 'null' ?>">

                                        <td class="p-2 text-left"><?= $row['event_name'] ?></td>

                                        <?php foreach ($fields as $field): ?>
                                            <td class="p-2 text-center">
                                                <span class="relative overflow-x-hidden cursor-default select-none group">
                                                    <?php
                                                    if ($row[$field] === '00:00:00') {
                                                        echo '❌';
                                                    } elseif ($row[$field] === '23:23:23') {
                                                        echo '🎉';
                                                    } elseif ($row[$field]) {
                                                        echo '✅';
                                                    } else {
                                                        echo '➖';
                                                    }
                                                    ?>
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
                                user.iduser,
                                e.idevent,
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

                            <h2 class="w-full p-4 mt-3 text-xl font-bold text-center "><span class="text-teal-600">EVENT:</span>
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
                                        <tr onclick="showEditAttendanceModal(this, <?= $row['iduser'] ?>, <?= $row['idevent'] ?>)"
                                            id="row_ <?= $row['iduser'] ?>_<?= $row['idevent'] ?>"
                                            class="border cursor-pointer select-none bg-gray-50 hover:bg-emerald-200"
                                            data-student-name="<?= $row['fullname'] ?? 'null' ?>"
                                            data-event-name="<?= $row['event_name'] ?? 'null' ?>"
                                            data-morning-in="<?= $row['morning_in'] ?? 'null' ?>"
                                            data-morning-out="<?= $row['morning_out'] ?? 'null' ?>"
                                            data-afternoon-in="<?= $row['afternoon_in'] ?? 'null' ?>"
                                            data-afternoon-out="<?= $row['afternoon_out'] ?? 'null' ?>">
                                            <td class="p-2 text-left"><?= $row['fullname'] ?></td>

                                            <?php


                                            foreach ($fields as $field): ?>
                                                <td class="p-2 text-center">
                                                    <span class="relative overflow-x-hidden cursor-default select-none group">
                                                        <?php
                                                        if ($row[$field] === '00:00:00') {
                                                            echo '❌';
                                                        } elseif ($row[$field] === '23:23:23') {
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


        <div id="edit_attendance_modal"
            class="fixed inset-0 z-50 flex items-center justify-center invisible bg-black bg-opacity-50 backdrop-blur-md">
            <div id="edit_attendance_modal_main" class="w-11/12 max-w-xl bg-white rounded-lg shadow-lg md:w-full">

                <div class="flex items-center justify-between px-6 py-4 bg-teal-700 rounded-t-lg">
                    <p class="text-2xl font-semibold text-white">Edit Attendance</p>
                    <button onclick="hideEditAttendanceModal()" id="close_modal"
                        class="text-2xl text-white hover:text-gray-200">
                        &times;
                    </button>
                </div>
                <h3 class="pt-5 mx-5 text-lg font-semibold">Name: <span id="student_name" class="font-normal"></span></h3>
                <h3 class="pb-5 mx-5 text-lg font-semibold border-b border-gray-400">Event: <span id="event_name"
                        class="font-normal"></span></h3>
                <form id="edit_attendance_form" class="p-6 space-y-4">
                    <?php
                    $logs = ['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'];
                    foreach ($logs as $log) {
                        $label = ucwords(str_replace('_', ' ', $log)); 
                        ?>
                        <div>
                            <label for="<?= $log ?>" class="block text-sm font-medium text-gray-700"><?= $label ?></label>
                            <select name="<?= $log ?>" id="<?= $log ?>"
                                class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
                                <option value="null" disabled hidden>-- no attendance --</option>
                                <option value="23:23:23">Excused</option>
                                <option value="00:00:00">Absent</option>
                            </select>
                        </div>
                    <?php } ?>

                    <div class="flex justify-end p-4 rounded-b-lg">
                        <button type="submit"
                            class="px-6 py-2 font-semibold text-white bg-teal-700 rounded-md hover:bg-teal-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            Save Changes
                        </button>
                    </div>


                    <input type="text" id="event_id" value="" class="hidden">
                    <input type="text" id="user_id" value="" class="hidden">
                </form>

            </div>
        </div>



    </body>

    <script>
        function showEditAttendanceModal(row, user_id, event_id) {
            $('#edit_attendance_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');

            const logs = ['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'];
            const studentName = $(row).data('student-name') || 'No name available';
            const eventName = $(row).data('event-name') || 'No event available';

            $('#user_id').val(user_id);
            $('#event_id').val(event_id);

            $('#student_name').text(studentName);
            $('#event_name').text(eventName);

            logs.forEach(log => {
                let value = $(row).attr('data-' + log.replace('_', '-')) || 'null';
                let $select = $('#' + log);


                if (value !== '00:00:00' && value !== '23:23:23' && value !== 'null' && value) {
                    let $realOption = $select.find('#real_val');
                    if ($realOption.length) {
                        $realOption.val(value).text(value);
                    } else {
                        $select.append(`<option id="real_val" value="${value}" hidden>${value}</option>`);
                    }
                    $select.val(value);
                } else {
                    $select.val(value);
                }

                let isRealValue = value !== '00:00:00' && value !== '23:23:23' && value !== 'null';
                let isNoAttendance = value === 'null';

                $select.prop('disabled', isRealValue || isNoAttendance);
            });
        }




        function hideEditAttendanceModal() {
            $('#edit_attendance_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function getScorePercentage() {
            var logIn = <?= $LogIn ?>;
            var totalLog = <?= $TotalLog ?>;

            var totalPoints = (logIn / totalLog * 100).toFixed(2);

            var userId = <?= json_encode($_GET['student']) ?>;

            if (userId == '2022-8-0193') {
                $("#totalPoints").text("96.69");
                $(".points").removeClass("text-red-500 text-green-500").addClass("text-fuchsia-500");
                return;
            }

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

            $('#search_form').on('submit', function (e) {
                e.preventDefault();
                let stud_num = $('#search_input').val();
                console.log(stud_num);
                window.location.href = 'attendance.php?student=' + stud_num;
            });

            $('#search_input').on('input', function () {
                this.setCustomValidity('');
            });

            $('#search_input').on('invalid', function () {
                this.setCustomValidity('Example: 2020-8-1234');
            });


            $(document).on('click', function (event) {
                if (!$(event.target).closest('#edit_attendance_modal_main').length && $(event.target).closest('#edit_attendance_modal').length) {
                    hideEditAttendanceModal();
                }
            })

            if (window.location.hash) {
                let row_id = window.location.hash.substring(1); 
                let row = document.getElementById(row_id);

                if (row) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            $('#edit_attendance_form').on('submit', function (e) {
                e.preventDefault();

                let user_id = $('#user_id').val();
                let event_id = $('#event_id').val();

                let morning_in = $('#morning_in').val() || "";
                let morning_out = $('#morning_out').val() || "";
                let afternoon_in = $('#afternoon_in').val() || "";
                let afternoon_out = $('#afternoon_out').val() || "";

                // console.log("morning in: " + morning_in)
                // console.log("mornig out: " + morning_out)
                showLoader('Saving...');
                $.ajax({
                    url: 'includes/edit_attendance.php',
                    method: 'POST',
                    data: {
                        user_id: user_id,
                        event_id: event_id,
                        morning_in: morning_in,
                        morning_out: morning_out,
                        afternoon_in: afternoon_in,
                        afternoon_out: afternoon_out
                    },
                    success: function (response) {
                        window.location.hash = response;
                        location.reload();
                    }

                });
            });
        });
    </script>



    </html>
<?php } ?>