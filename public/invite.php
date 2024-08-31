<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("./includes/connect_db.php");

if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {
    $stmt = $pdo->prepare("
        SELECT o.short_name as organization, year, block, f_name, l_name, iduser
        FROM user u
        INNER JOIN organization o ON u.organization = o.idorganization
        WHERE is_admin != 1 AND is_superuser != 1 AND is_officer != 1
        ORDER BY organization, year, block, l_name, f_name

    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students -         <?php  
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

    <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
    <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="./assets/css/output.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
</head>

<?php
include_once("./includes/partial/sidebar.php");
include_once("./includes/partial/header.php");
?>

<body class="flex justify-center w-screen min-h-screen mt-20 overflow-x-hidden">
    <main class="w-full px-3 py-2 h-fit">
        <div class="text-lg w-fit">
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
                    echo '<div class="mb-4 program-group">';
                    echo '<label><input type="checkbox" class="program-checkbox"> <span class="font-bold">' . strtoupper(htmlspecialchars($currentProgram)) . '</span></label>';
                    echo '<div class="ml-4">';
                }

                if ($student['year'] !== $currentYear) {
                    if ($currentYear !== '') {
                        echo '</div></div></div></div>';
                    }
                    $currentYear = $student['year'];
                    $currentBlock = ''; 
                    echo '<div class="mb-2 ml-4 year">';
                    echo '<label><input type="checkbox" class="year-checkbox"> <span class="font-semibold">' . htmlspecialchars("YEAR " . $currentYear) . '</span></label>';
                    echo '<div class="ml-8">';
                }

                if ($student['block'] !== $currentBlock) {
                    if ($currentBlock !== '') {
                        echo '</div></div>';
                    }
                    $currentBlock = $student['block'];
                    echo '<div class="block mb-1 md:hover:text-emerald-400">';
                    echo '<label class="px-2 py-1 text-xs text-white rounded-full cursor-pointer md:hover:bg-emerald-700 bg-emerald-800"><input type="checkbox" class="hidden block-checkbox"> ' . htmlspecialchars("BLOCK " . $currentBlock) . '</label>';
                    echo '<div class="ml-12">';
                }

                echo '<div class="px-2 md:hover:bg-blue-300 md:hover:text-emerald-800 student">';
                echo '<label>';
                echo '<input type="checkbox" name="students[]" value="' . htmlspecialchars($student['iduser']) . '" class="student-checkbox">';
                echo '<span class="ml-1">';
                echo htmlspecialchars($student['f_name'] . ' ' . $student['l_name']);
                echo '</span>';
                echo '</label>';
                echo '</div>';
            }

            // Close the last block, year, and program
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


    </main>
</body>


</html>

<script src="./assets/js/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function () {
        // Select/deselect all students within a block when the block checkbox is clicked
        $('.block-checkbox').click(function () {
            var $blockCheckboxes = $(this).closest('.block').find('.student-checkbox');
            $blockCheckboxes.prop('checked', this.checked);
        });

        // Select/deselect all students within a year when the year checkbox is clicked
        $('.year-checkbox').click(function () {
            var $yearCheckboxes = $(this).closest('.year').find('.student-checkbox, .block-checkbox');
            $yearCheckboxes.prop('checked', this.checked);
        });

        // Select/deselect all students within a program when the program checkbox is clicked
        $('.program-checkbox').click(function () {
            var $programCheckboxes = $(this).closest('.program-group').find('.student-checkbox, .block-checkbox, .year-checkbox');
            $programCheckboxes.prop('checked', this.checked);
        });
    });

</script>