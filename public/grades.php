<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {
    ?>

    <!DOCTYPE html>
    <html lang="en" class="transition duration-500">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Grades - <?php
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


    <body class="flex flex-col min-h-screen text-gray-800 bg-white">

        <script>
            showLoader("Fetching Grades..."); 
        </script>

        <?php
        function post_api($endpoint, $payload)
        {
            $url = "https://psu-api.palawan.edu.ph/bgs/" . $endpoint;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                return ['status' => 'error', 'message' => "cURL Error: $err"];
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['status' => 'error', 'message' => 'Invalid JSON response from API'];
            }

            return ['status' => 'ok', 'data' => $decoded];
        }




        $studentDetails = null;
        $gradesByTerm = [];


        if (isset($user['student_number'])) {
            $student_no = $user['student_number'];
            $details = post_api("x", ['id' => $student_no]);

            if ($details['status'] === 'ok') {
                $studentDetails = $details['data']['studentDetails'][0] ?? null;
                foreach ($studentDetails['registrations'] as $reg) {
                    $gradeRes = post_api("grades", ['id' => $student_no, 'termid' => $reg['termid']]);
                    if ($gradeRes['status'] === 'ok') {
                        $gradesByTerm[$reg['termid']] = [
                            'term' => $reg,
                            'grades' => $gradeRes['data']['grades'] ?? []
                        ];
                    }
                }
            }
        }
        ?>


        <!-- DESKTOP VIEW -->
        <main class="hidden flex-grow w-full p-6 mt-20 mb-6 bg-white rounded md:block">

            <div id="gradeSliderDesktop" class="relative max-w-4xl mx-auto">
                <div class="flex items-center justify-between mb-4">
                    <button id="prevBtnDesktop" class="text-2xl text-teal-600"><i class="fas fa-chevron-left"></i></button>
                    <span id="termLabelDesktop" class="font-semibold"></span>
                    <button id="nextBtnDesktop" class="text-2xl text-teal-600"><i class="fas fa-chevron-right"></i></button>
                </div>

                <?php foreach ($gradesByTerm as $i => $entry): ?>
                    <div class="term-card-desktop <?= $i === 0 ? '' : 'hidden' ?>" data-index="<?= $i ?>">
                        <?php if (count($entry['grades']) > 0): ?>
                            <div class="overflow-auto">
                                <table class="w-full text-sm border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="p-2 border">Subject Code</th>
                                            <th class="p-2 border">Title</th>
                                            <th class="p-2 border">Midterm</th>
                                            <th class="p-2 border">Final</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($entry['grades'] as $subject): ?>
                                            <tr>
                                                <td class="p-2 border"><?= $subject['subjectcode'] ?></td>
                                                <td class="p-2 border"><?= $subject['subjecttitle'] ?></td>
                                                <td class="p-2 border"><?= $subject['midterm'] ?? '' ?></td>
                                                <td class="p-2 border"><?= $subject['final'] ?? '' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500">No grades available.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </main>


        <!-- MOBILE VIEW -->
        <main class="flex-grow w-full max-w-3xl p-6 mx-auto mt-20 mb-6 bg-white rounded md:hidden">

            <div>
                <?php
                $studentNum = $user['student_number']; // e.g. 2022-8-0110
                $entryYear = (int) substr($studentNum, 0, 4);

                usort($gradesByTerm, function ($a, $b) {
                    return strcmp($a['term']['academicyear'], $b['term']['academicyear']) ?: strcmp($a['term']['schoolterm'], $b['term']['schoolterm']);
                });

                foreach ($gradesByTerm as $idx => $entry) {
                    $ay = $entry['term']['academicyear'];
                    $term = ucwords(strtolower($entry['term']['schoolterm']));
                    $diff = ((int) $ay) - $entryYear;

                    switch ($diff) {
                        case 0:
                            $yearLevel = "1st Year";
                            break;
                        case 1:
                            $yearLevel = "2nd Year";
                            break;
                        case 2:
                            $yearLevel = "3rd Year";
                            break;
                        case 3:
                            $yearLevel = "4th Year";
                            break;
                        case 4:
                            $yearLevel = "5th Year";
                            break;
                        default:
                            $yearLevel = "N/A";
                    }

                    $termLabel = "{$yearLevel} - {$term} ({$ay})";
                    $gradesByTerm[$idx]['label'] = $termLabel;
                }
                ?>

                <div id="gradeSlider" class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <button id="prevBtn" class="text-2xl text-teal-600 "><i class="fas fa-chevron-left"></i></button>
                        <span id="termLabel" class="font-semibold"></span>
                        <button id="nextBtn" class="text-2xl text-teal-600 "><i class="fas fa-chevron-right"></i></button>
                    </div>

                    <?php foreach ($gradesByTerm as $i => $entry): ?>
                        <div class="term-card <?= $i === 0 ? '' : 'hidden' ?>" data-index="<?= $i ?>">
                            <?php if (count($entry['grades']) > 0): ?>
                                <div class="space-y-3">
                                    <?php foreach ($entry['grades'] as $subject): ?>
                                        <div class="p-3 border border-gray-300 rounded ">
                                            <div class="text-sm font-semibold"><?= $subject['subjectcode'] ?> -
                                                <?= $subject['subjecttitle'] ?>
                                            </div>
                                            <div class="text-sm">Midterm: <strong><?= $subject['midterm'] ?? '-' ?></strong></div>
                                            <div class="text-sm">Final: <strong><?= $subject['final'] ?? '-' ?></strong></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500">No grades available.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>





        <footer class="p-4 text-xs leading-relaxed text-center text-white bg-teal-600">
            <p class="opacity-90 ">
                Unofficial grade portal using public API. No data is stored or harmed.
            </p>
        </footer>



    </body>
    <script>
        $(function () {
            const cardsDesktop = $('.term-card-desktop');
            const labelDesktop = $('#termLabelDesktop');
            const totalDesktop = cardsDesktop.length;
            let currentDesktop = totalDesktop - 1;

            const updateSliderDesktop = () => {
                cardsDesktop.addClass('hidden');
                $(cardsDesktop[currentDesktop]).removeClass('hidden');
                labelDesktop.text($(cardsDesktop[currentDesktop]).data('label'));
            };

            cardsDesktop.each(function (i) {
                $(this).attr('data-label', <?= json_encode(array_column($gradesByTerm, 'label')) ?>[i]);
            });

            $('#prevBtnDesktop').click(function () {
                if (currentDesktop > 0) {
                    currentDesktop--;
                    updateSliderDesktop();
                }
            });

            $('#nextBtnDesktop').click(function () {
                if (currentDesktop < totalDesktop - 1) {
                    currentDesktop++;
                    updateSliderDesktop();
                }
            });

            updateSliderDesktop();
        });

        $(function () {


            $(function () {

                $('.toggle-btn').click(function () {
                    $(this).next('.grades-content').slideToggle();
                    $(this).find('.caret-icon').toggleClass('rotate-90');
                });

                $('.toggle-btn-mobile').click(function () {
                    $(this).next('.grades-content-mobile').slideToggle();
                    $(this).find('.caret-icon').toggleClass('rotate-180');
                });

                const cards = $('.term-card');
                const label = $('#termLabel');
                const total = cards.length;
                let current = total - 1;

                const updateSlider = () => {
                    cards.addClass('hidden');
                    $(cards[current]).removeClass('hidden');
                    label.text($(cards[current]).data('label'));
                };

                cards.each(function (i) {
                    $(this).attr('data-label', <?= json_encode(array_column($gradesByTerm, 'label')) ?>[i]);
                });

                $('#prevBtn').click(function () {
                    if (current > 0) {
                        current--;
                        updateSlider();
                    }
                });

                $('#nextBtn').click(function () {
                    if (current < total - 1) {
                        current++;
                        updateSlider();
                    }
                });

                updateSlider();
            });

        });


    </script>
    <script>
        function changeHeaderTitle() {
            $('#header_title').text('Grades');
        }


        $(document).ready(function () {
            changeHeaderTitle();
            setTimeout(function () {
                hideLoader();
            }, 1569);
        });
    </script>

    </html>
<?php } ?>