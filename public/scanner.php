<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("./includes/connect_db.php");

if (!$_SESSION["logged_in"] || !($_SESSION['is_officer'] == 1 || $_SESSION['is_superuser'] == 1 || $_SESSION['is_admin'] == 1)) {
    header("Location: index.php");
} else {
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student - <?php
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
        <link rel="stylesheet" href="./assets/css/output.css">
        <script src="./assets/js/jquery-3.7.1.min.js"></script>
        <script src="./assets/js/html5-qrcode.min.js"></script>

    </head>


    <?php
    include_once("./includes/partial/sidebar.php");
    include_once("./includes/partial/header.php");
    ?>


    <body class="flex justify-center w-screen min-h-screen mt-24 overflow-x-hidden">
        <main class="flex justify-center w-full h-full ">
            <div class="w-full max-w-sm p-5 ">
                <h1 class="mb-4 text-2xl font-bold text-center">QR Scanner</h1>
                <div class="flex justify-center my-3">
                    <?php
                    $stmt = $pdo->prepare("
                    SELECT 
                    e.idevent,
                    CONCAT(e.name, ' ', 
                        CASE
                            WHEN e.log_time = 0 THEN '(none)'
                            WHEN e.log_time = 1 THEN '(AM - in)'
                            WHEN e.log_time = 2 THEN '(AM - out)'
                            WHEN e.log_time = 3 THEN '(PM - in)'
                            WHEN e.log_time = 4 THEN '(PM - out)'
                        END
                    ) AS name,
                    CASE
                        WHEN e.log_time = 0 THEN 'inactive'
                        WHEN e.log_time = 1 THEN 'morning_in'
                        WHEN e.log_time = 2 THEN 'morning_out'
                        WHEN e.log_time = 3 THEN 'afternoon_in'
                        WHEN e.log_time = 4 THEN 'afternoon_out'
                    END AS time,
                    e.is_active
                    FROM event e;
                ");
                    $stmt->execute();
                    $events = $stmt->fetchall(PDO::FETCH_ASSOC);
                    ?>
                    <select id="event" name="event" type="number" required=""
                        class="w-4/5 flex md:h-9 items-center pl-1 font-['mulish'] rounded-lg text-emerald-700  border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                        <?php
                        if ($events) {
                            foreach ($events as $event):
                                echo '<option value="' . $event['idevent'] . '" data-log-time="' . $event['time'] . '">' . $event['name'] . '</option>';
                            endforeach;
                        }
                        ?>
                    </select>
                </div>
                <div id="qr-reader" class="border border-gray-300 rounded-lg "></div>

                <div id="scan-result"
                    class="flex items-center justify-center invisible py-4 mt-8 mb-10 border-4 border-green-400 rounded-lg bg-slate-200">
                    <!-- load scanned profile here -->
                </div>

                <!-- <button onclick="updateAttendance('2022-8-0110', 1 ,'afternoon_out')">test</button> -->


            </div>
        </main>
    </body>

    <script>
        function changeHeaderTitle() {
            $('#header_title').text('QR scanner');
        }

        function updateAttendance(number, event, time) {
            $.ajax({
                url: './includes/update_attendance.php',
                method: 'POST',
                data: {
                    student_no: number,
                    eventid: event,
                    log_time: time
                },
                success: function (response) {
                    if (response == 'error:unknown_user') {
                        $('#scan-result').html('<p class="px-3 text-center">Error: This student is either not registered or invited.</p>');
                    } else {
                        $('#scan-result').html(response);
                    }
                    $('#scan-result').removeClass('invisible');
                    $('#scan-result').addClass('visible');
                }
            });

        }

        $(document).ready(function () {
            changeHeaderTitle();

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText.length > 15) {
                    decodedText = decodedText.split('~')[0].trim();
                }

                let eventid = $('#event').val();
                let log_time = $('#event option:selected').data('log-time');

                updateAttendance(decodedText, eventid, log_time);

            }


            const config = {
                fps: 30,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                disableFlip: false
            };

            const qrScanner = new Html5Qrcode("qr-reader");

            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    const backCamera = cameras.find(camera => camera.label.toLowerCase().includes('back'));
                    const cameraId = backCamera ? backCamera.id : cameras[0].id;

                    qrScanner.start(
                        cameraId,
                        config,
                        onScanSuccess
                    );
                } else {
                    alert("No camera found.");
                }
            }).catch(err => {
                alert(`Error getting cameras: ${err}`);
            });


        });
    </script>

    </html>
<?php } ?>