<?php
session_start();
include_once "connect_db.php";

// Enable error reporting for debugging (Remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get filters from POST request
$year_filter = isset($_POST['year']) ? $_POST['year'] : null;
$year_dict = [
    0 => "Unknown Year",
    1 => "1st Year",
    2 => "2nd Year",
    3 => "3rd Year",
    4 => "4th Year"
];

$query = "
    SELECT 
        user.iduser, user.student_no, user.f_name, user.l_name, 
        organization.abbreviation AS program, user.organization AS idprogram_user, 
        user.year, user.email, user.is_officer, user.is_superuser, 
        user.is_admin, user.profile_pic
    FROM user
    INNER JOIN organization ON user.organization = organization.idorganization
    WHERE user.is_admin <> 1"; // Exclude admins

$params = [];


if ($year_filter && $year_filter != 'all') {
    $query .= " AND user.year = ?";
    $params[] = $year_filter; 
}

$query .= " ORDER BY is_superuser DESC, is_officer DESC, user.year, user.f_name";

if ($year_filter && $year_filter == 'all') {
    $query .= " LIMIT 50";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

ob_start();
if (count($students) > 0) {
    foreach ($students as $student): ?>
        <tr id="student-<?= $student['iduser'] ?>"
            data-student_no="<?= $student['student_no'] ?>" 
            data-f_name="<?= $student['f_name'] ?>" 
            data-l_name="<?= $student['l_name'] ?>" 
            data-idprogram="<?= $student['idprogram_user'] ?>" 
            data-year="<?= $student['year'] ?>" 
            data-email="<?= $student['email'] ?>" 
            data-profile_pic="<?= base64_encode($student['profile_pic']) ?>"
            data-user_type="<?= ($student['is_officer'] ? '1' : ($student['is_superuser'] ? '2' : ($student['is_admin'] ? '3' : '0'))) ?>"
            class="cursor-pointer hover:bg-gray-300 even:bg-[#EDF4F2] odd:bg-gray-200" 
            onclick="showEditStudentModal(<?= $student['iduser'] ?>)">
            <td class="flex items-center py-2 pl-3">
                <img data-src="<?= 'data:image/jpeg;base64,'. base64_encode($student['profile_pic']) ?>" 
                    class="w-6 h-6 mr-2 border border-gray-400 rounded-full lozad">
                <p class="<?= ($student['is_superuser'] && $student['is_officer']) ? 'text-orange-400' : ($student['is_officer'] ? 'text-blue-600' : '') ?> font-semibold">
                    <?= $student['f_name'] . ' ' . $student['l_name'] ?>
                </p>
            </td>
            <td class="py-2 pl-3"><?= $student['student_no'] ?></td>
            <td class="py-2 pl-3"><?= $year_dict[$student['year']] ?></td>
        </tr>
    <?php endforeach;
    $table_html = ob_get_clean();
} else {
    $table_html = "<tr><td colspan='3' class='py-2 text-center text-gray-500'>No results found</td></tr>";
}

ob_start();
if (count($students) > 0): ?>
    <div id="students-div" class="flex flex-col items-center w-full md:hidden">
        <?php foreach ($students as $student): ?>
            <div id="student-<?php echo $student['iduser'] ?>" data-student_no="<?php echo $student['student_no'] ?>"
                        data-f_name="<?php echo $student['f_name'] ?>" data-l_name="<?php echo $student['l_name'] ?>"
                        data-idprogram="<?php echo $student['idprogram_user'] ?>" data-year="<?php echo $student['year'] ?>"
                        data-email="<?php echo $student['email'] ?>"
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
                                echo 'text-orange-400';
                            } ?>">
                                <?php echo $student['f_name'] ?>         <?php echo $student['l_name'] ?>
                            </p>
                            <p id="student-no" class="text-gray-700"><?php echo $student['student_no'] ?></p>
                            <p id="program-yr-blck"><?php echo $student['program'] ?>         <?php echo $student['year'] ?></p>
                        </div>
                    </div>
        <?php endforeach; ?>
    </div>




<?php else: ?>
    <div id="students-div" class="flex flex-col items-center w-full mt-10 md:hidden">
        <p class="text-lg text-gray-500">No students found.</p>
    </div>
<?php endif; ?>

<?php 
$div_html = ob_get_clean();

echo json_encode([
    "table" => $table_html,
    "div" => $div_html,
    "results_count" => count($students),
]);
?>
