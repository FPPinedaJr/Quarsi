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


    $stmt = $pdo->prepare("
        SELECT year, COUNT(*) AS total 
        FROM user
        WHERE is_admin != 1
        GROUP BY year
        ORDER BY year
    ");
    $stmt->execute();
    $yearResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalStmt = $pdo->prepare("SELECT COUNT(*) AS total_students FROM user WHERE is_admin != 1");
    $totalStmt->execute();
    $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total_students'];

    $yearLabels = [
        0 => 'Unknown',
        1 => '1st Year',
        2 => '2nd Year',
        3 => '3rd Year',
        4 => '4th Year',
    ];

    $chartData = [];
    foreach ($yearResults as $row) {
        $year = $row['year'];
        $count = $row['total'];
        $label = $yearLabels[$year] ?? $year;

        $chartData[] = [
            'year' => $label,
            'count' => (int) $count
        ];
    }

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
        <link rel="stylesheet" href="./assets/css/output.css?v=1.3">
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

    <body class="justify-center w-screen min-h-screen mt-24 overflow-x-hidden">

        <div class="flex justify-center w-full h-full p-4">
            <div class="w-5/6 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-bold text-center text-teal-600 mb-4">NUMBER OF STUDENTS</h2>
                <canvas id="yearChart"></canvas>
            </div>
        </div>

        <div class="flex justify-center w-full h-full mt-10">
            <table class="w-5/6 overflow-hidden bg-white rounded-lg shadow-md">
                <thead class="text-white bg-teal-500">
                    <tr>
                        <th class="px-4 py-2 text-center">ATTENDANCE RANGE</th>
                        <th class="px-4 py-2 text-center">COUNT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr class="border-b odd:bg-teal-100/60 even:bg-teal-200/60">
                            <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($row['attendance_range']); ?></td>
                            <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($row['count_in_range']); ?> students
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-center w-full mt-6">
            <button
                class="px-6 py-2 font-semibold text-teal-500 transition-all border border-teal-500 rounded-md md:hover:text-white md:hover:bg-teal-600"
                onclick="showNewSemModal()">
                Start a New Semester
            </button>
        </div>
    </body>
    <?php include_once("./includes/partial/footer.php"); ?>


    <div id="new_semester_modal"
        class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
        <div id="new_semester_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-3/5">
            <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Start a New Semester</p>
            </div>

            <div class="p-6 bg-white">
                <p class="text-lg font-medium text-gray-800">Starting a new semester will do the following:</p>
                <ul class="mt-4 ml-6 text-gray-700 list-disc">
                    <li>Delete all past attendance</li>
                    <li>Delete all past events</li>
                    <li>Select students for the new semester (manually)</li>
                    <li>Delete the students who did not enroll</li>
                </ul>
            </div>

            <div class="flex justify-end p-4 bg-white">
                <button id="proceed_button"
                    class="px-6 py-2 font-semibold text-white bg-teal-700 rounded-md hover:bg-teal-800">Proceed</button>
            </div>
        </div>
    </div>


    <div id="select_student_modal"
        class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
        <div id="select_student_modal_main"
            class="relative flex flex-col w-5/6 p-6 overflow-y-auto bg-white rounded-lg shadow-md h-5/6 md:w-2/5">
            <h2 class="mb-4 text-lg font-semibold text-teal-700">Select Students to Enroll</h2>
            <div>

                <input type="checkbox" id="select_all" class="px-4 py-2 mb-4 font-semibold">
                <label class="italic font-bold text-teal-500">Select All</label>
                <br>
                <input type="checkbox" id="year_increase" class="px-4 py-2 mb-4 font-semibold">
                <label class="italic font-bold text-teal-500">Increase Year Level</label>
            </div>
            <form id="student_form">
                <div id="student_list" class="space-y-2">
                    <?php
                    $last_year = 69;
                    foreach ($students as $student):
                        ?>

                        <?php
                        if ($student['year'] != $last_year) {
                            $last_year = $student['year'];
                            ?>
                            <div class="flex items-center justify-center mt-6 mb-3 space-x-2 text-white bg-teal-500">
                                <h2 class="text-lg font-bold"> - - - <?php echo $yearLabels[$student['year']]; ?> Year - - -</h2>
                            </div>


                        <?php } ?>

                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="<?= $student['iduser'] ?>}" name="<?= $student['fullname'] ?>"
                                value="<?= $student['iduser'] ?>" class="text-teal-700 student_checkbox">
                            <label for="student_<?= $student['iduser'] ?>" class="text-gray-700">
                                <?= $student['fullname'] ?>
                                <span class="invisible text-sm italic text-teal-600 inpl-3 year-increase">
                                    ( <?= $yearLabels[$student['year']] ?> year
                                    to
                                    <?php
                                    if ($student['year'] != 4) {
                                        echo $yearLabels[$student['year'] + 1];
                                    } else {
                                        echo $yearLabels[$student['year']];
                                    }
                                    ?> year )
                                </span></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="mt-4 text-sm text-red-600">Please double-check before proceeding.</p>
                <button type="submit" id="submit_students"
                    class="px-6 py-2 mt-6 font-semibold text-white bg-teal-700 rounded-md hover:bg-teal-800">
                    Submit
                </button>
            </form>
        </div>
    </div>

    <div id="success_modal"
        class="fixed invisible top-0 left-0  right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center">
        <div id="success_modal_main"
            class="relative flex flex-col items-center w-5/6 p-10 bg-white rounded-lg shadow-md md:w-1/3">
            <i class="mb-4 text-6xl text-teal-500 fas fa-check-circle"></i>
            <h3 class="mb-4 text-lg font-semibold text-gray-700">Operation Successful</h3>
            <p class="mb-6 text-center text-gray-600">The selected students have been enrolled successfully.</p>
            <button onclick="hideSuccessModal()"
                class="px-6 py-2 text-sm font-semibold text-teal-600 border border-teal-500 rounded-md hover:text-white hover:bg-teal-600">
                Okay
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        console.log('here');
        function changeHeaderTitle() {
            $('#header_title').text('Statistics');
        }

        function showNewSemModal(id) {
            $('#new_semester_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
        }

        function hideNewSemModal() {
            $('#new_semester_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function showSelectModal(id) {
            $('#select_student_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
        }

        function hideSelectModal() {
            $('#select_student_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function hideSuccessModal() {
            location.reload();
        }

        $(document).ready(function () {
            let selectAllState = false;

            console.log($('#increase_year').val());
            changeHeaderTitle();

            $('#select_all').on('click', function () {
                selectAllState = !selectAllState;
                $('.student_checkbox').prop('checked', selectAllState);
            });

            $('#year_increase').on('click', function () {
                $('.year-increase').toggleClass('invisible');
            });

            $('#student_form').on('submit', function (e) {
                e.preventDefault();

                let selectedStudents = [];
                $('.student_checkbox:checked').each(function () {
                    selectedStudents.push($(this).val());
                });

                if (selectedStudents.length === 0) {
                    alert('Please select at least one student.');
                    return;
                }

                let willIncreaseYear = $('#year_increase').is(':checked') ? 1 : 0;

                showLoader('Starting a new Semester...');
                $.ajax({
                    url: 'includes/end_sem.php',
                    method: 'POST',
                    data: {
                        students: selectedStudents,
                        will_increase_year: willIncreaseYear
                    },
                    success: function (response) {
                        $('#success_modal').removeClass('invisible');
                        hideLoader();
                    }

                });
            });





            $(document).on('click', function (event) {
                if (!$(event.target).closest('#new_semester_modal_main').length && $(event.target).closest('#new_semester_modal').length) {
                    hideNewSemModal();
                }
            })

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#select_student_modal_main').length && $(event.target).closest('#select_student_modal').length) {
                    hideSelectModal();
                }
            })

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#success_modal_main').length && $(event.target).closest('#success_modal').length) {
                    hideSuccessModal();
                }
            })

            $('#proceed_button').on('click', function () {
                hideNewSemModal();
                showSelectModal();
            });




            let chartData = <?php echo json_encode($chartData); ?>;

            let labels = chartData.map(item => item.year);
            let counts = chartData.map(item => item.count);

            let ctx = $('#yearChart')[0].getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Students per Year',
                        data: counts,
                        backgroundColor: '#14b8a6',
                        borderColor: '#14b8a6',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        });
    </script>



    </html>
<?php } ?>