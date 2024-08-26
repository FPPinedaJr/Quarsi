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
    <title>QR Scanner - <?php echo $_SESSION['username']; ?></title>

        <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
        <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
        <link rel="stylesheet" href="./assets/css/output.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
            rel="stylesheet">
        <script src="./assets/js/jquery-3.7.1.min.js"></script>
        <script src="./assets/js/html5-qrcode.min.js"></script>

</head>
<?php
include_once("./includes/partial/sidebar.php");
include_once("./includes/partial/header.php");

?>




<body class="flex justify-center w-screen min-h-screen mt-24 overflow-hidden">
    <main class="flex justify-center w-full h-full ">
        <div class="w-full max-w-sm p-5 ">
            <h1 class="mb-4 text-2xl font-bold text-center">QR Scanner</h1>
            <div id="qr-reader" class="border border-gray-300 rounded-lg "></div>
            <div id="qr-reader-results" class="mt-4 text-lg text-center">[result here]</div>
        </div>

    </main>
</body>

<script>
    function changeHeaderTitle() {
        $('#header_title').text('QR Scanner');
    }

    function onScanSuccess(decodedText, decodedResult) {
        qrResult.append(`<p>Scanned Code: ${decodedText}</p>`);
        alert(decodedText);
        $('#qr-reader-results').text(decodedText);
    }

    function onScanError(errorMessage) {
        console.warn(`QR Scan error: ${errorMessage}`);
    }


    
    $(document).ready(function () {
        changeHeaderTitle();
        
        const qrResult = $("#qr-reader-results");
        const config = {
            fps: 30,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
            disableFlip: false
        };

            const qrScanner = new Html5Qrcode("qr-reader");

            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras[0].id;
                    qrScanner.start(
                        cameraId,
                        config,
                        onScanSuccess,
                        onScanError
                    );
                } else {
                    qrResult.text("No camera found.");
                }
            }).catch(err => {
                qrResult.text(`Error getting cameras: ${err}`);
            });
        });
    </script>


    </html>
<?php } ?>