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

<header
    class="w-full h-16 bg-gradient-to-r from-[#ecd894] via-[#ffffff] to-[#499667] fixed top-0 left-0 right-0 p-2 flex justify-between align-center shadow-md z-20">
    <div class="flex items-center w-full min-h-full px-2 py-1 my-auto md:w-3/12 md:px-4 md:text-center">
        <a onclick="toggleSidebar()"
            class="md:mr-5 text-2xl md:text-4xl md:text-center hover:text-[#6a6b3a] cursor-pointer">
           
            <i class="fa fa-bars" aria-hidden="true"></i></a>
        <div class="flex justify-start pl-4 text-center min-w-40 md:w-96 md:ml-8 md:mr-2">
            <h1 class="font-['merriweather_sans'] text-[#000000d5] font-bold text-xl md:text-3xl my-auto">Students
            </h1>
        </div>
    </div>
</header>

<body class="flex justify-center w-screen min-h-screen mt-20 overflow-hidden">
    <main class="flex flex-col justify-center w-full h-full px-3 py-2">
        <!-- Search bar -->
        <div class="flex w-full h-10 mb-4 border border-gray-600 rounded-md md:w-[15rem]">
            <input class="flex h-full w-full align-center text-start pl-2 text-['mulish'] bg-white rounded-md focus:outline-none" placeholder="Find student..."> 
        </div>

        <!-- Filter -->
        <div class="flex w-full gap-2 mb-2 h-fit">
            <div class="flex items-start justify-center w-20 p-1 text-lg text-white bg-teal-700 rounded-sm h-fit">Year</div>
            <div class="flex items-center justify-center w-20 p-1 text-lg text-white bg-teal-700 rounded-sm h-fit">Block</div>
        </div>

        <div class="my-2 border-t-2 border-zinc-500"></div>
        <!-- Students List -->
        
        <div class="flex-col hidden w-full gap-2 mt-2 bg-white h-fit md:justify-center md:items-center md:flex">
            <div id="" class="relative flex flex-col w-full md:w-3/4 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] h-fit md:flex-row md:h-10">
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/4 md:h-full md:px-1 md:border-r-2 md:border-[#b7b9b9]">
                    Student Name
                </div>
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/4 md:text-[1.3rem] md:px-1 md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9]">
                    Student ID
                </div>
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/4 md:px-1 md:h-full md:text-[1.3rem]">
                    Program, Year & Block
                </div>
                <div class="absolute top-0 flex flex-col justify-center h-full p-1 text-white bg-zinc-600 font-['mulish'] align-center right-1 w-fit md:right-0 md:text-[1.3rem] md:w-1/4 md:h-full md:px-1">
                    <p class="">Points</p>
                </div>
            </div> 

        </div>
        <div class="flex flex-col w-full gap-2 mt-2 bg-white md:mt-0 h-fit md:justify-center md:items-center">
            <div id="" class="relative flex flex-col w-full md:w-3/4 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] hover:bg-[#dde4e2] h-fit cursor-pointer md:flex-row md:h-10">
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/4 md:h-full md:px-1 md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                    Roronoa Zoro
                </div>
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/4 md:text-[1.3rem] md:px-1 md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                    2022-X-XXXX
                </div>
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/4 md:px-1 md:h-full md:text-[1.3rem] md:font-medium">
                    BSCS X Block X
                </div>
                <div class="absolute top-0 flex flex-col justify-center h-full p-1 text-white bg-zinc-600 font-['mulish'] align-center right-1 w-fit md:right-0 md:text-[1.3rem] md:w-1/4 md:h-full md:px-1">
                    <p class="text-lg">0</p>
                    <p class="text-xs md:hidden">Points</p>
                </div>
            </div> 

        </div>  

    </main>

</body>
</html>
