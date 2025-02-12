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
    <title>Organizations -
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


<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100 ">
    <main class="w-5/6 min-h-screen p-6 pt-24 rounded ">

        <div id="organizationGrid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <?php foreach ($rows as $row) { ?>
                <div class="p-4 bg-teal-200 rounded-lg shadow " data-id="<?= $row['idorganization'] ?>"
                    data-name="<?= $row['name'] ?>" data-short_name="<?= $row['short_name'] ?>"
                    data-program="<?= $row['program'] ?>" data-abbreviation="<?= $row['abbreviation'] ?>">
                    <h3 class="text-lg font-bold"><?= $row['name'] ?> (<?= $row['short_name'] ?>)</h3>
                    <p><strong>Program:</strong> <?= $row['program'] ?></p>
                    <div class="mt-2 space-x-2">
                        <button onclick="showEditModal(this)"
                            class="px-2 py-1 text-white bg-blue-500 border rounded hover:bg-blue-400">
                            Edit <i class="fa-solid fa-pencil"></i>
                        </button>

                        <button onclick="showDeleteModal(this)"
                            class="px-2 py-1 text-white bg-red-500 rounded hover:bg-red-400">Delete
                            <i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            <?php } ?>

            <button onclick="showOrgModal()"
                class="p-4 text-5xl font-bold text-teal-400 border border-teal-400 rounded-lg shadow hover:text-teal-500 hover:border-teal-500">
                <i class="fa-solid fa-plus"></i></button>
        </div>
    </main>


    <div id="orgModal" class="fixed inset-0 flex items-center justify-center invisible bg-gray-800 bg-opacity-50">
        <div id="orgModalMain" class="p-6 bg-white rounded shadow w-96">
            <h3 id="modalTitle" class="mb-4 text-xl font-bold text-teal-700"><span id="add_edit">Add</span> Organization
            </h3>
            <form id="organizationForm">
                <div class="space-y-2">

                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="method" name="method">

                    <label for="name" class="block mt-1 font-medium text-teal-700">Name:</label>
                    <input type="text" id="name" name="name" placeholder="Name" required autocomplete="off"
                        class="w-full p-2 border border-teal-500 rounded focus:outline-none focus:ring-2 focus:ring-teal-500">

                    <label for="short_name" class="block mt-1 font-medium text-teal-700">Short Name:</label>
                    <input type="text" id="short_name" name="short_name" placeholder="Short Name" required
                        autocomplete="off"
                        class="w-full p-2 border border-teal-500 rounded focus:outline-none focus:ring-2 focus:ring-teal-500">

                    <label for="program" class="block mt-1 font-medium text-teal-700">Program:</label>
                    <input type="text" id="program" name="program" placeholder="Program" required autocomplete="off"
                        class="w-full p-2 border border-teal-500 rounded focus:outline-none focus:ring-2 focus:ring-teal-500">

                    <label for="abbreviation" class="block mt-1 font-medium text-teal-700">Abbreviation:</label>
                    <input type="text" id="abbreviation" name="abbreviation" placeholder="Abbreviation" required
                        autocomplete="off"
                        class="w-full p-2 border border-teal-500 rounded focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <button onclick="submitData()" class="w-full p-2 mt-4 text-white bg-teal-500 rounded hover:bg-teal-600">
                    Confirm
                </button>
            </form>
            <button onclick="hideOrgModal()"
                class="w-full p-2 mt-2 text-gray-500 bg-white border border-gray-500 rounded hover:bg-gray-600">Cancel</button>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center invisible bg-gray-800 bg-opacity-50">
        <div id="deleteModalMain" class="p-6 bg-white rounded shadow w-96">
            <h3 id="modalTitle" class="mb-4 text-xl font-bold text-teal-700">Delete Organization</h3>
            <form id="deleteForm" class="space-y-4">
                <p class="text-sm text-teal-700">Are you sure to delete organization "<span id="organization_to_delete"
                        class="font-semibold text-teal-500"></span>"?</p>

                <div class="flex justify-end">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-4 py-2 text-gray-400 bg-white border border-gray-400 rounded hover:bg-gray-600">Cancel</button>
                    <button onclick="submitData()"
                        class="px-4 py-2 ml-3 text-white bg-red-500 rounded hover:bg-red-600">Delete</button>
                </div>
            </form>
        </div>
    </div>


    <script>

        function showOrgModal(text) {
            $('#add_edit').text('Add');
            $('#method').val('add');

            $('#orgModal').removeClass('invisible');
        }

        function hideOrgModal() {
            $('#orgModal').addClass('invisible');
            $('#orgModal form')[0].reset();
        }

        function showEditModal(button) {

            const div = $(button).closest('div[data-id]');
            $('#method').val('edit');

            const id = $(div).data('id');
            const name = $(div).data('name');
            const short_name = $(div).data('short_name');
            const program = $(div).data('program');
            const abbreviation = $(div).data('abbreviation');

            $('#id').val(id);
            $('#name').val(name);
            $('#short_name').val(short_name);
            $('#program').val(program);
            $('#abbreviation').val(abbreviation);

            showOrgModal();
            $('#add_edit').text('Edit');
            $('#method').val('edit');

        }

        function showDeleteModal(button) {
            const div = $(button).closest('div[data-id]');

            const id = div.data('id');
            const name = div.data('name');

            $('#id').val(id);
            $('#method').val('delete');

            $('#organization_to_delete').text(name);
            $('#deleteModal').removeClass('invisible');
        }



        function hideDeleteModal() {
            $('#deleteModal').addClass('invisible');
        }



        function submitData() {
            let method = $('#method').val().trim();
            let id = $('#id').val().trim();
            let name = $('#name').val().trim();
            let short_name = $('#short_name').val().trim();
            let program = $('#program').val().trim();
            let abbreviation = $('#abbreviation').val().trim();




            let requestData = { id, name, short_name, program, abbreviation };

            switch (method) {
                case 'add':
                    if (!name || !short_name || !program || !abbreviation) {
                        return;
                    }
                    showLoader('Creating Organization...');
                    $.ajax({
                        url: 'includes/crud_organization.php',
                        type: 'POST',
                        data: { ...requestData, method: 'add' },
                        success: function (response) {
                            // alert(response);
                            location.reload();
                        }
                    });
                    break;

                case 'edit':
                    if (!name || !short_name || !program || !abbreviation) {
                        return;
                    }
                    showLoader('Saving...');
                    $.ajax({
                        url: 'includes/crud_organization.php',
                        type: 'POST',
                        data: { ...requestData, method: 'edit' },
                        success: function (response) {
                            // alert(response);
                            location.reload();
                        }
                    });
                    break;

                case 'delete':
                    showLoader('Deleting...');
                    $.ajax({
                        url: 'includes/crud_organization.php',
                        type: 'POST',
                        data: { id: id, method: 'delete' },
                        success: function (response) {
                            // alert(response);
                            location.reload();
                        }
                    });
                    break;

                default:
                    alert("Invalid method!");
                    break;
            }
        }









        $(document).ready(function () {
            $('#header_title').text('Organizations');
            $(document).on('click', function (event) {
                if ($(event.target).is('#orgModal')) {
                    hideOrgModal();
                }
                if ($(event.target).is('#deleteModal')) {
                    hideDeleteModal();
                }
            });
        });
    </script>
</body>
<?php include_once("./includes/partial/footer.php"); ?>

</html>