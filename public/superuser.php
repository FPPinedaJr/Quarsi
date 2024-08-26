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
        user.iduser as 'iduser',
        user.student_no as 'student_no',
        user.f_name as 'f_name',
        user.l_name as 'l_name',
        organization.abbreviation as 'program',
        user.organization as 'idprogram_user',
        user.year as 'year',
        user.block as 'block',
        user.email as 'email',
        user.is_officer as 'is_officer',
        user.is_superuser as 'is_superuser',
        user.is_admin as 'is_admin',
        user.total_points as 'total_points'
      FROM user
      INNER JOIN organization
      ON user.organization = organization.idorganization
      WHERE user.is_superuser = 1
    ");

    $stmt1->execute();
    $superusers = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("
        SELECT 
            organization.idorganization as 'idprogram',
            organization.program as 'name',
            organization.abbreviation as 'short_name'
        FROM organization
        ORDER BY organization.name
    ");

    $stmt2->execute();
    $programs = $stmt2->fetchAll(PDO::FETCH_ASSOC);
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
include_once("./includes/partial/sidebar.php");
?>

<?php
include_once("./includes/partial/header.php");
?>

<body class="flex justify-center w-screen min-h-screen mt-20 overflow-x-hidden">
    <main class="flex flex-col justify-center w-full px-3 py-2 h-fit">
        <!-- Search bar -->
        <div class="flex w-full h-10 mb-4 border border-gray-600 rounded-md md:w-[15rem]">
            <input id="search_superuser" name="search_superuser"
                class="flex h-full w-full align-center text-start pl-2 text-['mulish'] bg-white rounded-md focus:outline-none" placeholder="Find superuser...">
        </div>

        <!-- Filter -->
        <div class="flex w-full gap-2 mb-2 h-fit">
            <div class="flex items-start justify-center w-1/2 p-1 text-lg text-white bg-teal-700 rounded-sm md:w-20 h-fit">Year</div>
            <div class="flex items-center justify-center w-1/2 p-1 text-lg text-white bg-teal-700 rounded-sm md:w-20 h-fit">Block</div>
        </div>

        <!-- Add Button -->
        <div id="add_superuser_modal_btn" onclick="showAddSuperuserModal()"
            class="fixed z-20 flex items-center justify-center flex-shrink-0 w-8 h-8 bg-teal-700 border border-white rounded-md cursor-pointer top-4 right-5 md:top-3 md:w-10 md:h-10 hover:bg-teal-600/70">
            <i class="fa-solid fa-plus font-['mulish'] text-white text-xl md:text-3xl"></i>
        </div>

        <div class="my-2 border-t-2 border-zinc-500"></div>
        <!-- Students List -->

        <div class="flex-col hidden w-full gap-2 mt-2 bg-white h-fit md:justify-center md:items-center md:flex">
            <div id="" class="relative flex flex-col w-full md:w-3/4 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] h-fit md:flex-row md:h-10">
                <div class="flex items-center justify-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/4 md:h-full md:px-1 md:border-r-2 md:border-[#b7b9b9]">
                    Superuser Name
                </div>
                <div class="flex items-center justify-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/4 md:text-[1.3rem] md:px-1 md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9]">
                    Student ID
                </div>
                <div class="flex items-center justify-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/4 md:px-1 md:h-full md:text-[1.3rem]">
                    Program, Year & Block
                </div>
                <div class="absolute top-0 flex flex-col justify-center h-full p-1 text-white bg-zinc-600 font-['mulish'] align-center right-1 w-fit md:right-0 md:text-[1.3rem] md:w-1/4 md:h-full md:px-1">
                    <p class="text-center">Points</p>
                </div>
            </div>
        </div>

        <?php foreach ($superusers as $superuser): ?>
            <div id="superuser-<?php echo $superuser['iduser'] ?>" onclick="showEditSuperuserModal(<?php echo $superuser['iduser'] ?>)"
                data-student_no="<?php echo $superuser['student_no'] ?>" data-f_name="<?php echo $superuser['f_name'] ?>" data-l_name="<?php echo $superuser['l_name'] ?>"
                data-idprogram="<?php echo $superuser['idprogram_user'] ?>" data-year="<?php echo $superuser['year'] ?>"
                data-block="<?php echo $superuser['block'] ?>" data-email="<?php echo $superuser['email'] ?>" data-is_superuser="<?php echo $superuser['is_superuser'] ?>"
                data-user_type="<?php if ($superuser['is_superuser'] == 1) {
                                    echo "1";
                                } else if ($superuser['is_superuser'] == 1) {
                                    echo "2";
                                } else if ($superuser['is_admin'] == 1) {
                                    echo "3";
                                } else {
                                    echo "0";
                                } ?>" data-total_points="<?php echo $superuser['total_points'] ?>"
                class="flex flex-col w-full gap-2 mt-2 bg-white md:mt-0 h-fit md:justify-center md:items-center">
                <div id="" class="relative flex flex-col w-full md:w-3/4 p-1 md:p-0 border border-[#b7b9b9] bg-[#EDF4F2] hover:bg-[#dde4e2e0] h-fit cursor-pointer md:flex-row md:h-10">
                    <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-[1.5rem] md:text-[1.3rem] md:w-1/4 md:h-full md:px-1 md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                        <?= $superuser['f_name'] ?> <?= $superuser['l_name'] ?>
                    </div>
                    <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm text-zinc-600 md:w-1/4 md:text-[1.3rem] md:px-1 md:h-full md:text-black md:border-r-2 md:border-[#b7b9b9] md:font-medium">
                        <?= $superuser['student_no'] ?>
                    </div>
                    <div class="flex items-center w-full h-fit font-bold font-['mulish'] text-sm md:w-1/4 md:px-1 md:h-full md:text-[1.3rem] md:font-medium">
                        <?= $superuser['program'] ?> <?= $superuser['year'] ?> Block <?= $superuser['block'] ?>
                    </div>
                    <div class="absolute top-0 flex flex-col justify-center items-center h-full p-1 text-white bg-zinc-600 font-['mulish'] align-center right-0 min-w-16 md:right-0 md:text-[1.3rem] md:w-1/4 md:h-full md:px-1">
                        <p class="text-lg"><?= $superuser['total_points'] ?></p>
                        <p class="text-xs md:hidden">Points</p>
                    </div>
                </div>

            </div>

        <?php endforeach; ?>

    </main>

    <!-- Add superuser modal -->
    <div id="add_superuser_modal"
        class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
        <div id="add_superuser_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-3/5">
            <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Add Superuser</p>
            </div>

            <!-- fieldset -->
            <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                <form id="add_superuser_form" action="./includes/crud_superuser.php" type="button" method="POST"
                    class="flex flex-col justify-center w-full h-full px-3">

                    <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2 mt-4">
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <input id="add_f_name" name="f_name" type="text" required
                                class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                            <label for="f_name" class="pl-1 text-base md:text-lg text-zinc-600">First Name</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <input id="add_l_name" name="l_name" type="text" required
                                class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                            <label for="l_name" class="pl-1 text-base md:text-lg text-zinc-600">Last Name</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <input id="add_student_no" name="student_no" type="text" pattern="\d{4}-\d{1}-\d{4}" placeholder="ex. 2000-1-0001" required
                                class="w-full flex items-center md:h-9 pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                            <label for="student_no" class="pl-1 text-base md:text-lg text-zinc-600">Student No.</label>
                        </div>
                    </div>

                    <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2">
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="add_program" name="program" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?= $program['idprogram'] ?>"
                                        class="font-['mulish'] text-black text-base w-full"><?= $program['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="program" class="pl-1 text-base md:text-lg text-zinc-600">Program</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="add_year" name="year" type="number" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                <option disabled value="">Select</option>
                                <option value="1">First Year</option>
                                <option value="2">Second Year</option>
                                <option value="3">Third Year</option>
                                <option value="4">Fourth Year</option>
                            </select>
                            <label for="year" class="pl-1 text-base md:text-lg text-zinc-600">Year</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="add_block" name="block" type="number" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                <option disabled value="">Select</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <label for="block" class="pl-1 text-base md:text-lg text-zinc-600">Block</label>
                        </div>
                    </div>

                    <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2 mb-4">
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3">
                            <input id="add_email" name="email" type="email" required autocomplete="email"
                                class="w-full md:h-9 flex items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500 flex-grow-0">
                            <label for="email" class="pl-1 text-base md:text-lg text-zinc-600">Corp. Email</label>
                        </div>
                        <div class="flex-col invisible hidden w-full my-2 md:flex h-fit md:w-1/3"></div>
                        <div class="flex-col invisible hidden w-full my-2 md:flex h-fit md:w-1/3"></div>

                    </div>

                    <div class="flex items-center justify-center w-full gap-2 my-4 md:gap-4 md:flex-row">
                        <button id="add_superuser_btn" type="submit" name="action" value="add"
                            class="w-full h-10 text-['mulish'] bg-teal-700 hover:bg-teal-600 text-white font-semibold rounded-lg md:w-28">Add
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Edit students modal -->
    <div id="edit_superuser_modal"
        class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
        <div id="edit_superuser_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-3/5">
            <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Edit Superuser</p>
            </div>

            <!-- fieldset -->
            <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                <form id="edit_superuser_form" action="./includes/crud_superuser.php" type="button" method="POST"
                    class="flex flex-col justify-center w-full h-full px-3">

                    <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2 mt-4">
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <input id="iduser" name="iduser" type="hidden">
                            <input id="f_name" name="f_name" type="text" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                            <label for="f_name" class="pl-1 text-base md:text-lg text-zinc-600">First Name</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <input id="l_name" name="l_name" type="text" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                            <label for="l_name" class="pl-1 text-base md:text-lg text-zinc-600">Last Name</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <input id="student_no" name="student_no" type="text" pattern="\d{4}-\d{1}-\d{4}" placeholder="ex. 2000-1-0001" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                            <label for="student_no" class="pl-1 text-base md:text-lg text-zinc-600">Student No.</label>
                        </div>
                    </div>

                    <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2">
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="program" name="program" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem]">
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?= $program['idprogram'] ?>"
                                        class="font-['mulish'] text-black text-base w-full"><?= $program['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="program" class="pl-1 text-base md:text-lg text-zinc-600">Program</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="year" name="year" type="text" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                <option disabled value="" class="font-['mulish'] text-black text-base">Select</option>
                                <option value="1" class="font-['mulish'] text-black text-base">First Year</option>
                                <option value="2" class="font-['mulish'] text-black text-base">Second Year</option>
                                <option value="3" class="font-['mulish'] text-black text-base">Third Year</option>
                                <option value="4" class="font-['mulish'] text-black text-base">Fourth Year</option>
                            </select>
                            <label for="year" class="pl-1 text-base md:text-lg text-zinc-600">Year</label>
                        </div>
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                            <select id="block" name="block" type="text" required
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                <option disabled value="" class="font-['mulish'] text-black text-base">Select</option>
                                <option value="1" class="font-['mulish'] text-black text-base">1</option>
                                <option value="2" class="font-['mulish'] text-black text-base">2</option>
                                <option value="3" class="font-['mulish'] text-black text-base">3</option>
                            </select>
                            <label for="block" class="pl-1 text-base md:text-lg text-zinc-600">Block</label>
                        </div>
                    </div>

                    <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2 mb-4">
                        <div class="flex flex-col w-full my-2 h-fit md:w-1/3">
                            <input id="email" name="email" type="email" required autocomplete="email"
                                class="w-full flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                            <label for="email" class="pl-1 text-base md:text-lg text-zinc-600">Corp. Email</label>
                        </div>

                        <div class='flex flex-col w-full my-2 h-fit md:w-1/3'>
                            <select id='user_type' name='user_type' type='text' required
                                class='w-full flex md:h-9 items-center pl-1 font-mulish text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500'>
                                <option disabled value='' class='text-base text-black font-mulish'>Select</option>
                                <option value='0' class='text-base text-black font-mulish'>Student</option>
                                <option value='1' class='text-base text-black font-mulish'>Officer</option>
                                <option value='2' class='text-base text-black font-mulish'>Superuser</option>
                                <option value='3' class='text-base text-black font-mulish'>Admin</option>
                            </select>
                            <label for='user_type' class='pl-1 text-base md:text-lg text-zinc-600'>User Type</label>
                        </div>
                        <div class='flex flex-col w-full my-2 h-fit md:w-1/3'>
                            <input id='total_points' name='total_points' type='number' required
                                class='flex items-center w-full pl-1 text-black border border-gray-500 md:h-9 font-mulish focus:outline-teal-500'>
                            <label for='total_points' class='pl-1 text-base md:text-lg text-zinc-600'>Total Points</label>
                        </div>

                    </div>

                    <div class="flex flex-col items-center justify-center w-full gap-2 my-4 md:gap-4 md:flex-row">
                        <button id="save_student_btn" type="submit" value="submit" name="action"
                            class="w-full h-10 text-['mulish'] bg-teal-700 hover:bg-teal-600 text-white font-semibold rounded-lg md:w-20">Save
                        </button>
                        <button id="delete_superuser_btn" type="button" onclick="showDeleteSuperuserModal()"
                            class="w-full h-10 text-['mulish'] bg-red-700 hover:bg-red-600 text-white font-semibold rounded-lg md:w-20">Delete
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete_superuser_modal"
        class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center invisible w-full h-full overflow-y-hidden backdrop-blur-sm bg-gray-500/30">
        <div id="delete_superuser_modal_main"
            class="flex-col w-10/12 md:w-96 h-fit p-2 rounded-lg items-center justify-content bg-[#fbfcf8]">
            <div class="flex items-center w-full h-16 px-2 border-b border-emerald-700">
                <p class="font-['mulish'] text-emerald-700 font-semibold text-xl">Delete Superuser</p>
            </div>
            <div class="flex flex-col w-full h-auto p-2 text-md">
                <p class="font-semibold text-emerald-700">This will delete "<span id="superuser_to_delete"></span>."</p>
                <p class="text-emerald-700">Are you sure?</p>
            </div>
            <div class="flex flex-col w-full gap-2 p-2 md:flex-row md:mt-5 h-fit">
                <button id="deletesuperuserCancel" onclick="hideDeleteSuperuserModal()"
                    class="w-full p-1 border rounded-lg md:w-20 md:ml-auto border-emerald-700 hover:bg-emerald-700 hover:text-white text-md text-emerald-700">Cancel</button>
                <form action="./includes/crud_superuser.php" type="button" method="POST">
                    <button type="submit" value="delete" name="action"
                        class="w-full h-full p-1 text-white bg-red-600 rounded-lg md:w-20 md:ml-2 hover:bg-red-700 text-md">Delete</button>
                    <input id="id_delete_superuser" type="hidden" name="iduser" class="">
                </form>
            </div>
        </div>
    </div>

</body>


</html>

<script src="./assets/js/jquery-3.7.1.min.js"></script>
<script>
    function showAddSuperuserModal(id) {
        $('#add_superuser_modal').removeClass('invisible');
        $('body').addClass('overflow-hidden');
        $('#add_iduser').val('');
        $('#add_f_name').val('');
        $('#add_l_name').val('');
        $('#add_idprogram').val('');
        $('#add_student_no').val('');
        $('#add_year').val('');
        $('#add_block').val('');
        $('#add_email').val('');
    }

    function hideAddSuperuserModal() {
        $('#add_superuser_modal').addClass('invisible');
        $('body').removeClass('overflow-hidden');
    }

    function showEditSuperuserModal(id) {
        $('#edit_superuser_modal').removeClass('invisible');
        $('body').addClass('overflow-hidden')

        var $student_no = $('#superuser-' + id).data('student_no');
        var $f_name = $('#superuser-' + id).data('f_name');
        var $l_name = $('#superuser-' + id).data('l_name');
        var $idprogram = $('#superuser-' + id).data('idprogram');
        var $year = $('#superuser-' + id).data('year');
        var $block = $('#superuser-' + id).data('block');
        var $email = $('#superuser-' + id).data('email');
        var $user_type = $('#superuser-' + id).data('user_type');
        var $total_points = $('#superuser-' + id).data('total_points');

        $('#iduser').val(id);
        $('#f_name').val($f_name);
        $('#l_name').val($l_name);
        $('#idprogram').val($idprogram);
        $('#student_no').val($student_no);
        $('#year').val($year);
        $('#block').val($block);
        $('#email').val($email);
        $('#user_type').val($user_type);
        $('#total_points').val($total_points);

    }

    function hideEditSuperuserModal() {
        $('#edit_superuser_modal').addClass('invisible');
        $('body').removeClass('overflow-hidden');
    }

    function showDeleteSuperuserModal() {
        var $id = $('#iduser').val()
        $('#delete_superuser_modal').removeClass('invisible');
        $('body').addClass('overflow-hidden');
        $('#superuser_to_delete').text($('#superuser-' + $id).data('f_name') + " " + $('#superuser-' + $id).data('l_name'));
        $('#id_delete_superuser').val($id);
    }

    function hideDeleteSuperuserModal() {
        $('#delete_superuser_modal').addClass('invisible');
        $('body').removeClass('overflow-hidden');
    }

    function changeHeaderTitle() {
        $('#header_title').text('Superusers');
    }

    $(document).ready(function() {
        changeHeaderTitle();
        
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#edit_superuser_modal_main').length && $(event.target).closest('#edit_superuser_modal').length) {
                hideEditSuperuserModal();
            }
        })

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#delete_superuser_modal_main').length && $(event.target).closest('#delete_superuser_modal').length) {
                hideDeleteSuperuserModal();
            }
        })

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#add_superuser_modal_main').length && $(event.target).closest('#add_superuser_modal').length) {
                hideAddSuperuserModal();
            }
        })
    })
</script>