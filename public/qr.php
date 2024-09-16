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
        <title>Generate QR - <?php
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
        <link rel="stylesheet" href="./assets/css/output.css?v=1.0">
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
        <main class="flex justify-center w-full h-full">
            <div id="qr-code" class="h-full p-6 text-center w-96">
                <h1 class="mt-5 mb-10 text-2xl font-bold">Your QR Code</h1>
                <?php
                if (isset($user['student_number'])) {
                    $student_num = urlencode($user['student_number']);
                    $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=$student_num&size=300x300";
                    echo "<img id='qrImage' src='$qr_code_url' alt='QR Code' class='mx-auto mb-4'>";
                    echo "<div class='flex items-center justify-center mt-5 mb-10'><div>";

                    $profile_pic_base64 = base64_encode($user['profile_pic']);
                    echo "
                          <div class='w-20 h-20 mt-3 overflow-hidden border border-gray-400 rounded-full'>
                              <img src='data:image/jpeg;base64,$profile_pic_base64' alt='Profile Picture' class='object-cover w-full h-full'>
                          </div>
                    ";

                    echo "</div><div class='ml-4 text-left'>";
                    echo "<p class='text-lg font-bold text-green-800'>" . strtoupper($user['full_name']) . "</p>";
                    echo "<p class='text-sm text-green-800'>" . $user['student_number'] . "</p>";
                    echo "<p class='text-green-800 '>" . $user['section'] . "</p>";
                    echo "</div></div>";
                } else {
                    echo "<p class='text-red-500'>No student number found.</p>";
                }

                ?>
                <button id="downloadQR" class="px-4 py-2 mt-4 text-white bg-blue-500 rounded hover:bg-blue-600">
                    <i class="mr-2 text-xl fa-solid fa-download"></i>Download
                </button>
            </div>
        </main>
    </body>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function changeHeaderTitle() {
            $('#header_title').text('Generate QR');
        }

        $(document).ready(function () {
            changeHeaderTitle();
            $('#qrImage').on('load', function () {
                $('#downloadQR').on('click', function () {
                    $('#downloadQR').addClass('hidden');
                    html2canvas(document.querySelector("#qr-code"), {
                        useCORS: true
                    }).then(canvas => {
                        var link = document.createElement('a');
                        link.href = canvas.toDataURL("image/jpeg", 1.0);
                        link.download = 'qr-code.jpg';
                        link.click();
                    });
                    $('#downloadQR').removeClass('hidden');
                });
            });
        });
    </script>


    </html>
<?php } ?>