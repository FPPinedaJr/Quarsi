<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['logged_in'])) {
  if ($_SESSION['logged_in'] == true) {
    header('location: ./dashboard.php');
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta property="og:title" content="tot-tot">
  <meta property="og:description" content="tot-tot [description]">

  <title>Sign in | tot-tot</title>

  <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="./assets/css/output.css">
  <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
  <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
  <script src="./assets/js/jquery-3.7.1.min.js"></script>
</head>



<body class="flex items-center justify-center h-screen text-black bg-orange-200">
  <main class="flex justify-center w-11/12 text-black bg-transparent">
    <div class="flex justify-center p-2 text-center bg-white rounded-lg w-96">
    <div class="flex justify-center p-2 text-center bg-white rounded-lg w-96">
      <div class="w-full p-2">
        <div class="flex items-center justify-center w-full my-5 ">
          <img src="./assets/images/logo.png" alt="Logo" class="w-3/5 h-auto rounded-full ">
        </div>
        <h1 class="mt-5 text-4xl font-bold">SIGN IN</h1>

        <div class="w-full h-10">
          <div id="alert" role="alert" class="hidden">
            <div
              class="bottom-0 flex items-center px-3 py-1 text-red-800 bg-red-100 border-2 border-red-700 rounded-full ">
              <i class="fa-solid fa-circle-exclamation"></i>
              <div class="pr-1 text-sm font-medium ms-3">
                <p id="err_msg"></p>
              </div>
              <button type="button"
                class="ms-auto -mx-1.5 -my-1.5  text-red-600 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8  aria-label=Close">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </div>
        </div>

        <form id="loginForm" action="./includes/authenticate.php" type="button" method="POST" class="w-full px-10">
          <div class="flex "><label for="email">Email: </label></div>
          <input id="email" type="text" name="email" placeholder="Enter your email..."
            class="block w-full px-4 py-2 pr-12 mx-auto mt-1 text-black bg-white border-b-2 border-gray-300 focus:text-green-800 focus:border-b-2 focus focus:border-green-800 focus:outline-none focus:ring-0 input--main">
          <div class="relative">
            <div class="flex mt-3"><label for="password">Password: </label></div>
            <input id="password" type="password" name="password" placeholder="Enter your password..."
              class="block w-full px-4 py-2 pr-12 mx-auto mt-1 text-black border-b-2 border-gray-300 focus:text-green-800 focus:border-b-2 focus focus:border-green-800 focus:outline-none focus:ring-0 input--main">
            <button type="button" id="show" class="absolute top-8 right-3"><i id="eyeIcon"
                class="fas fa-eye"></i></button>
          </div>

          <div class="h-10 mt-6">
            <button type="submit" id="loginBtn"
              class="w-3/5 px-4 py-2 font-bold text-white bg-green-900 rounded-full focus:outline-none focus:shadow-outline md:hover:bg-green-950">LOG
              IN</button>
            <div id="spinner" class="hidden mt-4 text-3xl text-orange-800"><i class="fas fa-spinner fa-spin"></i>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>



  <script>
    function resetComponent() {
      $("#password").attr("type", "password")
      $("#eyeIcon").removeClass("fa-eye-slash").addClass("fa-eye");

      $("#loginBtn").removeClass("hidden");
      $("#spinner").addClass("hidden");

      $("#pass1Reg").attr("type", "password")
      $("#pass2Reg").attr("type", "password")

      $('#show-password').prop('checked', false).change();

      $("#registerBtn").removeClass("hidden");
      $("#spinnerReg").addClass("hidden");

    }


    $(document).ready(function () {
      $('#show-password').change(function () {
        const passwordInput1 = $('#pass1Reg');
        const passwordInput2 = $('#pass2Reg');
        const type = this.checked ? 'text' : 'password';
        passwordInput1.attr('type', type);
        passwordInput2.attr('type', type);
      });


      $("#alert button").click(function () {
        $("#alert").addClass("hidden");
      });


      $("#show").click(function () {
        var passwordInput = $("#password");
        var eyeIcon = $("#eyeIcon");
        if (passwordInput.attr("type") === "password") {
          passwordInput.attr("type", "text");
          eyeIcon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
          passwordInput.attr("type", "password");
          eyeIcon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
      });


      $("#loginForm").submit(function (e) {
        e.preventDefault();

        $("#loginBtn").addClass("hidden");
        $("#spinner").removeClass("hidden");
        $("#alert").addClass("hidden");

        var email = $("#email").val().trim();
        var password = $("#password").val().trim();

        $.ajax({
          url: $(this).attr('action'),
          method: $(this).attr('method'),
          data: {
            email: email,
            password: password
          },
          success: function (response) {
            console.log(response);

            if (response === "success") {
              window.location.href = "./dashboard.php";
              resetComponent();
            }
            else if (response === "err_empty_email") {
              $("#alert").removeClass("hidden");
              $("#err_msg").text("Enter your Email");
              resetComponent();
            }
            else if (response === "err_empty_password") {
              $("#err_msg").text("Enter your Password");
              $("#alert").removeClass("hidden");
              resetComponent();
            }
            else if (response === "err_password") {
              $("#alert").removeClass("hidden");
              $("#err_msg").text("Incorrect Password");
              resetComponent();
            }
            else if (response === "err_not_registered") {
              $("#alert").removeClass("hidden");
              $("#err_msg").text("Email not registered");
              resetComponent();
            }
          }
        });
      });



    });
  </script>
</body>


</html>