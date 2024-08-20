<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://raw.githubusercontent.com/mebjas/html5-qrcode/master/minified/html5-qrcode.min.js"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="w-full max-w-sm">
        <h1 class="mb-4 text-2xl font-bold text-center">QR Scanner</h1>
        <div id="qr-reader" class="border border-gray-300 rounded-lg"></div>
        <div id="qr-reader-results" class="mt-4 text-lg text-center"></div>
    </div>

    <script>
        $(document).ready(function () {
            try {
                const qrResult = $("#qr-reader-results");

                function onScanSuccess(decodedText, decodedResult) {
                    qrResult.append(`<p>Scanned Code: ${decodedText}</p>`);
                }

                function onScanError(errorMessage) {
                    console.warn(`QR Scan error: ${errorMessage}`);
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
            } catch (error) {
                console.error("An error occurred while initializing the QR scanner:", error);
            }
        });
    </script>

</body>

</html>