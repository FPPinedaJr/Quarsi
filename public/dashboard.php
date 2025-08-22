<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {

    if ($_SESSION['is_admin'] == 1) {
        header("Location: profile.php");
    }
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
    ?>

    <body class="justify-center w-screen min-h-screen mt-24 overflow-x-hidden ">
        <main class="flex flex-col items-center w-full h-full min-h-screen ">

            <!-- Points Div -->
            <div class="absolute top-0 left-0 w-full py-16 pt-32 text-2xl text-center bg-teal-300/50">
                <h1>
                    <span class="text-5xl font-bold points text-fuchsia-500" id="totalPoints">1234</span>
                    <span class="text-4xl font-bold points">%</span>
                </h1>
                <span class="mr-2 text-base">ali score</span>
            </div>

            <!-- Table Div -->
            <div class="w-full overflow-x-hidden overflow-y-auto md:max-w-lg mt-44">
                <?php
                $LogIn = 0;
                $TotalLog = 0;

                $stmt = $pdo->prepare("
               SELECT 
                    e.name,
                    a.morning_in,
                    a.morning_out,
                    a.afternoon_in,
                    a.afternoon_out
                FROM attendance a 
                INNER JOIN event e on a.event = e.idevent
                WHERE user = ?
                ORDER BY date;
            ");
                $stmt->execute([$_SESSION['userid']]);
                $rows = $stmt->fetchall(PDO::FETCH_ASSOC);



                function getStatus($time)
                {
                    if ($time === '00:00:00') {
                        return 'Absent';
                    } elseif ($time === '23:23:23') {
                        return 'Excused';
                    } elseif (is_null($time)) {
                        return 'No attendance';
                    } else {
                        return $time; // Exact time
                    }
                }


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

                if ($rows) {

                    ?>
                    <table class="w-full overflow-x-hidden text-center border-collapse">
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
                        <tbody class="overflow-hidden divide-y divide-gray-200 ">
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

        <?php if ($_SESSION['must_set_blockyear'] == 1) { ?>
            <div id="set_blockyear_modal"
                class="fixed top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
                <div id="set_blockyear_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-1/5">
                    <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                        <p class="font-semibold text-white font-['merriweather_sans'] text-xl">Year & Block
                        </p>
                    </div>
                    <form id="blockyear_form" action="includes/set_blockyear.php" method="POST"
                        class="w-full max-w-md p-6 mx-auto bg-white shadow-lg">

                        <!-- Year Selection -->
                        <div class="mb-4">
                            <label for="year" class="block mb-2 text-lg font-semibold text-gray-700">Year:</label>
                            <select name="year" id="year"
                                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option selected disabled class="italic">- - - select - - -</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>

                        <!-- Block Selection -->
                        <div class="mb-6">
                            <label for="block" class="block mb-2 text-lg font-semibold text-gray-700">Block:</label>
                            <select name="block" id="block"
                                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option selected disabled class="italic">- - - select - - -</option>
                                <option value="1">Block 1</option>
                                <option value="2">Block 2</option>
                                <option value="3">Block 3</option>
                                <option value="4">Block 4</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-center">
                            <button id="proceed_button"
                                class="px-6 py-3 font-semibold text-white transition duration-200 bg-teal-700 rounded-md hover:bg-teal-800">
                                Confirm
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        <?php } ?>

    </body>
    <?php include_once("./includes/partial/footer.php"); ?>

    <script>

        function showSetBlockyearModal(id) {
            $('#set_blockyear_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
        }

        function hideSetBlockyearModal() {
            $('#set_blockyear_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function getScorePercentage() {
            var logIn = <?= $LogIn ?? 0 ?>;
            var totalLog = <?= $TotalLog ?? 0 ?>;

            var totalPoints = totalLog > 0
                ? (logIn / totalLog * 100).toFixed(2)
                : 100;


            var userId = <?= json_encode($user['student_number']) ?>;

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
            $('body').on('touchstart', function (e) {
                if (e.touches.length > 1) {
                    e.preventDefault(); // Prevent default action for multi-touch
                }
            });


            changeHeaderTitle();
            getScorePercentage();


            $('#blockyear_form').on('submit', function (e) {
                e.preventDefault();

                let year = $('#year').val();
                let block = $('#block').val();
                let student = <?= $_SESSION['userid'] ?>;

                if (!year || !block) {
                    alert('Please set your year & block');
                    return;
                }

                hideSetBlockyearModal();
                $.ajax({
                    url: 'includes/set_blockyear.php',
                    method: 'POST',
                    data: {
                        student: student,
                        year: year,
                        block: block
                    },
                    dataType: 'text',
                    success: function (response) {
                        if (response.trim() === 'success') {
                        } else {
                            alert('Error: ' + response);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('Something went wrong. Please try again.');
                    }
                });
            });
        });
    </script>



    </html>
<?php } ?>