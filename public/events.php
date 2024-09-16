<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("./includes/connect_db.php");

if (!$_SESSION["logged_in"] || !($_SESSION['is_superuser'] == 1 || $_SESSION['is_admin'] == 1)) {
    header("Location: index.php");
} else {
    $stmt1 = $pdo->prepare("
    SELECT 
        event.idevent AS 'idevent',
        event.date AS 'date',
        event.name AS 'name',
        organization.short_name AS 'organization',
        event.organization AS 'idorganization',
        event.log_time AS 'log_time',
        event.is_active AS 'is_active',
        event.set_points AS 'set_points'
    FROM event
    INNER JOIN organization
    WHERE organization.idorganization = event.organization
    ");

    $stmt1->execute();
    $events = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("
        SELECT 
            organization.idorganization as 'idorganization',
            organization.name as 'name',
            organization.short_name as 'short_name'
        FROM organization
        ORDER BY organization.name
    ");

    $stmt2->execute();
    $organizations = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $stmt3 = $pdo->prepare("
        SELECT o.short_name as organization, year, block, f_name, l_name, iduser
        FROM user u
        INNER JOIN organization o ON u.organization = o.idorganization
        WHERE is_admin != 1 
        ORDER BY organization, year, block, l_name, f_name

    ");
    $stmt3->execute();
    $students = $stmt3->fetchAll(PDO::FETCH_ASSOC);
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

    <body class="flex justify-center w-screen min-h-screen mt-20 overflow-x-hidden">
        <main class="flex flex-col justify-center w-full px-3 py-2 h-fit">
            <!-- Search bar -->
            <div class="flex w-full h-10 mb-4 border border-gray-600 rounded-md md:w-[15rem]">
                <input id="search_student" name="search_student"
                    class="flex h-full w-full align-center text-start pl-2 text-['mulish'] bg-white rounded-md focus:outline-none"
                    placeholder="Find event...">
            </div>

            <!-- Filter -->
            <div class="flex w-full mb-2 h-fit">
                <div
                    class="flex items-center justify-center w-1/2 p-1 text-lg text-white bg-teal-700 rounded-sm md:w-20 h-fit">
                    Date</div>
            </div>

            <!-- Add Button -->
            <div id="add_event_modal_btn" onclick="showAddEventModal()"
                class="fixed z-20 flex justify-center flex-shrink-0 w-8 h-8 bg-teal-700 border border-white rounded-md cursor-pointer top-4 right-5 md:top-3 md:w-10 md:h-10 hover:bg-teal-600/70">
                <i class="fa-solid fa-plus font-['mulish'] text-white text-xl md:text-3xl"></i>
            </div>

            <div class="my-2 border-t-2 border-zinc-500"></div>
            <!-- Events List -->

            <div id=""
                class="flex-col hidden w-full gap-2 mt-2 bg-white md:flex md:mt-0 h-fit md:justify-center md:items-center">
                <div id=""
                    class="relative flex flex-col w-full md:w-4/5 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] h-fit md:flex-row md:h-10">
                    <div
                        class="flex items-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/6 md:h-full md:px-1 md:border-r-2 md:justify-center md:border-[#b7b9b9] md:font-medium">
                        Event Name
                    </div>
                    <div
                        class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/6 md:text-[1.3rem] md:px-1 md:justify-center md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                        Date
                    </div>
                    <div
                        class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:border-r-2 md:border-[#b7b9b9]">
                        Organization
                    </div>
                    <div
                        class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:border-r-2 md:border-[#b7b9b9]">
                        Set Points
                    </div>
                    <div
                        class="absolute top-0 right-0 flex flex-col items-center justify-center flex-grow-0 flex-shrink-0 w-24 h-full p-1 bg-zinc-600 align-center md:flex-row md:right-0 md:w-1/6 md:h-full md:px-1">
                        <div class="text-white font-['mulish'] text-lg md:text-[1.3rem] ">Status</div>
                        <div class="text-emerald-100 font-['mulish'] text-xs md:hidden">Log Time</div>
                    </div>
                    <div
                        class="hidden md:flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:bg-zinc-600 md:border-r-2 md:border-[#b7b9b9] text-white">
                        Log Time
                    </div>
                </div>
            </div>

            <?php foreach ($events as $event): ?>

                <div id="event-<?= $event['idevent'] ?>" data-name="<?= $event['name'] ?>" data-date="<?= $event['date'] ?>"
                    data-organization="<?= $event['idorganization'] ?>" data-log_time="<?= $event['log_time'] ?>"
                    data-status="<?= $event['is_active'] ?>" data-set_points="<?= $event['set_points'] ?>"
                    class="flex flex-col flex-shrink-0 w-full gap-2 mt-2 bg-white md:mt-0 h-fit md:justify-center md:items-center">
                    <div id="" onclick="showEditEventModal(<?= $event['idevent'] ?>)"
                        class="relative flex flex-col w-full md:w-4/5 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] hover:bg-[#dde4e2e0] h-fit cursor-pointer md:flex-row md:h-10">
                        <div
                            class="flex items-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/6 md:h-full md:px-1 md:border-r-2 md:justify-center md:border-[#b7b9b9] md:font-medium">
                            <?= $event['name'] ?>
                        </div>
                        <div
                            class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/6 md:text-[1.3rem] md:px-1 md:justify-center md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                            <?= $event['date'] ?>
                        </div>
                        <div
                            class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:border-r-2 md:border-[#b7b9b9]">
                            <?= $event['organization'] ?>
                        </div>
                        <div
                            class="flex items-center md:border-r-2 md:border-[#b7b9b9] w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium">
                            <?= $event['set_points'] ?>
                        </div>
                        <div
                            class="absolute top-0 right-0 flex flex-col items-center justify-center flex-grow-0 flex-shrink-0 w-24 h-full font-['mulish'] p-1 bg-zinc-600 align-center md:flex-row md:right-0 md:w-1/6 md:h-full md:px-1">
                            <?php if ($event['is_active'] == 0) {
                                echo '<div class="text-white text-lg md:text-[1.3rem] ">
                                    Inactive
                                </div>';
                            } else if ($event['is_active'] == 1) {
                                echo '<div class="text-emerald-100 text-lg md:text-[1.3rem] ">
                                    Active
                                </div>';
                            }
                            ?>
                            <div class="text-emerald-100 font-['mulish'] text-xs md:hidden">
                                <?php if ($event['log_time'] == 0) {
                                    echo "Disabled";
                                } else if ($event['log_time'] == 1) {
                                    echo "Morning In";
                                } else if ($event['log_time'] == 2) {
                                    echo "Morning Out";
                                } else if ($event['log_time'] == 3) {
                                    echo "Afternoon In";
                                } else if ($event['log_time'] == 4) {
                                    echo "Afternoon Out";
                                }
                                ?>
                            </div>
                        </div>
                        <div
                            class="hidden md:flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:bg-zinc-600 md:border-r-2 md:border-[#b7b9b9] text-white">
                            <?php if ($event['log_time'] == 0) {
                                echo "Disabled";
                            } elseif ($event['log_time'] == 1) {
                                echo "Morning In";
                            } else if ($event['log_time'] == 2) {
                                echo "Morning Out";
                            } else if ($event['log_time'] == 3) {
                                echo "Afternoon In";
                            } else if ($event['log_time'] == 4) {
                                echo "Afternoon Out";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>

        </main>

        <!-- Events Add Modal -->
        <div id="add_event_modal"
            class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
            <div id="add_event_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-3/5">
                <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                    <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Add Event</p>
                </div>

                <!-- fieldset -->
                <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                    <form id="add_event_form" action="./includes/crud_event.php" type="button" method="POST"
                        class="flex flex-col justify-center w-full h-full px-3">

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2 mt-4">
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="add_name" name="name" type="text" required autocomplete="name"
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="add_name" class="pl-1 text-base md:text-lg text-zinc-600">Event Name</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="add_date" name="date" type="date" required
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="add_date" class="pl-1 text-base md:text-lg text-zinc-600">Date</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="add_set_points" name="set_points" type="number" required
                                    class="w-full flex items-center md:h-9 pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="add_set_points" class="pl-1 text-base md:text-lg text-zinc-600">Set
                                    Points</label>
                            </div>
                        </div>

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2">
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="add_organization" name="organization" required autocomplete="off"
                                    class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <?php foreach ($organizations as $organization): ?>
                                        <option value="<?= $organization['idorganization'] ?>"
                                            class="font-['mulish'] text-black text-base w-full"><?= $organization['name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <label for="add_organization"
                                    class="pl-1 text-base md:text-lg text-zinc-600">Organization</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="add_log_time" name="log_time" type="number" required
                                    class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <option value="0">Disabled</option>
                                    <option value="1">Morning In</option>
                                    <option value="2">Morning Out</option>
                                    <option value="3">Afternoon In</option>
                                    <option value="4">Afternoon Out</option>
                                </select>
                                <label for="add_log_time" class="pl-1 text-base md:text-lg text-zinc-600">Log Time</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="add_status" name="status" type="number" required
                                    class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <label for="add_status" class="pl-1 text-base md:text-lg text-zinc-600">Status</label>
                            </div>
                        </div>


                        <div class="flex items-center justify-center w-full gap-2 my-4 md:gap-4 md:flex-row">
                            <button id="add_event_btn" type="submit" name="action" value="add"
                                class="w-full h-10 text-['mulish'] bg-teal-700 hover:bg-teal-600 text-white font-semibold rounded-lg md:w-28">Add
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Events Edit Modal -->
        <div id="edit_event_modal"
            class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
            <div id="edit_event_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-3/5">
                <div class="relative flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                    <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Edit Event</p>
                    <div class="absolute z-30 flex items-center top-2.3 h-fit invite md:top-4 right-11 invite_btn"
                        onclick="showInviteModal()">
                        <i
                            class="text-base text-white cursor-pointer md:text-xl fa-solid fa-user-plus hover:text-emerald-400"></i>
                    </div>
                    <div class="absolute z-30 flex items-center top-2.3 h-fit invite md:top-4 right-4 invite_btn"
                        onclick="showEndModal()">
                        <i
                            class="text-xl text-white cursor-pointer md:text-xl fa-solid fa-calendar-xmark hover:text-emerald-400"></i>
                    </div>
                </div>

                <!-- fieldset -->
                <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                    <form id="edit_event_form" action="./includes/crud_event.php" type="button" method="POST"
                        class="flex flex-col justify-center w-full h-full px-3">

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2 mt-4">
                            <input id="idevent" name="idevent" type="hidden">
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="name" name="name" type="text" required autocomplete="name"
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="name" class="pl-1 text-base md:text-lg text-zinc-600">Event Name</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="date" name="date" type="date" required
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="date" class="pl-1 text-base md:text-lg text-zinc-600">Date</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="set_points" name="set_points" type="number" required
                                    class="w-full flex items-center md:h-9 pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="set_points" class="pl-1 text-base md:text-lg text-zinc-600">Set Points</label>
                            </div>
                        </div>

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2">
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="organization" name="organization" required autocomplete="off"
                                    class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <?php foreach ($organizations as $organization): ?>
                                        <option value="<?= $organization['idorganization'] ?>"
                                            class="font-['mulish'] text-black text-base w-full"><?= $organization['name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <label for="organization"
                                    class="pl-1 text-base md:text-lg text-zinc-600">Organization</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="log_time" name="log_time" type="number" required
                                    class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <option value="0">Disabled</option>
                                    <option value="1">Morning In</option>
                                    <option value="2">Morning Out</option>
                                    <option value="3">Afternoon In</option>
                                    <option value="4">Afternoon Out</option>
                                </select>
                                <label for="log_time" class="pl-1 text-base md:text-lg text-zinc-600">Log Time</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="status" name="status" type="number" required
                                    class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <label for="status" class="pl-1 text-base md:text-lg text-zinc-600">Status</label>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center w-full gap-2 my-4 md:gap-4 md:flex-row">
                            <button id="save_event_btn" type="submit" value="submit" name="action"
                                class="w-full h-10 text-['mulish'] bg-teal-700 hover:bg-teal-600 text-white font-semibold rounded-lg md:w-20">Save
                            </button>
                            <button id="delete_event_btn" type="button" onclick="showDeleteEventModal()"
                                class="w-full h-10 text-['mulish'] bg-red-700 hover:bg-red-600 text-white font-semibold rounded-lg md:w-20">Delete
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="delete_event_modal"
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center invisible w-full h-full overflow-y-hidden backdrop-blur-sm bg-gray-500/30">
            <div id="delete_event_modal_main"
                class="flex-col w-10/12 md:w-96 h-fit p-2 rounded-lg items-center justify-content bg-[#fbfcf8]">
                <div class="flex items-center w-full h-16 px-2 border-b border-emerald-700">
                    <p class="font-['mulish'] text-emerald-700 font-semibold text-xl">Delete Event</p>
                </div>
                <div class="flex flex-col w-full h-auto p-2 text-md">
                    <p class="font-semibold text-emerald-700">This will delete "<span id="event_to_delete"></span>."</p>
                    <p class="text-emerald-700">Are you sure?</p>
                </div>
                <div class="flex flex-col w-full gap-2 p-2 md:flex-row md:mt-5 h-fit">
                    <button id="deleteEventCancel" onclick="hideDeleteEventModal()"
                        class="w-full p-1 border rounded-lg md:w-20 md:ml-auto border-emerald-700 hover:bg-emerald-700 hover:text-white text-md text-emerald-700">Cancel</button>
                    <form action="./includes/crud_event.php" type="button" method="POST">
                        <button type="submit" value="delete" name="action"
                            class="w-full h-full p-1 text-white bg-red-600 rounded-lg md:w-20 md:ml-2 hover:bg-red-700 text-md" >Delete</button>
                        <input id="id_delete_event" type="hidden" name="idevent" class="">
                    </form>
                </div>
            </div>
        </div>


        <!-- End modal -->
        <div id="end_modal"
            class="fixed top-0 left-0 z-50 flex items-center justify-center invisible w-full h-full backdrop-blur-sm bg-[#2e2c2c69]">
            <form id="end_students_form" action="./includes/end_event.php" type="button" method="POST"
                class="w-10/12 md:w-1/3 a-2/3">
                <input id="end_event" type="hidden" name="idEndEvent">
                <div id="end_modal_main" class="w-full h-full overflow-y-auto text-lg bg-white">
                    <div
                        class="w-full flex items-center justify-center font-semibold text-3xl text-white h-16 bg-teal-700 text-['mulish']">
                        End Event
                    </div>

                </div>
                <div class="flex flex-col w-full h-auto p-5 bg-white text-md">
                    <p class="text-emerald-700">Are you sure to end event "<span id="event_to_end" class="font-semibold"></span>"?</p>
                </div>
                <div class="flex items-center justify-center w-full gap-3 py-3 bg-white h-fit">
                    <button type="button" onclick="hideEndModal()"
                    class="rounded-lg hover:bg-teal-600 hover:text-white w-20 p-1 text-base font-semibold text-teal-800 font-['mulish'] border bg-none border-teal-700 cursor-pointer flex justify-center add_invite_btn">Cancel</button>
                    <button type="submit"
                            class="rounded-lg hover:bg-red-600 w-20 p-1 text-base font-semibold text-white font-['mulish'] bg-red-700 cursor-pointer flex justify-center add_invite_btn border border-red-700">End</button>
                </div>
            </form>
        </div>

        <!-- Invite Modal -->
        <div id="invite_modal"
            class="fixed top-0 left-0 z-30 flex items-center justify-center invisible w-full h-full backdrop-blur-sm bg-[#2e2c2c69]">
            <form id="invite_students_form" action="./includes/crud_invite.php" type="button" method="POST"
                class="w-10/12 md:w-1/3 h-2/3">
                <input id="invite_event" type="hidden" name="idevent">
                <div id="invite_modal_main" class="w-full h-full overflow-y-auto text-lg bg-white">
                    <div
                        class="w-full flex items-center justify-center font-semibold text-3xl text-white h-16 bg-teal-700 text-['mulish']">
                        Invite Students
                    </div>
                    <?php
                    $currentProgram = '';
                    $currentYear = '';
                    $currentBlock = '';

                    foreach ($students as $student) {
                        if ($student['organization'] !== $currentProgram) {
                            if ($currentProgram !== '') {
                                echo '</div></div></div>';
                            }
                            $currentProgram = $student['organization'];
                            $currentYear = '';
                            $currentBlock = '';
                            echo '<div class="m-4 program-group">';
                            echo '<label><input type="checkbox" class="program-checkbox"> <span class="font-bold">' . strtoupper(htmlspecialchars($currentProgram)) . '</span></label>';
                            echo '<div class="ml-4">';
                        }

                        if ($student['year'] !== $currentYear) {
                            if ($currentYear !== '') {
                                echo '</div></div></div></div>';
                            }
                            $currentYear = $student['year'];
                            $currentBlock = '';
                            echo '<div class="mb-2 ml-1 md:ml-4 year">';
                            echo '<i class="mr-3 text-teal-700 cursor-pointer fa-solid fa-caret-right year-dropdown"></i><label><input type="checkbox" class="year-checkbox"> <span class="font-semibold">' . htmlspecialchars("Year " . $currentYear) . '</span></label>
                            ';
                            echo '<div class="ml-4 md:ml-8">';
                        }

                        if ($student['block'] !== $currentBlock) {
                            if ($currentBlock !== '') {
                                echo '</div></div>';
                            }
                            $currentBlock = $student['block'];
                            echo '<div class="hidden mb-1 md:hover:text-emerald-600 block-container">';
                            echo ' <i class="ml-2 text-teal-700 cursor-pointer fa-solid fa-caret-right block-dropdown"></i><label class="relative py-1 pl-5"><input type="checkbox" class="block-checkbox"> ' . htmlspecialchars("block " . $currentBlock) . '</label>
                           ';
                            echo '<div class="hidden mt-2 ml-20 border-t border-gray-500 student-container">';
                        }

                        echo '<div class="px-2 md:hover:bg-blue-300 md:hover:text-emerald-800 student">';
                        echo '<label>';
                        echo '<input type="checkbox" name="students[]" value="' . htmlspecialchars($student['iduser']) . '" class="student-checkbox">';
                        echo '<span class="md:ml-1">';
                        echo htmlspecialchars($student['l_name'] . ', ' . $student['f_name']);
                        echo '</span>';
                        echo '</label>';
                        echo '</div>';
                    }

                    if ($currentBlock !== '') {
                        echo '</div>';
                    }
                    if ($currentYear !== '') {
                        echo '</div>';
                    }
                    if ($currentProgram !== '') {
                        echo '</div>';
                    }
                    ?>

                </div>
                
                <div class="flex justify-center w-full my-4 h-fit">
                    <div class="flex flex-wrap gap-6 p-2 w-fit h-fit justify-evenly">
                        <div class="checkbox-wrapper-12">
                            <label class="relative cursor-pointer">
                                <input class="absolute w-0 h-0 overflow-hidden checkbox-input" type="checkbox" name="logtime[]" value=1>
                                <span class="relative flex flex-col items-center justify-center h-12 bg-white border-2 border-gray-300 rounded-md shadow-md w-28 checkbox-tile">
                                <span class="text-sm text-center text-zinc-700 checkbox-label">Morning In</span>
                                </span>
                            </label>
                        </div>
                        <div class="checkbox-wrapper-12">
                            <label class="relative cursor-pointer">
                                <input class="absolute w-0 h-0 overflow-hidden checkbox-input" type="checkbox" name="logtime[]" value=2>
                                <span class="relative flex flex-col items-center justify-center h-12 bg-white border-2 border-gray-300 rounded-md shadow-md w-28 checkbox-tile">
                                <span class="text-sm text-center text-zinc-700 checkbox-label">Morning Out</span>
                                </span>
                            </label>
                        </div>
                        <div class="checkbox-wrapper-12">
                            <label class="relative cursor-pointer">
                                <input class="absolute w-0 h-0 overflow-hidden checkbox-input" type="checkbox" name="logtime[]" value=3>
                                <span class="relative flex flex-col items-center justify-center h-12 bg-white border-2 border-gray-300 rounded-md shadow-md w-28 checkbox-tile">
                                <span class="text-sm text-center text-zinc-700 checkbox-label">Afternoon In</span>
                                </span>
                            </label>
                        </div>
                        <div class="checkbox-wrapper-12">   
                            <label class="relative cursor-pointer">
                                <input class="absolute w-0 h-0 overflow-hidden checkbox-input" type="checkbox" name="logtime[]" value=4>
                                <span class="relative flex flex-col items-center justify-center h-12 bg-white border-2 border-gray-300 rounded-md shadow-md w-28 checkbox-tile">
                                <span class="text-sm text-center text-zinc-700 checkbox-label">Afternoon Out</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                    
                <div class="flex items-center justify-center w-full py-3 bg-white h-fit">
                    <button type="submit" value="invite" name="action"
                    class="rounded-lg hover:bg-teal-600 w-40 p-1 text-xl font-semibold text-white font-['mulish'] bg-teal-700 cursor-pointer flex justify-center add_invite_btn">Add
                    Invite</button>
                </div>
            </form>
        </div>




    </body>


    </html>

    <script src="./assets/js/jquery-3.7.1.min.js"></script>
    <script>
        function showAddEventModal() {
            $('#add_event_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#add_idevent').val('');
            $('#add_name').val('');
            $('#add_date').val('');
            $('#add_organization').val('');
            $('#add_set_points').val('');
            $('#add_log_time').val('');
            $('#add_status').val('');
        }

        function hideAddEventModal() {
            $('#add_event_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function showEditEventModal(id) {
            $('#edit_event_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');

            var $name = $('#event-' + id).data('name');
            var $date = $('#event-' + id).data('date');
            var $set_points = $('#event-' + id).data('set_points');
            var $organization = $('#event-' + id).data('organization');
            var $log_time = $('#event-' + id).data('log_time');
            var $status = $('#event-' + id).data('status');
            var $set_points = $('#event-' + id).data('set_points');

            $('#idevent').val(id);
            $('#date').val($date);
            $('#name').val($name);
            $('#set_points').val($set_points);
            $('#organization').val($organization);
            $('#log_time').val($log_time);
            $('#status').val($status);
        }

        function hideEditEventModal() {
            $('#edit_event_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function showDeleteEventModal() {
            var $id = $('#idevent').val()
            $('#delete_event_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#event_to_delete').text($('#event-' + $id).data('name'));
            $('#id_delete_event').val($id);
        }

        function hideDeleteEventModal() {
            $('#delete_event_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function changeHeaderTitle() {
            $('#header_title').text('Events');
        }

        function showEndModal() {
            var $idevent = $('#idevent').val();
            $('#end_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#end_event').val($idevent); 
            $('#event_to_end').text($('#event-' + $idevent).data('name'));
            $('#edit_event_modal').addClass('invisible');
            $('input[type="checkbox"]').prop('checked', false);
        }


        function hideEndModal() {
            $('#end_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function showEndModal() {
            var $idevent = $('#idevent').val();
            $('#end_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#end_event').val($idevent); 
            $('#event_to_end').text($('#event-' + $idevent).data('name'));
            $('#edit_event_modal').addClass('invisible');
            $('input[type="checkbox"]').prop('checked', false);
        }


        function hideEndModal() {
            $('#end_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function showInviteModal() {
            var $id = $('#idevent').val();
            $('#invite_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#invite_event').val($id);
            $('#edit_event_modal').addClass('invisible');
            $('input[type="checkbox"]').prop('checked', false);
        }

        function hideInviteModal() {
            $('#invite_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        $(document).ready(function () {
            changeHeaderTitle();

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#edit_event_modal_main').length && $(event.target).closest('#edit_event_modal').length) {
                    hideEditEventModal();
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#add_event_modal_main').length && $(event.target).closest('#add_event_modal').length) {
                    hideAddEventModal();
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#delete_event_modal_main').length && $(event.target).closest('#delete_event_modal').length) {
                    hideDeleteEventModal();
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#invite_modal_main').length && $(event.target).closest('#invite_modal').length) {
                    hideInviteModal();
                }
            });


            $(document).on('click', function (event) {
                if (!$(event.target).closest('#end_modal_main').length && $(event.target).closest('#end_modal').length) {
                    hideEndModal();
                }
            })

            // Select/deselect all students within a block when the block checkbox is clicked
            $('.block-checkbox').click(function () {
                var $blockCheckboxes = $(this).closest('.block-container').find('.student-checkbox');
                var $dropdown = $(this).closest('.block-container').find('.block-dropdown');
                var $container = $(this).closest('.block-container').find('.student-container');
                $blockCheckboxes.prop('checked', this.checked);
                if ($dropdown.hasClass('fa-caret-right')) {
                    $dropdown.removeClass('fa-caret-right').addClass('fa-caret-down');
                    $container.removeClass('hidden');
                }
            });

            $('.block-dropdown').click(function () {
                var $dropdown = $(this);
                var $container = $(this).closest('.block-container').find('.student-container');
                if ($dropdown.hasClass('fa-caret-right')) {
                    $dropdown.removeClass('fa-caret-right').addClass('fa-caret-down');
                    $container.removeClass('hidden');
                } else {
                    $dropdown.removeClass('fa-caret-down').addClass('fa-caret-right');
                    $container.addClass('hidden');
                }
            });

            // Select/deselect all students within a year when the year checkbox is clicked
            $('.year-checkbox').click(function () {
                var $yearCheckboxes = $(this).closest('.year').find('.student-checkbox, .block-checkbox');
                $yearCheckboxes.prop('checked', this.checked);
            });

            $('.year-dropdown').click(function () {
                var $dropdown = $(this).closest('.year').find('.year-dropdown');
                var $container = $(this).closest('.year').find('.block-container');
                if ($dropdown.hasClass('fa-caret-right')) {
                    $dropdown.removeClass('fa-caret-right').addClass('fa-caret-down');
                    $container.removeClass('hidden');
                } else {
                    $dropdown.removeClass('fa-caret-down').addClass('fa-caret-right');
                    $container.addClass('hidden');
                }
            });

            // Select/deselect all students within a program when the program checkbox is clicked
            $('.program-checkbox').click(function () {
                var $programCheckboxes = $(this).closest('.program-group').find('.student-checkbox, .block-checkbox, .year-checkbox');
                var $idevent = $
                $programCheckboxes.prop('checked', this.checked);
            });

            $('.checkbox-input').on('change', function() {
                const tile = $(this).next('.checkbox-tile');

                if (this.checked) {
                    tile.addClass('border-teal-500 shadow-lg text-teal-500');
                } else {
                    tile.removeClass('border-teal-500 shadow-lg text-teal-500');
                }
            });

        })
    </script>
<?php } ?>