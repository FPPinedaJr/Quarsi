<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {

    include_once("./includes/connect_db.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        $stmt = $pdo->prepare("SELECT password FROM user WHERE iduser = ?");
        $stmt->execute([$_SESSION['userid']]);
        $user_password = $stmt->fetchColumn();

        if (empty($current_password)) {
            $error_message = "Current password cannot be empty.";
        } elseif (empty($new_password)) {
            $error_message = "New password cannot be empty.";
        } elseif (empty($confirm_new_password)) {
            $error_message = "Confirm new password cannot be empty.";
        } elseif (strlen($new_password) < 8) {
            $error_message = "New password must be at least 8 characters.";
        } elseif (hash('sha256', $current_password) !== $user_password) {
            $error_message = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_new_password) {
            $error_message = "New passwords do not match.";
        } else {
            $new_password_hashed = hash('sha256', $new_password);
            $stmt = $pdo->prepare("UPDATE user SET password = ? WHERE iduser = ?");
            if ($stmt->execute([$new_password_hashed, $_SESSION['userid']])) {
                $success_message = "Password updated successfully.";
            } else {
                $error_message = "Failed to update password.";
            }
        }

        if (isset($error_message)) {
            header("Location: profile.php?error=" . urlencode($error_message));
        } elseif (isset($success_message)) {
            header("Location: profile.php?success=" . urlencode($success_message));
        }
        exit;
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">


    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile - <?php
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
        <link rel="stylesheet" href="./assets/css/output.css?v=1.1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
            rel="stylesheet">
        <script src="./assets/js/jquery-3.7.1.min.js"></script>
    </head>

    <?php
    include_once("./includes/partial/sidebar.php");
    include_once("./includes/partial/header.php");
    ?>

    <body class="flex justify-center w-screen min-h-screen mt-24 overflow-x-hidden">
        <main class="flex flex-col items-center w-full h-full py-8">
            <div class="relative w-48 h-48 overflow-hidden bg-gray-200 border border-teal-600 rounded-full">
                <img src="data:image/jpeg;base64,<?= base64_encode($user['profile_pic']) ?>" alt="Profile Picture"
                    class="object-cover w-full h-full">
            </div>

            <div class="mt-6 text-center">
                <p class="text-gray-600"> <?= htmlspecialchars($user['student_number']) ?></p>
                <h2 class="text-2xl font-bold"><?= htmlspecialchars($user['full_name']) ?></h2>
                <p class="text-gray-600"><?= htmlspecialchars($user['section']) ?></p>
                <p class="text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <div class="w-11/12 max-w-sm mt-8 md:w-full">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="p-2 mb-4 text-red-600 bg-red-100 rounded"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php } elseif (isset($_GET['success'])) { ?>
                    <div class="p-2 mb-4 text-green-600 bg-green-100 rounded"><?= htmlspecialchars($_GET['success']) ?></div>
                <?php } ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Current Password</label>
                        <input type="password" name="current_password" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700">New Password</label>
                        <input type="password" name="new_password" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700">Confirm New Password</label>
                        <input type="password" name="confirm_new_password"
                            class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <button type="submit"
                        class="w-full px-4 py-2 font-bold text-white bg-teal-500 rounded hover:bg-teal-600">Update
                        Password</button>
                </form>
            </div>
        </main>
    </body>

    <script>
        function changeHeaderTitle() {
            $('#header_title').text('Profile');
        }

        $(document).ready(function () {
            changeHeaderTitle();
        });
    </script>

    </html>
<?php } ?>