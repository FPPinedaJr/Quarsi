<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once("./includes/connect_db.php");

$stmt = $pdo->prepare("
SELECT 
    profile_pic,
    CONCAT(f_name, ' ', l_name) AS full_name,
    student_no AS student_number,
    CONCAT('Year ', year, ' Block ', block) AS section,
    email
FROM user
WHERE iduser = ?;
");
$stmt->execute([$_SESSION['userid']]);
$user = $stmt->fetch();
?>



<div id="sidebar" class="fixed inset-0 z-50 invisible w-full h-full">
  <div id="sidebar-overlay"
    class="fixed top-0 left-0 grid w-full min-h-screen p-4 place-items-center backdrop-blur-sm backdrop-opacity-10 backdrop-invert bg-black/30">
    <div id="sidebar-content"
      class="flex flex-col overflow-y-auto  bg-[#f5f5f5] h-full w-9/12 md:w-72 fixed left-0 duration-300 ease-out transition-all transform -translate-x-full">
      <div class="flex items-center justify-center px-5 mx-3 border-b-2 border-gray-700 text-4x min-h-16">
        <div class="flex items-center justify-center w-16 my-5 ">
          <img src="./assets/images/quarsi-logo.png" alt="Logo" class="w-3/5 h-auto rounded-full ">
        </div>
        <a href="home.php" class="font-['mulish'] text-black font-bold text-3xl mr-4 my-auto">Quarsi</a>
      </div>

      <!-- profile -->
      <div class="flex flex-col items-center my-3">
        <div class="w-32 h-32 overflow-hidden border border-gray-400 rounded-full">
          <img src="data:image/jpeg;base64,<?= base64_encode($user['profile_pic']) ?>" alt="Profile Picture"
            class="object-cover w-full h-full">
        </div>
        <div class="mt-2">
          <h3 class="font-semibold font-['merriweather_sans'] text-2xl text-center"><?php echo $user['full_name'] ?>
          </h3>
        </div>
        <div class="">
          <h3 class="font-['merriweather_sans'] text-lg text-center">
            <?php
            if ($_SESSION['is_admin'] == 1) {
              echo 'administrator';
            } else if ($_SESSION['is_superuser'] == 1) {
              echo 'superuser';
            } else if ($_SESSION['is_officer'] == 1) {
              echo 'officer';
            } else {
              echo 'student';
            }
            ?>

          </h3>
        </div>
      </div>
      <h2 class="px-3 py-1 mt-3 font-bold">Navigation</h2>

      <!-- navigation -->
      <div class="flex flex-col flex-grow mb-3 space-y-1 font-semibold">
        <a href="dashboard.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <div class="flex justify-center w-8">
            <i class="text-2xl fa-solid fa-table-columns"></i>
          </div>
          <span class="font-['merriweather_sans'] ml-3">Dashboard</span>
        </a>
        <a href="qr.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <div class="flex justify-center w-8">
            <i class="text-2xl fa-solid fa-qrcode"></i>
          </div>
          <span class="font-['merriweather_sans'] ml-3">Generate QR</span>
        </a>
        <a href="student.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <div class="flex justify-center w-8">
            <i class="text-2xl fa-solid fa-user-graduate"></i>
          </div>
          <span class="font-['merriweather_sans'] ml-3">Students</span>
        </a>
        <a href="officer.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <div class="flex justify-center w-8">
            <i class="text-2xl fa-solid fa-users"></i>
          </div>
          <span class="font-['merriweather_sans'] ml-3">Officers</span>
        </a>

        <?php
        if ($_SESSION['is_officer'] == 1 || $_SESSION['is_superuser'] == 1 || $_SESSION['is_admin'] == 1) {
          echo '
              <a href="scanner.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
                <div class="flex justify-center w-8">
                  <i class="text-2xl fa-solid fa-expand"></i>
                </div>
                <span class="font-[\'merriweather_sans\'] ml-3">QR scanner</span>
              </a>
              ';
          }

        if ($_SESSION['is_superuser'] == 1 || $_SESSION['is_admin'] == 1) {
          echo '
            <a href="events.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
              <div class="flex justify-center w-8">
                <i class="text-2xl fa-solid fa-calendar"></i>
              </div>
              <span class="font-[\'merriweather_sans\'] ml-3">Events</span>
            </a>
            <a href="statistics.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
              <div class="flex justify-center w-8">
                <i class="text-2xl fa-solid fa-chart-column"></i>
              </div>
              <span class="font-[\'merriweather_sans\'] ml-3">Statistics</span>
            </a>'
            ;
          }
        ?>

        <a href="profile.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <div class="flex justify-center w-8">
            <i class="text-2xl fa-regular fa-user"></i>
          </div>
          <span class="font-['merriweather_sans'] ml-3">Profile</span>
        </a>

        <a href="game" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <div class="flex justify-center w-8">
            <i class="text-2xl fa-solid fa-gamepad"></i>
          </div>
          <span class="font-['merriweather_sans'] ml-3">Have some fun!</span>
        </a>

        <a href="./includes/logout.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <div class="flex justify-center w-8">
            <i class="text-2xl fa-solid fa-arrow-right-from-bracket"></i>
          </div>
          <span class="font-['merriweather_sans'] ml-3">Log Out</span>
        </a>
      </div>

    </div>
  </div>
</div>

<script src="./assets/js/jquery-3.7.1.min.js"></script>
<script>
  let touchStartX = 0;
  let touchEndX = 0;
  let touchStartY = 0;
  let touchEndY = 0;
  const swipeThreshold = 50;

  function handleGesture() {
    const deltaX = touchEndX - touchStartX;
    const deltaY = touchEndY - touchStartY;

    if (Math.abs(deltaX) > Math.abs(deltaY)) {
      if (deltaX > swipeThreshold) {
        openSidebar();
      } else if (deltaX < -swipeThreshold) {
        closeSidebar();
      }
    }
  }

  document.addEventListener('touchstart', function (event) {
    touchStartX = event.changedTouches[0].screenX;
    touchStartY = event.changedTouches[0].screenY;
  });

  document.addEventListener('touchend', function (event) {
    touchEndX = event.changedTouches[0].screenX;
    touchEndY = event.changedTouches[0].screenY;
    handleGesture();
  });

  function toggleSidebar() {
    $('#sidebar').toggleClass('invisible');
    $('#sidebar-content').toggleClass('-translate-x-full');
    $('body').toggleClass('overflow-y-hidden');
  }

  function openSidebar() {
    $('#sidebar').removeClass('invisible');
    $('#sidebar-content').removeClass('-translate-x-full');
    $('body').addClass('overflow-y-hidden');
  }

  function closeSidebar() {
    $('#sidebar').addClass('invisible');
    $('#sidebar-content').addClass('-translate-x-full');
    $('body').removeClass('overflow-y-hidden');
  }

  $(document).ready(function () {
    $('#sidebar-overlay').on('click', function (event) {
      if (event.target === this) {
        closeSidebar();
      }
    });
  });

</script>