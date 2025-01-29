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
  <meta property="og:title" content="Quarsi">
  <meta property="og:description" content="ACS QR Attendance System">

  <title>Sign in | Quarsi</title>

  <link rel="icon" href="./assets/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="./assets/css/output.css?v=1.2">
  <link rel="stylesheet" href="./assets/css/fontawesome/all.min.css">
  <link rel="stylesheet" href="./assets/css/fontawesome/fontawesome.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap"
    rel="stylesheet">
  <script src="./assets/js/jquery-3.7.1.min.js"></script>
</head>



<body
  class="flex items-center justify-center h-screen overflow-hidden text-black md:bg-gradient-to-tr from-green-800 to-slate-200 ">
  <main class="flex justify-center w-4/6 text-black bg-transparent">
    <div class="flex justify-center p-2 text-center bg-white rounded-lg md:shadow-lg md:shadow-zinc-700/50 ">
      <div class="flex justify-center p-2 text-center bg-white rounded-lg w-96">
        <div class="w-full p-2">
          <div class="flex items-center justify-center w-full my-5 ">
            <img src="./assets/images/logo.png" alt="Logo" class="w-3/5 h-auto rounded-full ">
          </div>
          <h1 class="mt-5 text-4xl font-bold">SIGN IN</h1>

          <div class="flex justify-center w-full h-10">
            <div id="alert" role="alert" class="hidden w-2/3 mt-2">
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
            <input id="email" type="text" name="email" placeholder="Enter your email..."
              class="block w-full px-4 py-2 pr-12 mx-auto mt-1 text-black bg-white border-b-2 border-gray-300 focus:text-green-800 focus:border-b-2 focus focus:border-green-800 focus:outline-none focus:ring-0 input--main">
            <div class="relative mt-5">
              <input id="password" type="password" name="password" placeholder="Enter your password..."
                class="block w-full px-4 py-2 pr-12 mx-auto mt-1 text-black border-b-2 border-gray-300 focus:text-green-800 focus:border-b-2 focus focus:border-green-800 focus:outline-none focus:ring-0 input--main">
              <button type="button" id="show" class="absolute top-2 right-3"><i id="eyeIcon"
                  class="fas fa-eye"></i></button>
            </div>
            <div class="w-full flex justify-end text-sm mr-1">
              <p onclick='showForgotPassModal()' class="text-green-600 cursor-pointer hover:text-green-500 hover:underline">Forgot Password?</p>
            </div>

            <div class="h-10 mt-8 mb-3">
              <button type="submit" id="loginBtn"
                class="w-3/5 px-4 py-2 font-bold text-white bg-green-900 rounded-full md:bg-emerald-600 focus:outline-none focus:shadow-outline md:hover:bg-emerald-700">LOG
                IN</button>
              <div id="spinner" class="hidden mt-4 text-3xl text-emerald-700"><i class="fas fa-spinner fa-spin"></i>
              </div>
            </div>
          </form>
        </div>
      </div>
  </main>

  <!-- Submit OTP -->
  <div class="invisible fixed top-0 left-0 w-full h-full bg-gray-400/50 backdrop-blur-md flex justify-center items-center">
    <div class="flex items-center flex-col md:w-1/4 w-10/12 px-4 py-2 bg-white rounded-md">
      <div class="text-center font-bold text-3xl mt-3">OTP Verification</div>
      <!-- Input OTP -->
      <div class="w-3/4 h-fit flex justify-between mt-7 px-4">
        <input id="digit-1"
          class="border border-gray-400 rounded-md text-2xl w-10 h-10 bg-white shadow-lg text-center focus:outline-emerald-400"
          autocomplete="one-time-code" maxlength="1" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
        <input id="digit-2"
          class="border border-gray-400 rounded-md text-2xl w-10 h-10 bg-white shadow-lg text-center focus:outline-emerald-400"
          autocomplete="one-time-code" maxlength="1" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
        <input id="digit-3"
          class="border border-gray-400 rounded-md text-2xl w-10 h-10 bg-white shadow-lg text-center focus:outline-emerald-400"
          autocomplete="one-time-code" maxlength="1" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
        <input id="digit-4"
          class="border border-gray-400 rounded-md text-2xl w-10 h-10 bg-white shadow-lg text-center focus:outline-emerald-400"
          autocomplete="one-time-code" maxlength="1" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
      </div>
      <div class="mt-4 mb-2 text-xs text-zinc-500 w-3/4 text-center flex flex-wrap">
        Please enter the 4-digit one-time-password (OTP) we sent to your email to verify.
      </div>
      <button class="bg-emerald-500 py-1 text-md text-white mt-2 rounded-full w-[9rem] font-semibold hover:bg-emerald-600 mb-6" type="button">Submit</button>
    </div>
  </div>

<!-- Send OTP -->
  <div id="forgot_pass_modal" class="invisible fixed top-0 left-0 w-full h-full bg-gray-400/50 backdrop-blur-md flex justify-center items-center">
    <div id="forgot_pass_modal_main" class="flex items-center flex-col md:w-1/4 w-10/12 px-6 py-2 bg-white rounded-md">
      <div class="w-full h-fit py-1 font-bold text-3xl text-center">Forgot Password</div>
      <form action="send_otp.php" method="POST"  class="flex flex-col w-full mt-4">
        <label for="corp_email" class=" text-zinc-700">
          Enter your corporate email:
        </label>
        <input id="corp_email" type="email" name="email" 
        class="mt-2 w-full px-2 py-1 focus:outline-none rounded-md border border-teal-700">
        <input type="hidden" name="action" value="verify_email">
        <div class="flex justify-center mt-4 mb-2">
          <button id="email-btn" type="button" onclick="verifyEmail()" class="bg-emerald-500 py-1 text-md text-white mt-2 rounded-full w-[9rem] font-semibold hover:bg-emerald-600">Send OTP</button>
        </div>
      </form>
    </div>
  </div>




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

    function showForgotPassModal() {
      $('#forgot_pass_modal').removeClass('invisible');
    }

    function hideForgotPassModal() {
      $('#forgot_pass_modal').addClass('invisible');
    }

    function verifyEmail() {
      var $email = $('#corp_email').val();
      var $action = $('input[name=action]').val();
      console.log($email);
      console.log($action);
      $.ajax({
        url: "includes/send_otp.php",
        type: "POST",
        data: {
          email: $email,
          action: $action
        },
        success: function(response) {
          console.log(response);
            if (response === "exists") {
                alert("Email found! Sending OTP...");
                // $("#forgot_pass_modal form").submit();
            } else {
                alert("Email not registered!");
            }
        }
    });
    }


    $(document).ready(function () {
      $("input[id^='digit-']").on("keyup", function (e) {
        let currentInput = $(this);
        let currentValue = currentInput.val();

        if (e.key === "Backspace") {
          if (currentValue === "") {
            currentInput.prev("input").focus();
          }
        } else if (currentValue.length === 1) {
          currentInput.next("input").focus();
        }
      });

      $("input[id^='digit-']").on("input", function () {
        let currentInput = $(this);
        if (currentInput.val().length > 1) {
          currentInput.val(currentInput.val().charAt(0));
        }
      });

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

      $(document).on('click', function (event) {
        if (!$(event.target).closest('#forgot_pass_modal_main').length && $(event.target).closest('#forgot_pass_modal').length) {
          hideForgotPassModal();
        }
      })

    });
  </script>
</body>


</html>