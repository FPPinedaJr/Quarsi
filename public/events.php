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
        DATE_FORMAT(event.date, '%M %d, %Y') AS 'formatted-date',
        event.date AS 'date',
        event.name AS 'name',
        event.log_time AS 'log_time',
        event.morning_in AS 'morning_in',
        event.morning_out AS 'morning_out',
        event.afternoon_in AS 'afternoon_in',
        event.afternoon_out AS 'afternoon_out',
            (
                SELECT GROUP_CONCAT(attendance.user) 
                FROM attendance
                WHERE attendance.event = event.idevent
            ) AS 'invited_users'
    FROM event
    LEFT JOIN attendance
        ON event.idevent = attendance.event
        GROUP BY 
        event.idevent
    ");

    $stmt1->execute();
    $events = $stmt1->fetchAll(PDO::FETCH_ASSOC);


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
        <link rel="stylesheet" href="./assets/css/output.css?v=1.2">
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
            <div class="flex justify-center w-full my-4 h-fit">
                <table class="w-full md:w-2/3">
                    <tr class="text-lg font-light text-left text-white bg-teal-700">
                        <th class="w-3/6 px-2 py-1 font-normal">EVENT NAME</th>
                        <th class="w-2/6 px-2 py-1 font-normal">DATE</th>
                        <th class="w-1/6 px-2 py-1 font-normal">CURRENT LOG</th>
                    </tr>
    
                    <?php foreach ($events as $event): ?>
                    <tr id="event-<?=$event['idevent']?>" data-idevent="<?=$event['idevent']?>" data-name="<?=$event['name']?>" 
                        data-date="<?=$event['date']?>" data-log_time="<?=$event['log_time']?>"
                        data-morning_in="<?=$event['morning_in']?>" data-morning_out="<?=$event['morning_out']?>"
                        data-afternoon_in="<?=$event['afternoon_in']?>" data-afternoon_out="<?=$event['afternoon_out']?>"
                        data-users="<?=$event['invited_users']?>"
                        class="cursor-pointer border-b border-[#b7b9b9] bg-[#EDF4F2] hover:bg-gray-200 text-lg" onclick="showEditEventModal(<?=$event['idevent']?>)"> 
                        <td class="py-1 pl-2"><?=$event['name']?></td>
                        <td class="py-1 pl-2"><?=$event['formatted-date']?></td>
                        <td class="py-1 pl-2">
                            <?php
                            if ($event['log_time']  == 0) {
                                echo 'Disabled';
                            } else if ($event['log_time']  == 1) {
                                echo 'Morning In';
                            } else if ($event['log_time']  == 2) {
                                echo 'Morning Out';
                            } else if ($event['log_time']  == 3) {
                                echo 'Afternoon In';
                            } else if ($event['log_time']  == 4) {
                                echo 'Afternoon Out';
                            } ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </main>

        <!-- Events Add Modal -->
        <div id="add_event_modal"
            class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
            <div id="add_event_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-[25rem]">
                <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                    <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Add Event</p>
                </div>

                <!-- fieldset -->
                <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                    <form id="add_event_form" action="./includes/crud_event.php" type="button" method="POST"
                        class="flex flex-col justify-center w-full h-full px-3">

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] mt-4">
                            <div class="flex flex-col w-full my-2 h-fit ">
                                <input id="add_name" name="name" type="text" required autocomplete="name"
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="add_name" class="pl-1 text-base md:text-lg text-zinc-600">Event Name</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit ">
                                <input id="add_date" name="date" type="date" required
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="add_date" class="pl-1 text-base md:text-lg text-zinc-600">Date</label>
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
            <div id="edit_event_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-[25rem]">
                <div class="relative flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                    <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Edit Event</p>
                    <div id="inviteBtn" class="absolute z-30 flex items-center top-2.3 h-fit invite md:top-4 right-5 invite_btn"
                        onclick="">
                        <i
                            class="text-base text-white cursor-pointer md:text-xl fa-solid fa-user-plus hover:text-emerald-400"></i>
                    </div>
                </div>

                <!-- fieldset -->
                <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                    <form id="edit_event_form" action="./includes/crud_event.php" type="button" method="POST"
                        class="flex flex-col justify-center w-full h-full px-3">

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] mt-4">
                            <input id="idevent" name="idevent" type="hidden">
                            <div class="flex flex-col w-full my-2 h-fit md:w-full ">
                                <input id="name" name="name" type="text" required autocomplete="name"
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="name" class="pl-1 text-base md:text-lg text-zinc-600">Event Name</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-full ">
                                <input id="date" name="date" type="date" required
                                    class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="date" class="pl-1 text-base md:text-lg text-zinc-600">Date</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-full ">
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
                    <p class="font-semibold text-emerald-700">Are you sure to delete event "<span id="event_to_delete"></span>"?</p>
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

        <!-- Invite Modal -->
        <div id="invite_modal"
            class="fixed top-0 left-0 z-40 flex items-center justify-center invisible w-full h-full backdrop-blur-sm bg-[#2e2c2c69]">
            <form id="invite_students_form" action="./includes/crud_invite.php" type="button" method="POST"
                class="w-10/12 md:w-1/3 h-2/3">
                <input id="invite_event" type="hidden" name="idevent">
                <input type="hidden" name="action" value="invite">
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
                        echo '</div>';                    
                    }
                    if ($currentProgram !== '') {
                        echo '</div>';
                        echo '</div>';                    
                        echo '</div>';                    
                    }
                    ?>
                
                    <div class="flex flex-col justify-center items-center w-full h-fit border-t-2 pt-2 mt-4">
                        <div class="w-full text-center font-semibold text-lg mb-6 text-zinc-800">Log Time</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 w-3/4">
                            <label class="inline-flex items-center mb-5 cursor-pointer">
                                <input type="checkbox" name="logtime[]" value="1" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Morning In</span>
                            </label>                        
                            <label class="inline-flex items-center mb-5 cursor-pointer">
                                <input type="checkbox" name="logtime[]" value="2" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Morning Out</span>
                            </label>                        
                            <label class="inline-flex items-center mb-5 cursor-pointer">
                                <input type="checkbox" name="logtime[]" value="3" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Afternoon In</span>
                            </label>                        
                            <label class="inline-flex items-center mb-5 cursor-pointer">
                                <input type="checkbox" name="logtime[]" value="4" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Afternoon Out</span>
                            </label>                        
                        </div>
                    </div>
                        
                    <div class="flex items-center justify-center w-full py-3 bg-white h-fit">
                        <button id="add_invite_btn" type="button" onclick="showConfirmInviteModal()"
                        class="rounded-lg hover:bg-teal-600 w-40 p-1 text-xl font-semibold text-white font-['mulish'] bg-teal-700 cursor-pointer flex justify-center add_invite_btn">Add
                        Invite</button>
                    </div>
                </div>
                
            </form>
        </div>

        
        <!-- Edit Invite Modal -->
        <div id="edit_invite_modal"
            class="fixed top-0 left-0 z-30 invisible flex items-center justify-center w-full h-full backdrop-blur-sm bg-[#2e2c2c69]">
            <div id="edit_invite_modal_main" class="overflow-y-auto text-lg bg-white w-10/12 md:w-1/3 h-2/3 overflow-x-hidden">
                <form id="edit_invite_students_form" action="./includes/crud_invite.php" type="button" method="POST"
                    class="w-full h-fit">
                    <input id="edit_invite_event" type="hidden" name="idevent">
                    <input type="hidden" name="action" value="update_invite">
                        <div
                            class="w-full flex items-center justify-center font-semibold text-3xl text-white h-16 bg-teal-700 text-['mulish']">
                            Update Invite
                        </div>
                        <div class="w-full flex h-fit p-1 text-xl ml-2 font-semibold text-zinc-700 my-2"><p>Invited Students: <span id="invited-students" class="text-teal-500"></span></p></div>
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
                            echo '</div>';                    
                        }
                        if ($currentProgram !== '') {
                            echo '</div>';
                            echo '</div>';                    
                            echo '</div>';                    
                        }
                        ?>

                        <div class="flex flex-col justify-center items-center w-full h-fit border-t-2 pt-2 mt-4">
                            <div class="w-full text-center font-semibold text-lg mb-6 text-zinc-800">Log Time</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 w-3/4">
                                <label class="inline-flex items-center mb-5 cursor-pointer">
                                    <input id="morning_in_toggle" type="checkbox" name="logtime[]" value="1" class="sr-only peer" onclick="showLogtimeModal('#morning_in_toggle')">
                                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Morning In</span>
                                </label>                        
                                <label class="inline-flex items-center mb-5 cursor-pointer">
                                    <input id="morning_out_toggle" type="checkbox" name="logtime[]" value="2" class="sr-only peer" onclick="showLogtimeModal('#morning_out_toggle')">
                                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Morning Out</span>
                                </label>                        
                                <label class="inline-flex items-center mb-5 cursor-pointer">
                                    <input id="afternoon_in_toggle" type="checkbox" name="logtime[]" value="3" class="sr-only peer" onclick="showLogtimeModal('#afternoon_in_toggle')">
                                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Afternoon In</span>
                                </label>                        
                                <label class="inline-flex items-center mb-5 cursor-pointer">
                                    <input id="afternoon_out_toggle" type="checkbox" name="logtime[]" value="4" class="sr-only peer" onclick="showLogtimeModal('#afternoon_out_toggle')">
                                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600 dark:peer-checked:bg-teal-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">Afternoon Out</span>
                                </label>                        
                            </div>
                        </div>

                        <div class="flex items-center justify-center w-full py-3 bg-white h-fit mt-6 mb-2">
                            <button id="edit_invite_btn" type="button" onclick="showConfirmEditInviteModal()"
                            class="rounded-lg hover:bg-teal-600 w-40 p-1 text-xl font-semibold text-white font-['mulish'] bg-teal-700 cursor-pointer flex justify-center add_invite_btn">Update
                            Invite</button>
                        </div>
                </form>
            </div>


        </div>

        <!-- Toggle switch alert modal -->
        <div id="switch_modal" class="w-full h-full bg-gray-500/30 backdrop-blur-sm flex justify-center items-center z-30 fixed top-0 left-0 invisible">
            <div id="switch_modal_main" class="rounded-lg w-3/4 md:w-1/4 h-36 flex flex-col bg-[#fbfcf8] p-2">
                <div class="w-full h-fit pt-2 mb-2 border-b border-teal-700 font-semibold text-teal-800 text-lg">Change Log Time</div>
                <div class="w-full h-auto bg-[#fbfcf8] text-teal-800 flex flex-wrap">
                    <p>
                        Are you sure to turn <span id="changelog_status" class="font-semibold"></span> <span id="changelog" class="italic"></span> log?
                    </p>
                </div>
                <div class="w-full flex justify-center mt-auto mb-1">
                    <button onclick="hideLogtimeModal()" type="button"  class="rounded px-2 py-1 text-teal-800 hover:text-teal-500 hover:underline">Cancel</button>
                    <button class="rounded px-2 py-1 ml-8 text-white bg-teal-800 hover:bg-teal-500" onclick="confirmLogtimeChange()">Confirm</button>
                </div>
            </div>
        </div>

        <!-- Confirm Invite -->
        <div id="confirm_invite" class="w-full h-full bg-gray-500/30 backdrop-blur-sm flex justify-center items-center z-40 fixed top-0 left-0 invisible">
            <div id="confirm_invite_main" class="rounded-lg w-3/4 md:w-1/4 h-36 flex flex-col bg-[#fbfcf8] p-2">
                <div class="w-full h-fit pt-2 mb-2 border-b border-teal-700 font-semibold text-teal-800 text-lg">Confirm Invite</div>
                <div class="w-full h-auto bg-[#fbfcf8] text-teal-800 flex flex-wrap">
                    <p>
                        Are you sure to invite <span id="invite_count" class="font-semibold"></span> students?
                    </p>
                </div>
                <div class="w-full flex justify-center mt-auto mb-1">
                    <button onclick="hideConfirmInviteModal()" type="button"  class="rounded px-2 py-1 text-teal-800 hover:text-teal-500 hover:underline">Cancel</button>
                    <button class="rounded px-2 py-1 ml-8 text-white bg-teal-800 hover:bg-teal-500" onclick="confirmInvite()">Confirm</button>
                </div>
            </div>
        </div>

        <!-- Invite Error -->
        <div id="confirm_invite_error" class="w-full h-full bg-gray-500/30 backdrop-blur-sm flex justify-center items-center z-40 fixed top-0 left-0 invisible">
            <div id="confirm_invite_error_main" class="rounded-lg w-3/4 md:w-1/4 h-36 flex flex-col bg-[#fbfcf8] p-2">
                <div class="w-full h-fit pt-2 mb-2 border-b border-teal-700 font-semibold text-teal-800 text-lg">Invite Error</div>
                <div class="w-full h-auto bg-[#fbfcf8] text-teal-800 flex flex-wrap">
                    <p>
                        Cannot invite 0 students. Please select students to invite.
                    </p>
                </div>
                <div class="w-full flex justify-center mt-auto mb-1">
                    <button onclick="hideConfirmInviteErrorModal()" type="button"  class="rounded px-2 py-1 text-white bg-teal-700">Okay</button>
                </div>
            </div>
        </div>

        <!-- Confirm Update Invite -->
        <div id="confirm_update_invite" class="w-full h-full bg-gray-500/30 backdrop-blur-sm flex justify-center items-center z-40 fixed top-0 left-0 invisible">
            <div id="confirm_update_invite_main" class="rounded-lg w-3/4 md:w-1/4 h-36 flex flex-col bg-[#fbfcf8] p-2">
                <div class="w-full h-fit pt-2 mb-2 border-b border-teal-700 font-semibold text-teal-800 text-lg">Confirm Invite Changes</div>
                <div class="w-full h-auto bg-[#fbfcf8] text-teal-800 flex flex-wrap">
                    <p>
                        Are you sure to make changes in this invite?
                    </p>
                </div>
                <div class="w-full flex justify-center mt-auto mb-1">
                    <button onclick="hideConfirmEditInviteModal()" type="button"  class="rounded px-2 py-1 text-teal-800 hover:text-teal-500 hover:underline">Cancel</button>
                    <button class="rounded px-2 py-1 ml-8 text-white bg-teal-800 hover:bg-teal-500" onclick="confirmEditInvite()">Confirm</button>
                </div>
            </div>
        </div>

        <!-- Invited student notif -->
        <div id="notif_invite" class="fixed invisible top-30 left-1/2 -translate-x-1/2 flex items-center justify-between w-1/2 md:w-1/5 h-10 rounded-md border border-teal-500 bg-white text-teal-700 text-xs md:text-sm shadow">
            <p class="flex-1 text-center">
                Successfully invited <span id="success_invite_count"  class="font-bold"></span> students
            </p>
            <button class="p-2 hover:bg-gray-200 h-full rounded-md">
                <i class="fa-solid fa-xmark"></i>
            </button>
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
            var $log_time = $('#event-' + id).data('log_time');
            var $users =  $('#event-' + id).data('users');

            $('#idevent').val(id);
            $('#date').val($date);
            $('#name').val($name);
            $('#log_time').val($log_time);
            if ($users) {
                $('#inviteBtn').off('click').on('click', showEditInviteModal); 
            } else {
                $('#inviteBtn').off('click').on('click', showInviteModal); 
            }
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
        
        function showInviteModal() {
            var $id = $('#idevent').val();
            $('#invite_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#invite_event').val($id);
            $('#edit_event_modal').addClass('invisible');   
            hideEditInviteModal();         
            $('input[type="checkbox"]').prop('checked', false);
        }

        function hideInviteModal() {
            $('#invite_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function showEditInviteModal() {
            var $id = $('#idevent').val();
            var $usersStr = $('#event-' + $id).data('users');
            $usersStr = String($usersStr);
            if ($usersStr) { 
                if ($usersStr.includes(',')) {
                    var $usersList = $usersStr.split(',');
                } else {
                    var $usersList = [$usersStr];
                }
            } else {
                console.log('No users found for the specified event.');
            }

            $('#invited-students').text($usersList.length);

            $('#edit_invite_modal_main').find('.student-checkbox').each(function() {
                var studentValue = $(this).val(); 

                if ($usersList.includes(studentValue)) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });

            
            $('#edit_invite_modal_main').find('.block-checkbox').each(function () {
                var $blockCheckbox = $(this);
                var $studentCheckboxes = $blockCheckbox.closest('.block-container').find('.student-checkbox');

                var totalCheckboxes = $studentCheckboxes.length;
                var checkedCheckboxes = $studentCheckboxes.filter(':checked').length; 

                $blockCheckbox.prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            $('#edit_invite_modal_main').find('.year-checkbox').each(function () {
                var $yearCheckbox = $(this);
                var $blockCheckboxes = $yearCheckbox.closest('.year').find('.block-checkbox');

                var totalCheckboxes = $blockCheckboxes.length;
                var checkedCheckboxes = $blockCheckboxes.filter(':checked').length; 

                $yearCheckbox.prop('checked', totalCheckboxes === checkedCheckboxes);
            });
            
            $('#edit_invite_modal_main').find('.program-checkbox').each(function () {
                var $programCheckbox = $(this);
                var $yearCheckboxes = $programCheckbox.closest('.program-group').find('.year-checkbox');

                var totalCheckboxes = $yearCheckboxes.length;
                var checkedCheckboxes = $yearCheckboxes.filter(':checked').length; 

                $programCheckbox.prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            var $morning_in = "";
            var $morning_out = "";
            var $afternoon_in = "";
            var $afternoon_out = "";

            if($('#event-' + $id).data('morning_in')) {
                $morning_in = 1;
            }
            if($('#event-' + $id).data('morning_out')) {
                $morning_out = 2;
            }
            if($('#event-' + $id).data('afternoon_in')) {
                $afternoon_in = 3;
            }
            if($('#event-' + $id).data('afternoon_out')) {
                $afternoon_out = 4;
            }

            var logs_list = [$morning_in, $morning_out, $afternoon_in, $afternoon_out];
            
            $('#edit_invite_modal_main').find('.peer').each(function() {
                var checkboxValue = $(this).val();
                if (logs_list.map(String).includes(checkboxValue)) {
                    $(this).prop('checked', true);
                }
            });

            $('#edit_invite_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#edit_invite_event').val($id);
            $('#edit_event_modal').addClass('invisible');
        }

        function hideEditInviteModal() {
            $('#edit_invite_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        let pendingCheckbox = null;

        function showLogtimeModal(idlog) {
            pendingCheckbox = $(idlog); 
            const logText = pendingCheckbox.closest('label').find('span').text();

            const status = pendingCheckbox.is(':checked') ? "on" : "off";
            $('#changelog_status').text(status);
            $('#changelog').text(logText);

            $('#switch_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
        }

        function hideLogtimeModal() {
            $('#switch_modal').addClass('invisible');

            if (pendingCheckbox) {
                pendingCheckbox.prop('checked', !pendingCheckbox.is(':checked'));
                pendingCheckbox = null; 
            }
        }

        function confirmLogtimeChange() {
            const checkboxState = pendingCheckbox.is(':checked') ? 1 : 0; 
            const checkbox = pendingCheckbox.val(); 
            var $id = $('#idevent').val();

            console.log(checkboxState);
            console.log(checkbox);
            console.log($id);

            $.ajax({
                url: './includes/update_logtime.php', 
                type: 'POST',
                data: {
                    id: $id,
                    checkbox: checkbox,
                    state: checkboxState,
                },
                success: function (response) {
                    console.log('Database updated:', response);

                    $('#switch_modal').addClass('invisible');
                    pendingCheckbox = null;
                },
                error: function (xhr, status, error) {
                    console.error('Error updating database:', error);

                    pendingCheckbox.prop('checked', !pendingCheckbox.is(':checked'));
                    $('#switch_modal').addClass('invisible');
                    pendingCheckbox = null;
                }
            });
        }

        function showConfirmInviteModal () {
            $count = $('input.student-checkbox:checked').length; 
            if ($count == 0) {
                $('#confirm_invite_error').removeClass("invisible");
            } else {
                $('#invite_count').text($count);
                $('#confirm_invite').removeClass("invisible");
            }
        }

        function hideConfirmInviteModal() {
            $('#confirm_invite').addClass("invisible");
        }

        function hideConfirmInviteErrorModal() {
            $('#confirm_invite_error').addClass("invisible");
        }

        function showConfirmEditInviteModal () {
            $count = $('input.student-checkbox:checked').length; 
            if ($count == 0) {
                $('#confirm_invite_error').removeClass("invisible");
            } else {
                $('#confirm_update_invite').removeClass("invisible");  
            }
        }

        function hideConfirmEditInviteModal() {
            $count = $('input.student-checkbox:checked').length; 
            $('#confirm_update_invite').addClass("invisible");
        }

        function confirmInvite() {
            var formData = $('#invite_students_form').serialize();

            $.ajax({
                url: './includes/crud_invite.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    console.log("Response from server:", response);                    
                    if (response.trim() === "success") {  
                        hideConfirmInviteModal();
                        location.reload();
                        let count = $('input.student-checkbox:checked').length; 
                        // sessionStorage.setItem('invite_success', count); 
                        $('#success_invite_count').text(count);
                        hideInviteModal();
                    } else {
                        alert('Error: ' + response);
                    }
                },
                error: function () {
                    alert('Something went wrong. Please try again.');
                }
            });
        }

        function confirmEditInvite() {


            var formData = $('#edit_invite_students_form').serialize();

            $.ajax({
                url: './includes/crud_invite.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    console.log("Response from server:", response);                    
                    if (response.trim() === "success") {  
                        hideConfirmEditInviteModal();
                        location.reload();

                        hideEditInviteModal();
                    } else {
                        alert('Error: ' + response);
                    }
                },
                error: function () {
                    alert('Something went wrong. Please try again.');
                }
            });
        }

        function showNotification() {
            $('#notif_invite').removeClass('invisible'); 
            setTimeout(function () {
                $('#notif_invite').addClass('invisible'); 
            }, 5000);
        }

        $('#notif_invite button').on('click', function () {
            $('#notif_invite').addClass('invisible');
        });

        $(document).ready(function () {
            changeHeaderTitle();

            let invite_count = sessionStorage.getItem('invite_success');
            if (invite_count) {
                $('#success_invite_count').text(invite_count);
                showNotification();
                sessionStorage.removeItem('invite_success'); 
            }

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
                if (!$(event.target).closest('#edit_invite_modal_main').length && $(event.target).closest('#edit_invite_modal').length) {
                    hideEditInviteModal();
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#invite_modal_main').length && $(event.target).closest('#invite_modal').length) {
                    hideInviteModal();
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#switch_modal_main').length && $(event.target).closest('#switch_modal').length) {
                    hideLogtimeModal();
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#confirm_invite_main').length && $(event.target).closest('#confirm_invite').length) {
                    hideConfirmInviteModal();
                }  
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#confirm_invite_error_main').length && $(event.target).closest('#confirm_invite_error').length) {
                    hideConfirmInviteErrorModal();
                }  
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#confirm_edit_invite_main').length && $(event.target).closest('#confirm_edit_invite').length) {
                    hideConfirmEditInviteModal();
                }  
            });

            $('#invite_modal_main').find('.block-checkbox').click(function () {
                var $blockCheckboxes = $(this).closest('.block-container').find('.student-checkbox');
                var $dropdown = $(this).closest('.block-container').find('.block-dropdown');
                var $container = $(this).closest('.block-container').find('.student-container');
                $blockCheckboxes.prop('checked', this.checked);
                if ($dropdown.hasClass('fa-caret-right')) {
                    $dropdown.removeClass('fa-caret-right').addClass('fa-caret-down');
                    $container.removeClass('hidden');
                }
            });

            $('#invite_modal_main').find('.block-dropdown').click(function () {
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

            $('#invite_modal_main').find('.year-checkbox').click(function () {
                var $yearCheckboxes = $(this).closest('.year').find('.student-checkbox, .block-checkbox');
                $yearCheckboxes.prop('checked', this.checked);
            });

            $('#invite_modal_main').find('.year-dropdown').click(function () {
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

            $('#invite_modal_main').find('.program-checkbox').click(function () {
                var $programCheckboxes = $(this).closest('.program-group').find('.student-checkbox, .block-checkbox, .year-checkbox');
                var $idevent = $
                $programCheckboxes.prop('checked', this.checked);
            });

            $('#invite_modal_main').find('.checkbox-input').on('change', function() {
                const tile = $(this).next('.checkbox-tile');

                if (this.checked) {
                    tile.addClass('border-teal-500 shadow-lg text-teal-500');
                } else {
                    tile.removeClass('border-teal-500 shadow-lg text-teal-500');
                }
            });


            $('#edit_invite_modal_main').find('.block-checkbox').click(function () {
                var $blockCheckboxes = $(this).closest('.block-container').find('.student-checkbox');
                var $dropdown = $(this).closest('.block-container').find('.block-dropdown');
                var $container = $(this).closest('.block-container').find('.student-container');
                $blockCheckboxes.prop('checked', this.checked);
                if ($dropdown.hasClass('fa-caret-right')) {
                    $dropdown.removeClass('fa-caret-right').addClass('fa-caret-down');
                    $container.removeClass('hidden');
                }
            });

            $('#edit_invite_modal_main').find('.block-dropdown').click(function () {
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

            $('#edit_invite_modal_main').find('.year-checkbox').click(function () {
                var $yearCheckboxes = $(this).closest('.year').find('.student-checkbox, .block-checkbox');
                $yearCheckboxes.prop('checked', this.checked);
            });

            $('#edit_invite_modal_main').find('.year-dropdown').click(function () {
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

            $('#edit_invite_modal_main').find('.program-checkbox').click(function () {
                var $programCheckboxes = $(this).closest('.program-group').find('.student-checkbox, .block-checkbox, .year-checkbox');
                var $idevent = $programCheckboxes.prop('checked', this.checked);
            });

            $('#edit_invite_modal_main').find('.checkbox-input').on('change', function() {
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