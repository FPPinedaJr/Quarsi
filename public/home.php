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
    <title>Document</title>

    <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
    <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="./assets/css/output.css">
    <script src="./assets/js/jquery-3.7.1.min.js"></script>
</head>
<?php
include_once ("./includes/partial/sidebar.php");

?>

<header
    class="w-full h-16 bg-gradient-to-r from-[#ecd894] via-[#ffffff] to-[#499667] fixed top-0 left-0 right-0 p-2 flex justify-between align-center shadow-md z-20">
    <div class="flex items-center w-full min-h-full px-2 py-1 my-auto md:w-3/12 md:px-4 md:text-center">
        <a onclick="toggleSidebar()"
            class="md:mr-5 text-2xl md:text-4xl md:text-center hover:text-[#6a6b3a] cursor-pointer">
           
            <i class="fa fa-bars" aria-hidden="true"></i></a>
        <div class="flex justify-start pl-4 text-center min-w-40 md:w-96 md:ml-8 md:mr-2">
            <h1 class="font-['merriweather_sans'] text-[#000000d5] font-bold text-xl md:text-3xl my-auto">Manage Cards
            </h1>
        </div>
    </div>



</header>

<body>
    <h1>WELCOME TO ADMIN SIDE!</h1>
</body>

</html>