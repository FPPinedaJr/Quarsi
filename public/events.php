<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("./includes/connect_db.php");

if ($_SESSION["logged_in"] == !true) {
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
                class="flex h-full w-full align-center text-start pl-2 text-['mulish'] bg-white rounded-md focus:outline-none" placeholder="Find event...">
        </div>

        <!-- Filter -->
        <div class="flex w-full mb-2 h-fit">
            <div class="flex items-center justify-center w-1/2 p-1 text-lg text-white bg-teal-700 rounded-sm md:w-20 h-fit">Date</div>
        </div>

        <!-- Add Button -->
        <div id="add_event_modal_btn" onclick="showAddEventModal()"
            class="fixed z-20 flex items-center justify-center flex-shrink-0 w-8 h-8 bg-teal-700 border border-white rounded-md cursor-pointer top-4 right-5 md:top-3 md:w-10 md:h-10 hover:bg-teal-600/70">
            <i class="fa-solid fa-plus font-['mulish'] text-white text-xl md:text-3xl"></i>
        </div>

        <div class="my-2 border-t-2 border-zinc-500"></div>
        <!-- Events List -->

        <div id=""
            class="flex-col hidden w-full gap-2 mt-2 bg-white md:flex md:mt-0 h-fit md:justify-center md:items-center">
            <div id="" class="relative flex flex-col w-full md:w-4/5 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] h-fit md:flex-row md:h-10">
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/6 md:h-full md:px-1 md:border-r-2 md:justify-center md:border-[#b7b9b9] md:font-medium">
                    Event Name
                </div>
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/6 md:text-[1.3rem] md:px-1 md:justify-center md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                    Date
                </div>
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:border-r-2 md:border-[#b7b9b9]">
                    Organization
                </div>
                <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:border-r-2 md:border-[#b7b9b9]">
                    Set Points
                </div>
                <div class="absolute top-0 right-0 flex flex-col items-center justify-center flex-grow-0 flex-shrink-0 w-24 h-full p-1 bg-zinc-600 align-center md:flex-row md:right-0 md:w-1/6 md:h-full md:px-1">
                    <div class="text-white font-['mulish'] text-lg md:text-[1.3rem] ">Status</div>
                    <div class="text-emerald-100 font-['mulish'] text-xs md:hidden">Log Time</div>
                </div>
                <div class="hidden md:flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:bg-zinc-600 md:border-r-2 md:border-[#b7b9b9] text-white">
                    Log Time
                </div>
            </div>
        </div>

        <?php foreach($events as $event): ?>

            <div id="event-<?= $event['idevent']?>" onclick="showEditEventModal(<?= $event['idevent']?>)"
                data-name="<?= $event['name']?>" data-date="<?= $event['date']?>" data-organization="<?= $event['idorganization']?>"
                data-log_time="<?= $event['log_time']?>" data-status="<?= $event['is_active']?>" data-set_points="<?= $event['set_points']?>"
                class="flex flex-col w-full gap-2 mt-2 bg-white md:mt-0 h-fit md:justify-center md:items-center">
                <div id="" class="relative flex flex-col w-full md:w-4/5 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] hover:bg-[#dde4e2e0] h-fit cursor-pointer md:flex-row md:h-10">
                    <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/6 md:h-full md:px-1 md:border-r-2 md:justify-center md:border-[#b7b9b9] md:font-medium">
                        <?= $event['name']?>
                    </div>
                    <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/6 md:text-[1.3rem] md:px-1 md:justify-center md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                        <?= $event['date']?>
                    </div>
                    <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:border-r-2 md:border-[#b7b9b9]">
                        <?= $event['organization']?>
                    </div>
                    <div class="flex items-center md:border-r-2 md:border-[#b7b9b9] w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium">
                        <?= $event['set_points']?>
                    </div>
                    <div class="absolute top-0 right-0 flex flex-col items-center justify-center flex-grow-0 flex-shrink-0 w-24 h-full font-['mulish'] p-1 bg-zinc-600 align-center md:flex-row md:right-0 md:w-1/6 md:h-full md:px-1">
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
                            <?php if ($event['log_time'] == 1) {
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
                    <div class="hidden md:flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/6 md:px-1 md:h-full md:text-[1.3rem] md:justify-center md:font-medium md:bg-zinc-600 md:border-r-2 md:border-[#b7b9b9] text-white">
                        <?php if ($event['log_time'] == 1) {
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
        <?php endforeach?>

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
                            <label for="add_set_points" class="pl-1 text-base md:text-lg text-zinc-600">Set Points</label>
                        </div>
                    </div>

                    <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2">
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="add_organization" name="organization" required autocomplete="off"
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                <?php foreach ($organizations as $organization): ?>
                                    <option value="<?= $organization['idorganization'] ?>"
                                        class="font-['mulish'] text-black text-base w-full"><?= $organization['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="add_organization" class="pl-1 text-base md:text-lg text-zinc-600">Organization</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="add_log_time" name="log_time" type="number" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
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
            <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Edit Event</p>
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
                                        class="font-['mulish'] text-black text-base w-full"><?= $organization['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="organization" class="pl-1 text-base md:text-lg text-zinc-600">Organization</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="log_time" name="log_time" type="number" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
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
                        class="w-full h-full p-1 text-white bg-red-600 rounded-lg md:w-20 md:ml-2 hover:bg-red-700 text-md">Delete</button>
                    <input id="id_delete_event" type="hidden" name="idevent" class="">
                </form>
            </div>
        </div>
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
        $('body').addClass('overflow-hidden')

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

    $(document).ready(function() {
        changeHeaderTitle();

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#edit_event_modal_main').length && $(event.target).closest('#edit_event_modal').length) {
                hideEditEventModal();
            }
        })

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#add_event_modal_main').length && $(event.target).closest('#add_event_modal').length) {
                hideAddEventModal();
            }
        })

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#delete_event_modal_main').length && $(event.target).closest('#delete_event_modal').length) {
                hideDeleteEventModal();
            }
        })
        
    })
</script>