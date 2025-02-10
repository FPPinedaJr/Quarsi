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

// Ensure `page` is always an integer
$page = isset($_POST['page']) && is_numeric($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

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
$query .= " ORDER BY is_superuser DESC, is_officer DESC, user.year, user.block, user.f_name LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total students count (needed for pagination)
$count_query = "SELECT COUNT(*) FROM user WHERE is_admin <> 1";
$stmt_count = $pdo->prepare($count_query);
$stmt_count->execute();
$total_students = $stmt_count->fetchColumn();
$total_pages = ceil($total_students / $limit);

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
            <td class="py-2 pl-3 flex items-center">
                <img data-src="<?= 'data:image/jpeg;base64,'. base64_encode($student['profile_pic']) ?>" 
                    class="rounded-full mr-2 border border-gray-400 w-6 h-6 lozad">
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
    $table_html = "<tr><td colspan='3' class='text-center text-gray-500 py-2'>No results found</td></tr>";
}

// Generate div section
ob_start();
if (count($students) > 0): ?>
    <div id="students-div" class="flex flex-col w-full items-center md:hidden mt-10">
        <?php foreach ($students as $student): ?>
            <div class="flex student-div items-center p-1 border-2 border-gray shadow-md w-full h-36 rounded-md bg-[#d9f0ea] my-3 overflow-hidden">
                <div class="w-1/3 h-full flex items-center justify-center">
                    <img data-src="<?= 'data:image/jpeg;base64,'. base64_encode($student['profile_pic']) ?>" class="w-16 h-16 rounded-full border border-gray-200 lozad">
                </div>
                <div class="w-2/3 h-full pl-2 p-1 flex justify-center flex-col text-nowrap">
                    <p class="font-semibold text-xl"><?= $student['f_name'] ?> <?= $student['l_name'] ?></p>
                    <p class="text-gray-700"><?= $student['student_no'] ?></p>
                    <p><?= $student['program'] ?> <?= $student['year'] ?> Block <?= $student['block'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div id="students-div" class="flex flex-col w-full items-center md:hidden mt-10">
        <p class="text-gray-500 text-lg">No students found.</p>
    </div>
<?php endif; ?>

<?php 
$div_html = ob_get_clean();

// Return JSON response
echo json_encode([
    "table" => $table_html,
    "div" => $div_html,
    "total_pages" => $total_pages
]);
?>
