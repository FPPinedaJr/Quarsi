<?php
session_start();
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard -
        <?php
        if ($_SESSION['is_officer'] == 1) {
            echo "Officer";
        } elseif ($_SESSION['is_superuser'] == 1) {
            echo "President";
        } elseif ($_SESSION['is_admin'] == 1) {
            echo "Administrator";
        } else {
            echo "Student";
        }
        ?>
    </title>

    <link rel="icon" href="./assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
    <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="./assets/css/output.css?v=1.3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <script src="./assets/js/jquery-3.7.1.min.js"></script>
</head>
<?php
include_once("./includes/partial/sidebar.php");
include_once("./includes/connect_db.php");
include_once("./includes/partial/header.php");


$stmt = $pdo->query("
    SELECT 
        idorganization,
        name,
        short_name,
        program,
        abbreviation
    FROM organization ORDER BY name;
");
$rows = $stmt->fetchall(PDO::FETCH_ASSOC);


?>


<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <main class="w-5/6 p-6 bg-white rounded shadow">
        <h2 class="mb-4 text-2xl font-bold text-center">Organizations</h2>

        <div id="organizationGrid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <?php foreach ($rows as $row) { ?>
                <div class="p-4 bg-teal-200 rounded-lg shadow ">
                    <h3 class="text-lg font-bold"><?= $row['name'] ?> (<?= $row['short_name'] ?>)</h3>
                    <p><strong>Program:</strong> <?= $row['program'] ?></p>
                    <div class="mt-2 space-x-2">
                        <button
                            onclick="editOrganization(<?= $row['idorganization'] ?>, <?= $row['name'] ?>, <?= $row['short_name'] ?>, <?= $row['program'] ?>, <?= $row['abbreviation'] ?>)"
                            class="px-2 py-1 text-yellow-500 border border-teal-600 rounded hover:text-yellow-400 ">Edit <i
                                class="fa-solid fa-pencil"></i></button>
                        <button onclick="deleteOrganization(<?= $row['idorganization'] ?>, <?= $row['name'] ?>)"
                            class="px-2 py-1 text-red-500 bg-teal-200 border border-teal-600 rounded hover:text-red-400">Delete
                            <i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            <?php } ?>

            <button onclick="showOrgModal()"
                class="p-4 text-5xl font-bold text-blue-500 border border-blue-500 rounded-lg shadow hover:bg-teal-100">
                <i class="fa-solid fa-plus"></i></button>
        </div>
    </main>


    <!-- Modal -->
    <div id="orgModal" class="fixed inset-0 flex items-center justify-center invisible bg-gray-800 bg-opacity-50">
        <div id="orgModalMain" class="p-6 bg-white rounded shadow w-96">
            <h3 id="modalTitle" class="mb-4 text-xl font-bold">Add Organization</h3>
            <form id="organizationForm" class="space-y-4">
                <input type="hidden" id="id" name="id">
                <input type="text" id="name" name="name" placeholder="Name" required class="w-full p-2 border rounded">
                <input type="text" id="short_name" name="short_name" placeholder="Short Name" required
                    class="w-full p-2 border rounded">
                <input type="text" id="program" name="program" placeholder="Program" required
                    class="w-full p-2 border rounded">
                <input type="text" id="abbreviation" name="abbreviation" placeholder="Abbreviation" required
                    class="w-full p-2 border rounded">
                <button type="submit" class="w-full p-2 text-white bg-blue-500 rounded hover:bg-blue-600">Save</button>
            </form>
            <button onclick="hideOrgModal()"
                class="w-full p-2 mt-4 text-white bg-gray-500 rounded hover:bg-gray-600">Cancel</button>
        </div>
    </div>

    <script>

        function showOrgModal() {
            $('#orgModal').removeClass('invisible');
        }

        function hideOrgModal() {
            $('#orgModal').addClass('invisible');
            $('#organizationForm')[0].reset();
        }

        function editOrganization(id, name, short_name, program, abbreviation) {
            $('#id').val(id);
            $('#name').val(name);
            $('#short_name').val(short_name);
            $('#program').val(program);
            $('#abbreviation').val(abbreviation);
            $('#modalTitle').text("Edit Organization");
            showOrgModal();
        }

        function deleteOrganization(id, name) {
            if (confirm(`Are you sure you want to delete '${name}'?`)) {
                $.ajax({
                    url: 'crud_organization.php',
                    type: 'DELETE',
                    data: { id: id },
                    success: function () {
                        fetchOrganizations();
                    }
                });
            }
        }

        $(document).ready(function () {
            fetchOrganizations();

            $("#organizationForm").submit(function (event) {
                event.preventDefault();
                $.post('crud_organization.php', $(this).serialize(), function () {
                    fetchOrganizations();
                    hideOrgModal();
                });
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#orgModalMain').length && $(event.target).closest('#orgModal').length) {
                    hideOrgModal();
                }
            })
        });
    </script>
</body>

</html>