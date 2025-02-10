<?php
session_start();
include_once "./includes/connect_db.php";

// Enable error reporting for debugging (Remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get filters from POST request
$year_filter = isset($_POST['years']) && is_array($_POST['years']) ? $_POST['years'] : [];
$block_filter = isset($_POST['blocks']) && is_array($_POST['blocks']) ? $_POST['blocks'] : [];
$org_filter = isset($_POST['org']) && is_array($_POST['org']) ? $_POST['org'] : [];


$query = "
    SELECT 
        user.iduser, user.student_no, user.f_name, user.l_name, 
        organization.abbreviation AS program, user.organization AS idprogram_user, 
        user.year, user.block, user.email, user.is_officer, user.is_superuser, 
        user.is_admin, user.profile_pic
    FROM user
    INNER JOIN organization ON user.organization = organization.idorganization
    WHERE user.is_admin <> 1"; // Exclude admins

$params = [];

// Apply Filters
if (!empty($org_filter)) {
    $placeholders = implode(',', array_fill(0, count($org_filter), '?'));
    $query .= " AND user.organization IN ($placeholders)";
    $params = array_merge($params, $org_filter);
}

if (!empty($year_filter)) {
    $placeholders = implode(',', array_fill(0, count($year_filter), '?'));
    $query .= " AND user.year IN ($placeholders)";
    $params = array_merge($params, $year_filter);
}

if (!empty($block_filter)) {
    $placeholders = implode(',', array_fill(0, count($block_filter), '?'));
    $query .= " AND user.block IN ($placeholders)";
    $params = array_merge($params, $block_filter);
}

// Add pagination
$query .= " ORDER BY is_superuser DESC, is_officer DESC, user.year, user.block, user.f_name ";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total students count (needed for pagination)
$count_query = "SELECT COUNT(*) FROM user WHERE is_admin <> 1";
$stmt_count = $pdo->prepare($count_query);
$stmt_count->execute();
$total_students = $stmt_count->fetchColumn();

// Ensure correct JSON response type
header('Content-Type: application/json');

// Generate table rows
ob_start();
if (count($students) > 0) {
    foreach ($students as $student): ?>
        <tr id="student-<?= $student['iduser'] ?>"
            data-student_no="<?= $student['student_no'] ?>" 
            data-f_name="<?= $student['f_name'] ?>" 
            data-l_name="<?= $student['l_name'] ?>" 
            data-idprogram="<?= $student['idprogram_user'] ?>" 
            data-year="<?= $student['year'] ?>" 
            data-block="<?= $student['block'] ?>" 
            data-email="<?= $student['email'] ?>" 
            data-profile_pic="<?= base64_encode($student['profile_pic']) ?>"
            data-user_type="<?= ($student['is_officer'] ? '1' : ($student['is_superuser'] ? '2' : ($student['is_admin'] ? '3' : '0'))) ?>"
            class="cursor-pointer hover:bg-gray-300 even:bg-[#EDF4F2] odd:bg-gray-200" 
            onclick="showEditStudentModal(<?= $student['iduser'] ?>)">
            <td class="flex items-center py-2 pl-3">
                <img data-src="<?= 'data:image/jpeg;base64,'. base64_encode($student['profile_pic']) ?>" 
                    class="w-6 h-6 mr-2 border border-gray-400 rounded-full lozad">
                <p class="<?= ($student['is_superuser'] && $student['is_officer']) ? 'text-violet-500' : ($student['is_officer'] ? 'text-blue-600' : '') ?> font-semibold">
                    <?= $student['f_name'] . ' ' . $student['l_name'] ?>
                </p>
            </td>
            <td class="py-2 pl-3"><?= $student['student_no'] ?></td>
            <td class="py-2 pl-3"><?= $student['program'] ?> <?= $student['year'] ?> Block <?= $student['block'] ?></td>
        </tr>
    <?php endforeach;
    $table_html = ob_get_clean();
} else {
    $table_html = "<tr><td colspan='3' class='py-2 text-center text-gray-500'>No results found</td></tr>";
}

// Generate div section
ob_start();
if (count($students) > 0): ?>
    <div id="students-div" class="flex flex-col items-center w-full mt-10 md:hidden">
        <?php foreach ($students as $student): ?>
            <div class="flex student-div items-center p-1 border-2 border-gray shadow-md w-full h-36 rounded-md bg-[#d9f0ea] my-3 overflow-hidden">
                <div class="flex items-center justify-center w-1/3 h-full">
                    <img data-src="<?= 'data:image/jpeg;base64,'. base64_encode($student['profile_pic']) ?>" class="w-16 h-16 border border-gray-200 rounded-full lozad">
                </div>
                <div class="flex flex-col justify-center w-2/3 h-full p-1 pl-2 text-nowrap">
                    <p class="text-xl font-semibold"><?= $student['f_name'] ?> <?= $student['l_name'] ?></p>
                    <p class="text-gray-700"><?= $student['student_no'] ?></p>
                    <p><?= $student['program'] ?> <?= $student['year'] ?> Block <?= $student['block'] ?></p>
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

// Return JSON response
echo json_encode([
    "table" => $table_html,
    "div" => $div_html,
    "results_count" => count($students),
]);
?>
