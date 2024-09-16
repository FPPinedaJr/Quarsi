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
    ?>

    <?php
    include_once("./includes/partial/header.php");
    ?>

    <body class="flex justify-center w-screen h-screen overflow-x-hidden"> 
        <main class="flex items-center justify-center w-full h-full">
            <div class="items-center p-5 text-lg text-center bg-teal-200 rounded-lg w-fit h-fit">
                <i class="fa-solid fa-bomb text-7xl"></i>
                <p>User already exists.</p>
                <button type="button" onclick="history.back()"
                class="px-2 py-1 mt-3 text-sm font-semibold text-center text-white bg-gray-700 rounded-lg w-fit h-fit">Go back</button>
            </div>
        </main>           
    </body>


    </html>

    <script src="./assets/js/jquery-3.7.1.min.js"></script>
    <script>
        
    </script>
