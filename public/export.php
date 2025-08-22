<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {
    include_once("./includes/connect_db.php");

    $stmt = $pdo->prepare('SELECT idevent, `name` FROM event');
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <body class="flex justify-center items-center w-screen min-h-screen overflow-x-hidden">

        <!-- Main Div -->

        <div class="w-10/12 md:w-96 h-fit p-4">
            <!-- Event Options -->
            <div class="w-full p-2 rounded-lg shadow-lg shadow-teal-800/40 border border-teal-500/40 flex flex-col">
                <div class="w-full text-center p-1 text-xl font-bold">Select Event</div>
                <div id="events" class="mt-2 w-full p-4">
                    <select name="idevent" id="idevent"
                        class="w-full text-black rounded border border-gray-400 font-semibold p-1">
                        <option value="" class="text-center border-t" disabled selected>--- Select Event ---</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?= $event['idevent'] ?>"><?= $event['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button
                class="mt-10 w-full p-1 rounded-lg text-white font-semibold cursor-pointer bg-teal-700 hover:bg-teal-800"
                onclick="exportEvent()">Export
                Attendance</button>
        </div>


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            function exportEvent() {
                let idevent = $('#idevent').val();
                if (idevent) {
                    window.location.href = 'includes/export_event.php?idevent=' + idevent;
                } else {
                    alert("Please select an event.");
                }
            }

            function changeHeaderTitle() {
                $('#header_title').text('Export Attendance');
            }

            $(document).ready( function () {
                changeHeaderTitle();
            })
        </script>



    </html>
<?php } ?>