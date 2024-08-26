<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

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
include_once("./includes/connect_db.php");
include_once("./includes/partial/header.php");
?>

<body class="flex justify-center w-screen min-h-screen mt-24 overflow-hidden">
    <main class="flex flex-col items-center w-full h-full ">
        <?php
        $stmt = $pdo->prepare("
            SELECT total_points FROM user WHERE iduser = ?;
        ");
        $stmt->execute([$_SESSION['userid']]);
        $row = $stmt->fetch();
        ?>

        <!-- Points Div -->
        <div class="absolute top-0 w-full py-16 pt-32 text-2xl text-center bg-teal-300/50">
            <h1><span class="text-5xl font-bold"><?= $row['total_points'] ?><span> <span class="text-sm">pts</span></h1>
        </div>

        <!-- Table Div -->
        <div class="w-full max-w-sm overflow-y-auto mt-36">
            <?php
            $stmt = $pdo->prepare("
               SELECT 
                    e.name,
                    a.morning_in,
                    a.morning_out,
                    a.afternoon_in,
                    a.afternoon_out,
                    a.points
                FROM attendance a 
                INNER JOIN event e on a.event = e.idevent
                WHERE user = ?;
            ");
            $stmt->execute([$_SESSION['userid']]);
            $row = $stmt->fetch();

            if ($row) {
                ?>
                <table class="w-full text-center border-collapse">
                    <thead>
                        <tr>
                            <th class="p-2 text-left">Event</th>
                            <th class="p-2">Morning In</th>
                            <th class="p-2">Morning Out</th>
                            <th class="p-2">Afternoon In</th>
                            <th class="p-2">Afternoon Out</th>
                            <th class="p-2">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 text-left"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="p-2">
                                <span
                                    class="<?= $row['morning_in'] === '00:00:00' ? 'bg-red-500' : ($row['morning_in'] ? 'bg-green-500' : 'bg-gray-300') ?> block w-6 h-6 rounded"></span>
                            </td>
                            <td class="p-2">
                                <span
                                    class="<?= $row['morning_out'] === '00:00:00' ? 'bg-red-500' : ($row['morning_out'] ? 'bg-green-500' : 'bg-gray-300') ?> block w-6 h-6 rounded"></span>
                            </td>
                            <td class="p-2">
                                <span
                                    class="<?= $row['afternoon_in'] === '00:00:00' ? 'bg-red-500' : ($row['afternoon_in'] ? 'bg-green-500' : 'bg-gray-300') ?> block w-6 h-6 rounded"></span>
                            </td>
                            <td class="p-2">
                                <span
                                    class="<?= $row['afternoon_out'] === '00:00:00' ? 'bg-red-500' : ($row['afternoon_out'] ? 'bg-green-500' : 'bg-gray-300') ?> block w-6 h-6 rounded"></span>
                            </td>
                            <td class="p-2 text-right <?= $row['points'] < 0 ? 'text-red-500' : '' ?>"><?= $row['points'] ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="w-full h-full mt-10 text-center text-gray-500">No attendance recorded</div>
            <?php } ?>
        </div>
    </main>
</body>





</html>