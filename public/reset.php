<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION["logged_in"] == !true) {
    header("Location: index.php");
} else {
    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Password Reset - <?php
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

    <body class="flex justify-center w-screen min-h-screen mt-24 overflow-x-hidden md:items-center">
        <main class="flex flex-col items-center justify-center w-full h-full">
            <div class="w-full max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h2 class="mb-6 text-2xl font-bold text-center" id="header_title">Reset Password</h2>

                <form id="resetPasswordForm" class="space-y-6">
                    <div>
                        <label for="student_no" class="block text-sm font-medium text-gray-700">Student Number</label>
                        <input type="text" id="student_no" name="student_no"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                            pattern="\d{4}-\d{1,2}-\d{4}[\dA-Za-z]{0,2}" placeholder="2000-1-0001" required>
                    </div>
                    <div>
                        <button type="submit" id="submitButton"
                            class="relative flex justify-center w-full px-4 py-2 font-bold text-white bg-teal-600 rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                            <span id="buttonText">Reset Password</span>
                            <svg id="spinner" class="hidden w-5 h-5 ml-2 text-white animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <div id="response" class="mt-4 text-sm font-medium text-center text-indigo-500"></div>
            </div>
        </main>

    </body>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function changeHeaderTitle() {
            $('#header_title').text('Password Reset');
        }

        $(document).ready(function () {
            changeHeaderTitle();

            $('#resetPasswordForm').submit(function (event) {
                event.preventDefault();

                const student_no = $('#student_no').val();
                const submitButton = $('#submitButton');
                const buttonText = $('#buttonText');
                const spinner = $('#spinner');

                buttonText.addClass('hidden'); 
                spinner.removeClass('hidden'); 
                submitButton.attr('disabled', true); 

                $.ajax({
                    type: 'POST',
                    url: 'includes/reset_password.php',
                    data: { student_no: student_no },
                    success: function (response) {
                        $('#response').text(response);
                        $('#response').addClass('');
                        $('#response').removeClass(response);
                    },
                    error: function () {
                        $('#response').text('An error occurred while resetting the password.');
                    },
                    complete: function () {
                        buttonText.removeClass('hidden'); 
                        spinner.addClass('hidden'); 
                        submitButton.attr('disabled', false); 
                    }
                });
            });
        });
    </script>



    </html>
<?php } ?>