<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - <?php echo $_SESSION['username']; ?></title>

    <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
    <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="./assets/css/output.css">
    <script src="./assets/js/jquery-3.7.1.min.js"></script>
</head>
<?php
include_once ("./includes/partial/sidebar.php");

?>

<?php
include_once("./includes/partial/header.php");
?>

<body class="flex justify-center w-screen min-h-screen mt-24 overflow-hidden">
    <main class="flex justify-center w-full h-full">
        <div id="qr-code" class="h-full p-6 text-center w-96">
            <h1 class="mt-5 mb-10 text-2xl font-bold">Your QR Code</h1>
            <?php
            if (isset($_SESSION['student_number'])) {
                $student_number = urlencode($_SESSION['student_number']);
                $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=$student_number&size=300x300";
                echo "<img id='qrImage' src='$qr_code_url' alt='QR Code' class='mx-auto mb-4'>";
                echo "<p class='mt-5 text-sm text-green-800'>$student_number</p>";
                echo "<p class='text-lg font-bold text-green-800'>" . strtoupper($_SESSION['username']) . "</p>";
                echo "<p class='mb-10 text-green-800'>" . $_SESSION['section'] . "</p>";
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
        $('#header_title').text('Home');
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