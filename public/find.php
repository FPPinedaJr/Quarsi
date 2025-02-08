<?php
session_start();
include_once "./includes/connect_db.php";

$input = isset($_POST['input']) ? trim($_POST['input']) : '';

if ($input !== '') {
    $input .= '%';
    $query = "
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
        INNER JOIN organization ON user.organization = organization.idorganization
        WHERE (f_name LIKE ? OR l_name LIKE ?)
        ORDER BY is_superuser DESC, is_officer DESC, user.year, user.block, user.f_name;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$input, $input]);
} else {
    $query = "
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
        INNER JOIN organization ON user.organization = organization.idorganization
        ORDER BY is_superuser DESC, is_officer DESC, user.year, user.block, user.f_name;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
}

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');

ob_start();
// Generate table rows
$table_html = '';
if (count($students) > 0) {
    foreach ($students as $student): ?>                        
        <tr id="student-<?php echo $student['iduser'] ?>"
            data-student_no="<?php echo $student['student_no'] ?>" data-f_name="<?php echo $student['f_name'] ?>"
            data-l_name="<?php echo $student['l_name'] ?>" data-idprogram="<?php echo $student['idprogram_user'] ?>"
            data-year="<?php echo $student['year'] ?>" data-block="<?php echo $student['block'] ?>"
            data-email="<?php echo $student['email'] ?>"
            data-profile_pic="<?= base64_encode($student['profile_pic']) ?>" data-user_type="<?php if ($student['is_officer'] == 1) {
                echo "1";
            } else if ($student['is_superuser'] == 1) {
                echo "2";
            } else if ($student['is_admin'] == 1) {
                echo "3";
            } else {
                echo "0";
            } ?>" class="cursor-pointer hover:bg-gray-300 even:bg-[#EDF4F2] odd:bg-gray-200" onclick="showEditStudentModal(<?=$student['iduser']?>)">
            <td class="py-2 pl-3 flex items-center"><img data-src="<?php if ($student['profile_pic']) {echo 'data:image/jpeg;base64,'. base64_encode($student['profile_pic']);}?>"
            class="rounded-full mr-2 border border-gray-400 w-6 h-6 lozad"> 
            <p class="<?php if($student['is_superuser'] == 0 && $student['is_officer'] == 1) {echo 'text-blue-600';} else if ($student['is_superuser'] == 1 && $student['is_officer'] == 1) {echo 'text-violet-500';}?> font-semibold"><?=$student['f_name']?> <?=$student['l_name']?></p></td>
            <td class="py-2 pl-3"><?=$student['student_no']?></td>
            <td class="py-2 pl-3"><?=$student['program']?> <?=$student['year']?> Block <?=$student['block']?></td>
        </tr>
    <?php endforeach;
    $table_html = ob_get_clean();
} else {
    $table_html = "<tr><td colspan='3' class='text-center text-gray-500 py-2'>No results found</td></tr>";
}

// Generate div content using output buffering
ob_start();
if (count($students) > 0): ?>
        <?php foreach ($students as $student): ?>
                    <?php if ($student['is_superuser'] == 1)?>
                    <div id="student-<?php echo $student['iduser'] ?>"
                        data-student_no="<?php echo $student['student_no'] ?>" data-f_name="<?php echo $student['f_name'] ?>"
                        data-l_name="<?php echo $student['l_name'] ?>" data-idprogram="<?php echo $student['idprogram_user'] ?>"
                        data-year="<?php echo $student['year'] ?>" data-block="<?php echo $student['block'] ?>"
                        data-email="<?php echo $student['email'] ?>"
                        data-profile_pic="<?= base64_encode($student['profile_pic']) ?>" data-user_type="<?php if ($student['is_officer'] == 1) {
                            echo "1";
                        } else if ($student['is_superuser'] == 1) {
                            echo "2";
                        } else if ($student['is_admin'] == 1) {
                            echo "3";
                        } else {
                            echo "0";
                        }?>"
                    class="flex student-div items-center p-1 border-2 border-gray shadow-md w-full h-36 rounded-md bg-[#d9f0ea] my-3 overflow-hidden" onclick="showEditStudentModal(<?php echo $student['iduser'] ?>)">
                <div class="w-1/3 h-full flex items-center justify-center">
                    <img data-src="data:image/jpeg;base64,<?= base64_encode($student['profile_pic']) ?>" alt="profile picture" class="w-16 h-16 rounded-full border border-gray-200 lozad">
                </div>
                <div class="w-2/3 h-full pl-2 p-1 flex justify-center flex-col text-nowrap">
                    <p class="font-semibold text-xl <?php if($student['is_superuser'] == 0 && $student['is_officer'] == 1) {echo 'text-blue-600';} else if ($student['is_superuser'] == 1 && $student['is_officer'] == 1) {echo 'text-violet-500';}?>"><?php echo $student['f_name'] ?> <?php echo $student['l_name'] ?></p>
                    <p class="text-gray-700"><?php echo $student['student_no'] ?></p>
                    <p class=""><?php echo $student['program'] ?> <?php echo $student['year'] ?> Block <?php echo $student['year'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div id="students-div" class="flex student-div flex-col w-full items-center md:hidden mt-10">
        <p class="text-gray-500 text-lg">No students found.</p>
    </div>
<?php endif; ?>

<?php $div_html = ob_get_clean();

// Return JSON response
echo json_encode(["table" => $table_html, "div" => $div_html]);
?>
