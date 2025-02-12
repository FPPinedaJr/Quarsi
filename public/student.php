<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("./includes/connect_db.php");

if (!$_SESSION["logged_in"] || !($_SESSION['is_officer'] == 1 || $_SESSION['is_superuser'] == 1 || $_SESSION['is_admin'] == 1)) {
    header("Location: index.php");
} else {
    $limit = 50;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
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
        user.profile_pic as 'profile_pic'
      FROM user
      INNER JOIN organization
      ON user.organization = organization.idorganization
      WHERE user.is_admin <> 1
      ORDER BY is_superuser DESC, is_officer DESC, user.year, user.block, user.f_name
      LIMIT ? OFFSET ?;
    ");

    $stmt1->execute([$limit, $offset]);
    $students = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    $results_count = count($students);

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

    $stmt3 = $pdo->query("SELECT COUNT(*) FROM user WHERE is_admin <> 1");
    $total_students = $stmt3->fetchColumn();
    $total_pages = ceil($total_students / $limit);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Students - <?php
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
        <link rel="stylesheet" href="./assets/css/output.css?v=1.3">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
            rel="stylesheet">
        <script src="./assets/js/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/lozad"></script>
    </head>

    <?php
    include_once("./includes/partial/sidebar.php");
    ?>

    <?php
    include_once("./includes/partial/header.php");
    ?>

    <body class="flex justify-center w-screen min-h-screen mt-20 overflow-x-hidden">
        <main class="flex flex-col justify-center w-full px-3 py-2 h-fit">

            <div class="flex items-center w-full p-2 md:w-fit h-fit">
                <div class="flex items-center justify-end h-fit md:w-[20rem] w-fit">
                    <p class="text-3xl text-teal-700 font-['mulish'] font-semibold">Quarsi</p>
                </div>

                <div class="flex items-center w-full p-1 ml-5 rounded-lg md:w-fit bg-gray-200/70 h-fit">
                    <!-- Search bar -->
                    <div class="flex bg-none w-full h-10 md:w-[15rem]">
                        <input id="search_student" name="search_student"
                            class="flex h-full w-full align-center text-start pl-2 bg-transparent text-['mulish'] focus:outline-none"
                            placeholder="Find student...">
                    </div>

                    <!-- Filter -->
                    <div class="relative flex items-center justify-center p-1 text-teal-700 bg-none h-fit w-fit">
                        <i id="filter" onclick="toggleFilter()" class="text-xl cursor-pointer fa-solid fa-sliders"></i>

                        <!-- Filters -->
                        <div id="filter_dropdown"
                            class="absolute flex flex-col bg-white border rounded-sm top-[2rem] right-[0.20rem] md:-top-2 md:-right-[9.5rem] h-fit w-36 border-gray-200/50 invisible">
                            <div
                                class="relative group w-full h-fit md:px-2 pl-2 py-1 text-sm font-['mulish'] hover:bg-gray-100 cursor-pointer flex justify-between items-center border-b border-gray-100/70 text-center pr-5">
                                <i class="ml-2 text-xs fa-solid fa-angle-left md:hidden"></i>Year<i
                                    class="hidden text-xs fa-solid fa-angle-right md:block"></i>
                                <div id="year_filter"
                                    class="absolute top-0 flex flex-col invisible border border-gray-200 rounded-md group-hover:visible right-28 md:-right-28 h-fit w-28">
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="year[]" id="year-1" value="1">
                                        <label for="year-1" class="ml-4 font-['mulish'] cursor-pointer">Year 1</label>
                                    </div>
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="year[]" id="year-2" value="2">
                                        <label for="year-2" class="ml-4 font-['mulish'] cursor-pointer">Year 2</label>
                                    </div>
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="year[]" id="year-3" value="3">
                                        <label for="year-3" class="ml-4 font-['mulish'] cursor-pointer">Year 3</label>
                                    </div>
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="year[]" id="year-4" value="4">
                                        <label for="year-4" class="ml-4 font-['mulish'] cursor-pointer">Year 4</label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="group w-full h-fit md:px-2 pl-2 py-1 text-sm font-['mulish'] hover:bg-gray-100 cursor-pointer flex justify-between items-center text-center pr-5">
                                <i class="ml-2 text-xs fa-solid fa-angle-left md:hidden"></i>Block<i
                                    class="hidden text-xs fa-solid fa-angle-right md:block"></i>
                                <div id="block_filter"
                                    class="absolute flex flex-col invisible border border-gray-200 rounded-md group-hover:visible group top-7 right-28 md:-right-28 h-fit w-28">
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="block[]" id="block-0" value="0">
                                        <label for="block-0" class="ml-4 font-['mulish'] cursor-pointer">Block 0</label>
                                    </div>
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="block[]" id="block-1" value="1">
                                        <label for="block-1" class="ml-4 font-['mulish'] cursor-pointer">Block 1</label>
                                    </div>
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="block[]" id="block-2" value="2">
                                        <label for="block-2" class="ml-4 font-['mulish'] cursor-pointer">Block 2</label>
                                    </div>
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="block[]" id="block-3" value="3">
                                        <label for="block-3" class="ml-4 font-['mulish'] cursor-pointer">Block 3</label>
                                    </div>
                                    <div
                                        class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                        <input type="checkbox" name="block[]" id="block-4" value="4">
                                        <label for="block-4" class="ml-4 font-['mulish'] cursor-pointer">Block 4</label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="group w-full h-fit md:px-2 pl-2 py-1 text-sm font-['mulish'] hover:bg-gray-100 cursor-pointer flex justify-between items-center text-center pr-5">
                                <i class="ml-2 text-xs fa-solid fa-angle-left md:hidden"></i>Organization<i
                                    class="hidden text-xs fa-solid fa-angle-right md:block"></i>
                                <div id="org_filter"
                                    class="absolute flex flex-col invisible border border-gray-200 rounded-md group-hover:visible group top-14 right-28 md:-right-28 h-fit w-28">
                                    <?php foreach ($programs as $program): ?>
                                        <div
                                            class="flex w-full px-2 py-1 bg-white border-b border-gray-100 cursor-pointer h-fit hover:bg-gray-100">
                                            <input type="checkbox" name="org[]" class="org_radio"
                                                id="org-<?= $program['idprogram'] ?>" value="<?= $program['idprogram'] ?>">
                                            <label for="org-<?= $program['idprogram'] ?>"
                                                class="ml-4 font-['mulish'] cursor-pointer"><?= $program['short_name'] ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Add Button -->
            <div id="add_student_modal_btn" onclick="showAddStudentModal()"
                class="fixed z-[100] flex justify-center flex-shrink-0 w-8 h-8 bg-teal-700 border border-white rounded-md cursor-pointer top-4 right-5 md:top-3 md:w-10 md:h-10 hover:bg-teal-600/70">
                <i class="fa-solid fa-plus font-['mulish'] text-white text-xl md:text-3xl"></i>
            </div>


            <!-- Students List -->
            <!-- Desktop view -->
            <div class="justify-center hidden w-full my-10 border-gray-400 md:flex">
                <table id="students-table" class="w-2/3">
                    <thead>
                        <th class="w-1/2 py-2 pl-3 text-lg font-semibold text-left text-white bg-teal-700">Students Name
                        </th>
                        <th class="w-1/4 py-2 pl-3 text-lg font-semibold text-left text-white bg-teal-700">Student ID</th>
                        <th class="w-1/4 py-2 pl-3 text-lg font-semibold text-left text-white bg-teal-700">Program, Year &
                            Block</th>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr id="student-<?php echo $student['iduser'] ?>"
                                data-student_no="<?php echo $student['student_no'] ?>"
                                data-f_name="<?php echo $student['f_name'] ?>" data-l_name="<?php echo $student['l_name'] ?>"
                                data-idprogram="<?php echo $student['idprogram_user'] ?>"
                                data-year="<?php echo $student['year'] ?>" data-block="<?php echo $student['block'] ?>"
                                data-email="<?php echo $student['email'] ?>"
                                data-profile_pic="<?= base64_encode($student['profile_pic']) ?>" data-user_type="<?php if ($student['is_officer'] == 1 && $student['is_superuser'] == 0) {
                                      echo "1";
                                  } else if ($student['is_officer'] == 1 && $student['is_superuser'] == 1) {
                                      echo "2";
                                  } else if ($student['is_admin'] == 1) {
                                      echo "3";
                                  } else {
                                      echo "0";
                                  } ?>" class="cursor-pointer hover:bg-gray-300 even:bg-[#EDF4F2] odd:bg-gray-200"
                                onclick="showEditStudentModal(<?= $student['iduser'] ?>)">
                                <td class="flex items-center py-2 pl-3"><img data-src="<?php if ($student['profile_pic']) {
                                    echo 'data:image/jpeg;base64,' . base64_encode($student['profile_pic']);
                                } ?>" class="w-6 h-6 mr-2 border border-gray-400 rounded-full lozad">
                                    <p id="fullname" class="<?php if ($student['is_superuser'] == 0 && $student['is_officer'] == 1) {
                                        echo 'text-blue-600';
                                    } else if ($student['is_superuser'] == 1 && $student['is_officer'] == 1) {
                                        echo 'text-violet-500';
                                    } ?> font-semibold">
                                        <?= $student['f_name'] ?>         <?= $student['l_name'] ?>
                                    </p>
                                </td>
                                <td id="student-no" class="py-2 pl-3"><?= $student['student_no'] ?></td>
                                <td id="program-yr-blck" class="py-2 pl-3"><?= $student['program'] ?>         <?= $student['year'] ?>
                                    Block
                                    <?= $student['block'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div id="students-div" class="flex flex-col items-center w-full mt-10 md:hidden">
                <?php foreach ($students as $student): ?>
                    <?php if ($student['is_superuser'] == 1) ?>
                    <div id="student-<?php echo $student['iduser'] ?>" data-student_no="<?php echo $student['student_no'] ?>"
                        data-f_name="<?php echo $student['f_name'] ?>" data-l_name="<?php echo $student['l_name'] ?>"
                        data-idprogram="<?php echo $student['idprogram_user'] ?>" data-year="<?php echo $student['year'] ?>"
                        data-block="<?php echo $student['block'] ?>" data-email="<?php echo $student['email'] ?>"
                        data-profile_pic="<?= base64_encode($student['profile_pic']) ?>" data-user_type="<?php if ($student['is_officer'] == 1) {
                              echo "1";
                          } else if ($student['is_superuser'] == 1) {
                              echo "2";
                          } else if ($student['is_admin'] == 1) {
                              echo "3";
                          } else {
                              echo "0";
                          } ?>"
                        class="flex student-div items-center p-1 border border-teal-400 shadow-md w-full h-36 rounded-md bg-[#d9f0ea] my-3 overflow-hidden"
                        onclick="showEditStudentModal(<?php echo $student['iduser'] ?>)">
                        <div class="flex items-center justify-center w-1/3 h-full">
                            <img data-src="data:image/jpeg;base64,<?= base64_encode($student['profile_pic']) ?>"
                                alt="profile picture" class="w-16 h-16 border border-gray-200 rounded-full lozad">
                        </div>
                        <div class="flex flex-col justify-center w-2/3 h-full p-1 pl-2 text-nowrap">
                            <p id="fullname" class="font-semibold text-xl <?php if ($student['is_superuser'] == 0 && $student['is_officer'] == 1) {
                                echo 'text-blue-600';
                            } else if ($student['is_superuser'] == 1 && $student['is_officer'] == 1) {
                                echo 'text-violet-500';
                            } ?>">
                                <?php echo $student['f_name'] ?>         <?php echo $student['l_name'] ?>
                            </p>
                            <p id="student-no" class="text-gray-700"><?php echo $student['student_no'] ?></p>
                            <p id="program-yr-blck"><?php echo $student['program'] ?>         <?php echo $student['year'] ?> Block
                                <?php echo $student['year'] ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="pagination_container" class="flex justify-center w-full">
                <div class="flex flex-col items-center my-5 md:w-1/3">
                    <div class="flex items-center gap-2">
                        <a href="?page=1" data-page="1"
                            class="paginate_btn px-3 py-2 border bg-teal-200 text-teal-800 rounded hover:bg-teal-300 <?= ($page == 1) ? 'pointer-events-none opacity-50' : '' ?>">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                        <a href="?page=<?= max(1, $page - 1) ?>" data-page="<?= max(1, $page - 1) ?>"
                            class="paginate_btn px-3 py-2 border bg-teal-200 text-teal-800 rounded hover:bg-teal-300 <?= ($page == 1) ? 'pointer-events-none opacity-50' : '' ?>">
                            <i class="fas fa-angle-left"></i>
                        </a>

                        <!-- Input Box -->
                        <div class="flex items-center px-2 py-1 bg-white rounded">
                            <input type="text" id="page_input"
                                class="w-12 text-center border-b-2 border-teal-500 outline-none" value="<?= $page ?>"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')" minlength="1"
                                maxlength="<?= $total_pages ?>">
                            <span class="mx-2 text-sm">of</span>
                            <span class="text-sm total-pages"><?= $total_pages ?> pages</span>
                        </div>


                        <a href="?page=<?= min($total_pages, $page + 1) ?>" data-page="<?= min($total_pages, $page + 1) ?>"
                            class="paginate_btn px-3 py-2 border bg-teal-200 text-teal-800 rounded hover:bg-teal-300 <?= ($page == $total_pages) ? 'pointer-events-none opacity-50' : '' ?>">
                            <i class="fas fa-angle-right"></i>
                        </a>
                        <a href="?page=<?= $total_pages ?>" data-page="<?= $total_pages ?>"
                            class="paginate_btn px-3 py-2 border bg-teal-200 text-teal-800 rounded hover:bg-teal-300 <?= ($page == $total_pages) ? 'pointer-events-none opacity-50' : '' ?>">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </div>

                    <p class="mt-2 mb-2 text-sm text-teal-500">Showing <span class="results-count"><?= $results_count ?></span> results...</p>
                </div>
            </div>

        </main>

        <!-- Add student modal -->
        <div id="add_student_modal"
            class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center overflow-y-auto">
            <div id="add_student_modal_main" class="relative flex flex-col w-5/6 h-fit md:w-3/5">
                <div class="flex items-center justify-center w-full h-12 text-center bg-teal-700 md:h-16">
                    <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Add Student</p>
                </div>

                <!-- fieldset -->
                <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                    <form id="add_student_form" action="./includes/crud_student.php" type="button" method="POST"
                        enctype="multipart/form-data" class="flex flex-col justify-center w-full h-full px-3">
                        <input type="hidden" name="action" value="add">

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
                                <input id="add_student_no" name="student_no" type="text"
                                    pattern="\d{4}-\d{1,2}-\d{4}[\dA-Za-z]{0,2}" placeholder="2000-1-0001" required
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
                                    <option value="">Select</option>
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
                                    <option value="">Select</option>
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
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3">
                                <input id="add_profile_pic" name="profile_pic" type="file" class="w-full bg-white md:h-9 flex items-center font-['mulish'] text-black focus:outline-teal-500 border border-gray-500 flex-grow-0
                                        file:h-full file:border-none file:bg-teal-700 file:text-white">
                                <label for="add_profile_pic" class="pl-1 text-base md:text-lg text-zinc-600">Profile
                                    Picture</label>
                            </div>
                            <div class="flex-col invisible hidden w-full my-2 md:flex h-fit md:w-1/3"></div>

                        </div>

                        <div class="flex items-center justify-center w-full gap-2 my-4 md:gap-4 md:flex-row">
                            <button id="add_student_btn" type="button" onclick="addStudent()"
                                class="w-full h-10 text-['mulish'] bg-teal-700 hover:bg-teal-600 text-white font-semibold rounded-lg md:w-28">Add
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Edit students modal -->
        <div id="edit_student_modal"
            class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center  ">
            <div id="edit_student_modal_main" class="relative flex flex-col w-5/6 overflow-y-auto h-4/5 md:h-fit md:w-3/5">
                <div
                    class="flex flex-col items-center justify-center w-full py-2 text-center bg-teal-700 md:flex-row h-fit md:h-16">
                    <p class="font-semibold text-white font-['merriweather_sans'] text-2xl md:text-3xl">Edit Students</p>
                    <div class="block md:absolute md:right-5 text-white font-['mulish']">
                        <a id="student_log" class="hover:underline">View Student's Log</a>
                    </div>
                </div>

                <!-- fieldset -->
                <div class="w-full h-fit flex bg-[#fbfcf8] p-1">
                    <form id="edit_student_form" action="./includes/crud_student.php" enctype="multipart/form-data"
                        type="button" method="POST" class="flex flex-col justify-center w-full h-full px-3">

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2 mt-4">
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3">
                                <input id="iduser" name="iduser" type="hidden">
                                <input id="edit_action" name="action" type="hidden" value="update">
                                <input id="edit_f_name" name="f_name" type="text" required
                                    class="w-full md:w-3/4 flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="edit_f_name" class="pl-1 text-base md:text-lg text-zinc-600">First Name</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="edit_l_name" name="l_name" type="text" required
                                    class="w-full md:w-3/4 flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="edit_l_name" class="pl-1 text-base md:text-lg text-zinc-600">Last Name</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <input id="edit_student_no" name="student_no" type="text"
                                    pattern="\d{4}-\d{1,2}-\d{4}[\dA-Za-z]{0,2}" placeholder="2000-1-0001" required
                                    class="w-full md:w-3/4 flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="edit_student_no" class="pl-1 text-base md:text-lg text-zinc-600">Student
                                    No.</label>
                            </div>
                        </div>

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2">
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="edit_program" name="program" required
                                    class="w-full md:w-3/4 flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem]">
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?= $program['idprogram'] ?>"
                                            class="font-['mulish'] text-black text-base w-full"><?= $program['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="edit_program" class="pl-1 text-base md:text-lg text-zinc-600">Program</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="edit_year" name="year" type="text" required
                                    class="w-full md:w-3/4 flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <option value="" class="font-['mulish'] text-black text-base">Select</option>
                                    <option value="1" class="font-['mulish'] text-black text-base">First Year</option>
                                    <option value="2" class="font-['mulish'] text-black text-base">Second Year</option>
                                    <option value="3" class="font-['mulish'] text-black text-base">Third Year</option>
                                    <option value="4" class="font-['mulish'] text-black text-base">Fourth Year</option>
                                </select>
                                <label for="edit_year" class="pl-1 text-base md:text-lg text-zinc-600">Year</label>
                            </div>
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3 ">
                                <select id="edit_block" name="block" type="text" required
                                    class="w-full md:w-3/4 flex md:h-9 items-center pl-1 font-['mulish'] text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500">
                                    <option value="" class="font-['mulish'] text-black text-base">Select</option>
                                    <option value="1" class="font-['mulish'] text-black text-base">1</option>
                                    <option value="2" class="font-['mulish'] text-black text-base">2</option>
                                    <option value="3" class="font-['mulish'] text-black text-base">3</option>
                                </select>
                                <label for="edit_block" class="pl-1 text-base md:text-lg text-zinc-600">Block</label>
                            </div>
                        </div>

                        <div class="flex w-full h-fit flex-col font-['mulish'] bg-[#fbfcf8] md:flex-row md:gap-2">
                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3">
                                <input id="edit_email" name="email" type="email" required autocomplete="email"
                                    class="w-full md:w-3/4 flex md:h-9 items-center pl-1 font-['mulish'] text-black focus:outline-teal-500 border border-gray-500">
                                <label for="edit_email" class="pl-1 text-base md:text-lg text-zinc-600">Corp. Email</label>
                            </div>

                            <div class="flex flex-col w-full my-2 h-fit md:w-1/3">
                                <input id="add_profile_pic" name="profile_pic" type="file"
                                    class="flex items-center flex-grow-0 w-full text-black bg-white border border-gray-500 md:w-3/4 md:h-9 focus:outline-teal-500 file:h-full file:border-none file:bg-teal-700 file:text-white">
                                <input id="hidden_profile" type="hidden" name="hidden_profile">
                                <label for="add_profile_pic" class="pl-1 text-base md:text-lg text-zinc-600">Profile
                                    Picture</label>
                            </div>

                            <?php if ($_SESSION['is_admin'] == 1 || $_SESSION['is_superuser'] == 1) {
                                echo "
                                <div class='flex flex-col w-full my-2 h-fit md:w-1/3'>
                                    <select id='edit_user_type' name='user_type' type='text' required
                                        class='w-full md:w-3/4 flex md:h-9 items-center pl-1 font-mulish text-black text-base border border-gray-500 h-[1.65rem] focus:outline-teal-500'>
                                        <option value='' class='text-base text-black font-mulish'>Select</option>
                                        <option value='0' class='text-base text-black font-mulish'>Student</option>
                                        <option value='1' class='text-base text-black font-mulish'>Officer</option>
                                        <option value='2' class='text-base text-black font-mulish'>Superuser</option>
                                        <option value='3' class='text-base text-black font-mulish'>Admin</option>
                                    </select>
                                    <label for='user_type' class='pl-1 text-base md:text-lg text-zinc-600'>User Type</label>
                                </div>";
                            } else {
                                echo "
                                <div class='flex invisible w-full my-2 h-fit md:w-1/3'></div>
                                ";
                            } ?>

                        </div>

                        <div class="flex flex-col items-center justify-center w-full gap-2 my-4 md:gap-4 md:flex-row">
                            <button id="save_student_btn" type="button" onclick="editStudent()"
                                class="w-full h-10 text-['mulish'] bg-teal-700 hover:bg-teal-600 text-white font-semibold rounded-lg md:w-20">Save
                            </button>
                            <button id="delete_student_btn" type="button" onclick="showDeleteStudentModal()"
                                class="w-full h-10 text-['mulish'] bg-red-700 hover:bg-red-600 text-white font-semibold rounded-lg md:w-20">Delete
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="delete_student_modal"
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center invisible w-full h-full overflow-y-hidden backdrop-blur-sm bg-gray-500/30">
            <div id="delete_student_modal_main"
                class="flex-col w-10/12 md:w-96 h-fit p-2 rounded-lg items-center justify-content bg-[#fbfcf8]">
                <div class="flex items-center w-full h-16 px-2 border-b border-emerald-700">
                    <p class="font-['mulish'] text-emerald-700 font-semibold text-xl">Delete Student</p>
                </div>
                <div class="flex flex-col w-full h-auto p-2 text-md">
                    <p class="font-semibold text-emerald-700">This will delete "<span id="student_to_delete"></span>."</p>
                    <p class="text-emerald-700">Are you sure?</p>
                </div>
                <div class="flex flex-col w-full gap-2 p-2 md:flex-row md:mt-5 h-fit">
                    <button id="deleteStudentCancel" onclick="hideDeleteStudentModal()"
                        class="w-full p-1 border rounded-lg md:w-20 md:ml-auto border-emerald-700 hover:bg-emerald-700 hover:text-white text-md text-emerald-700">Cancel</button>
                    <form id="delete_student_form" action="./includes/crud_student.php" type="button" method="POST">
                        <button type="button" onclick="deleteStudent()" 
                            class="w-full h-full p-1 text-white bg-red-600 rounded-lg md:w-20 md:ml-2 hover:bg-red-700 text-md">Delete</button>
                        <input id="id_delete_student" type="hidden" name="iduser" class="">
                        <input type="hidden" value="delete" name="action">
                    </form>
                </div>
            </div>
        </div>


    </body>


    </html>

    <script src="./assets/js/jquery-3.7.1.min.js"></script>
    <script>
        function showAddStudentModal() {
            $('#add_student_modal').removeClass('invisible');
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

        function hideAddStudentModal() {
            $('#add_student_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function addStudent() {
            let data = new FormData($('#add_student_form')[0]);
            hideAddStudentModal();
            showLoader("Loading...");
            $('#filter').addClass('invisible');

            $.ajax({
                url: 'includes/crud_student.php',
                type: 'POST',
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    location.reload();
                    $('#filter').removeClass('invisible')
                }
            });
        }

        function showEditStudentModal(id) {
            $('#edit_student_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden')

            var $student_no = $('#student-' + id).data('student_no');
            var $f_name = $('#student-' + id).data('f_name');
            var $l_name = $('#student-' + id).data('l_name');
            var $idprogram = $('#student-' + id).data('idprogram');
            var $year = $('#student-' + id).data('year');
            var $block = $('#student-' + id).data('block');
            var $email = $('#student-' + id).data('email');
            var $user_type = $('#student-' + id).data('user_type');
            var $total_points = $('#student-' + id).data('total_points');
            var $hidden_profile = $('#student-' + id).data('profile_pic');

            $('#student_log').attr('href', 'attendance.php?student=' + id);
            $('#iduser').val(id);
            $('#edit_f_name').val($f_name);
            $('#edit_l_name').val($l_name);
            $('#edit_program').val($idprogram);
            $('#edit_student_no').val($student_no);
            $('#edit_year').val($year);
            $('#edit_block').val($block);
            $('#edit_email').val($email);
            $('#edit_user_type').val($user_type);
            $('#hidden_profile').val($hidden_profile);

        }

        function hideEditStudentModal() {
            $('#edit_student_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function editStudent() {
            let id = $('#iduser').val();
            let data = new FormData($('#edit_student_form')[0]);

            // for (let pair of data.entries()) {
            //     console.log(pair[0] + ', ' + pair[1]);  // Debug output
            // }            
            hideEditStudentModal();
            showLoader("Loading...");
            $('#filter').addClass('invisible');

            $.ajax({
                url: 'includes/crud_student.php',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function (response) {
                    window.location.hash = response;
                    location.reload();
                    $('#filter').removeClass('invisible')
                }
            });
        }


        function showDeleteStudentModal() {
            var $id = $('#iduser').val()
            $('#delete_student_modal').removeClass('invisible');
            $('body').addClass('overflow-hidden');
            $('#student_to_delete').text($('#student-' + $id).data('f_name') + " " + $('#student-' + $id).data('l_name'));
            $('#id_delete_student').val($id);
        }

        function hideDeleteStudentModal() {
            $('#delete_student_modal').addClass('invisible');
            $('body').removeClass('overflow-hidden');
        }

        function deleteStudent() {
            let data = $('#delete_student_form').serialize();
            let id = $('#iduser').val();
            showLoader("Loading...");
            $('#filter').addClass('invisible');

            $.ajax({
                url: 'includes/crud_student.php',
                type: 'POST',
                data: data,
                success: function (response) {
                    hideDeleteStudentModal();
                    hideEditStudentModal();
                    window.location.hash = response;
                    location.reload();
                    $('#filter').removeClass('invisible')
                }
            });
        }

        function changeHeaderTitle() {
            $('#header_title').text('Students');
        }

        function toggleFilter() {
            $('#filter_dropdown').toggleClass('invisible');
        }

        function hideFilter() {
            $('#filter_dropdown').addClass('invisible');
        }

        $(document).ready(function () {
            const observer = lozad();
            observer.observe();

            changeHeaderTitle();
            let debounceTimer;

            $("#page_input").keypress(function (event) {
                if (event.which === 13) {
                    let pageNumber = $(this).val().trim();
                    let maxPage = parseInt("<?= $total_pages ?>");
                    if (pageNumber !== "" && !isNaN(pageNumber)) {
                        pageNumber = Math.max(1, Math.min(maxPage, parseInt(pageNumber)));
                        window.location.href = "?page=" + pageNumber;
                    }
                }
            })

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#edit_student_modal_main').length && $(event.target).closest('#edit_student_modal').length) {
                    hideEditStudentModal();
                }
            })

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#delete_student_modal_main').length && $(event.target).closest('#delete_student_modal').length) {
                    hideDeleteStudentModal();
                }
            })

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#add_student_modal_main').length && $(event.target).closest('#add_student_modal').length) {
                    hideAddStudentModal();
                }
            })

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#filter').length && !$(event.target).closest('#filter_dropdown').length) {
                    hideFilter();
                }
            })

            if (localStorage.getItem('loading') === 'true') {
                $("#students-table tbody").html(
                    '<tr>' +
                    '<td colspan="3" class="flex items-center justify-center w-full h-36">' +
                    '<div class="w-10 h-10 border-4 border-gray-300 rounded-full border-t-teal-500 animate-spin"></div>' +
                    '</td>' +
                    '</tr>'
                );
            }

            localStorage.removeItem('loading');


            $("#search_student").on("keyup", function () {
                $("input[name='year[]'], input[name='block[]'], input[name='org[]']").prop("checked", true);

                let input = $(this).val().trim();

                if (input == '') {
                    location.reload();
                    return;
                }


                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    $("#students-table tbody").html(
                        '<tr>' +
                        '<td colspan="3" class="relative w-full h-36">' +
                        '<div class="w-10 h-10 mx-auto border-4 border-gray-300 rounded-full border-t-teal-500 animate-spin"></div>' +
                        '</td>' +
                        '</tr>'
                    );

                    $("#students-div").html(
                        '<div class="relative flex justify-center items-center w-full h-36">' +
                        '<div class="w-10 h-10 mx-auto border-4 border-gray-300 rounded-full border-t-teal-500 animate-spin"></div>' +
                        '<div>' 
                    );

                    $.ajax({
                        url: "./find.php",
                        method: "POST",
                        dataType: "json",
                        data: { input: input },
                        success: function (response) {
                            $("#students-table tbody").html(response.table);
                            $("#students-div").html(response.div);

                            if (response.results_count) {
                                $("#page_input").val(1);
                                $(".total-pages").text(1);
                                $(".results-count").text(response.results_count);
                                $(".paginate_btn").addClass("pointer-events-none opacity-50");
                            }

                            const observer = lozad();
                            observer.observe();
                            localStorage.removeItem('loading');
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                            $("#students-table tbody").html('<p class="text-red-500">Error occurred. Please try again.</p>');
                        }
                    });
                }, 600);
            });

            $("input[name='year[]'], input[name='block[]'], input[name='org[]']").prop("checked", true);

            $("input[name='year[]'], input[name='block[]'], input[name='org[]']").on("change", function () {
                let selectedYears = $("input[name='year[]']:checked").map(function () {
                    return this.value;
                }).get();

                let selectedBlocks = $("input[name='block[]']:checked").map(function () {
                    return this.value;
                }).get();

                let selectedOrg = $("input[name='org[]']:checked").map(function () {
                    return this.value;
                }).get();

                let page = $(".paginate_btn").data("page") || 1;
                console.log(page);

                if (selectedYears.length === 0 || selectedBlocks.length === 0 || selectedOrg.length === 0) {
                    $("#students-table tbody").html("<tr><td colspan='3' class='p-4 text-center'>No students found</td></tr>");
                    $("#students-div").html('<p class="text-lg text-gray-500">No students found.</p>');
                    $(".results-count").text('0');
                    return;
                }

                $("#students-table tbody").html(
                    '<tr>' +
                    '<td colspan="3" class="relative w-full h-36">' +
                    '<div class="w-10 h-10 mx-auto border-4 border-gray-300 rounded-full border-t-teal-500 animate-spin"></div>' +
                    '</td>' +
                    '</tr>'
                );

                $("#students-div").html(
                    '<div class="relative flex justify-center items-center w-full h-36">' +
                    '<div class="w-10 h-10 mx-auto border-4 border-gray-300 rounded-full border-t-teal-500 animate-spin"></div>' +
                    '<div>' 
                );

                $.ajax({
                    url: "./filter.php",
                    method: "POST",
                    dataType: "json",
                    data: {
                        years: selectedYears,
                        blocks: selectedBlocks,
                        org: selectedOrg,
                        page: page
                    },
                    success: function (response) {
                        $("#students-table tbody").html(response.table);
                        $("#students-div").html(response.div);
                        $("#page_container").html(response.page);

                        if (response.results_count) {
                            $("#page_input").val(1);
                            $(".total-pages").text(1);
                            $(".results-count").text(response.results_count);
                            $(".paginate_btn").addClass("pointer-events-none opacity-50");
                        }

                        const observer = lozad();
                        observer.observe();
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
        })
    </script>
<?php } ?>