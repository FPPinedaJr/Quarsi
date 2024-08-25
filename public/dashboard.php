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
        <h1 class="mt-5 mb-10 text-2xl font-bold">Points</h1>
        <div id="qr-code" class="h-full p-6 text-center w-96">
          
        </div>
    </main>
</body>




</html>