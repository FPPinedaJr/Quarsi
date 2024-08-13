<div id="sidebar" class="fixed inset-0 z-50 invisible w-full h-full">
  <div id="sidebar-overlay"
    class="fixed top-0 left-0 grid w-full min-h-screen p-4 place-items-center backdrop-blur-sm backdrop-opacity-10 backdrop-invert bg-black/30">
    <div id="sidebar-content" class="flex flex-col bg-[#f5f5f5] h-full w-9/12 md:w-72 fixed left-0 duration-300 ease-out transition-all transform -translate-x-full">
      <div class="flex items-center justify-between px-5 h-16 text-4xl bg-[#ecd894] ">
        <a href="home.php" class="font-['cookie'] text-[#000000d5] font-bold text-5xl my-auto">Plushie Card</a>
        <a onclick="toggleSidebar()" class="hover:text-[#6a6b3a] text-2xl cursor-pointer"><i class="fa-solid fa-angles-left"></i></a>
      </div>
      <!-- profile -->
      <div class="flex flex-col items-center my-3">
        <div class="w-32 h-32 overflow-hidden border border-gray-400 rounded-full">
          <img class="object-cover w-full h-full" alt="profile">
        </div>
        <div class="mt-2">
          <h3 class="font-semibold font-['merriweather_sans'] text-2xl text-center"><?php echo $_SESSION['username'] ?>
          </h3>
        </div>
        <div class="">
          <h3 class="font-['merriweather_sans'] text-lg text-center">User type</h3>
        </div>
      </div>
      <h2 class="px-3 py-1 mt-3 font-bold">Navigation</h2>
      <!-- navigation -->
      <div class="flex flex-col flex-grow mb-3 space-y-1 font-semibold">
        <a href="home.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <i class="text-2xl fa-solid fa-house "></i>
          <span class="font-['merriweather_sans'] ml-5">Home</span>
        </a>
        <a href="" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <i class="text-2xl fa-solid fa-magnifying-glass"></i>
          <span class="font-['merriweather_sans'] ml-5">Advanced Search</span>
        </a>
        <a href="" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <i class="text-2xl fa-solid fa-user "></i>
          <span class="font-['merriweather_sans'] ml-5">Profile</span>
        </a>

        <!-- fixed to the bottom -->
        <div class="flex-grow"></div>
        <a href="#" class="flex items-center px-5 hover:bg-[#d8d8d8] cursor-pointer py-3">
          <i class="text-2xl fa-solid fa-circle-info"></i>
          <span class="font-['merriweather_sans'] ml-5">About</span>
        </a>
        <a href="../__includes__/logout.php" class="hover:bg-[#d8d8d8] cursor-pointer flex items-center px-5 py-3">
          <i class="text-2xl text-black fa-solid fa-arrow-right-from-bracket"></i>
          <span class=" text-black font-['merriweather_sans'] ml-5">Log Out</span>
        </a>
      </div>
    </div>
  </div>
</div>

<script src="./js/jquery-3.7.1.min.js"></script>
<script>
  function toggleSidebar() {
    $('#sidebar').toggleClass('invisible');
    $('#sidebar-content').toggleClass('-translate-x-full');
    $('body').toggleClass('overflow-y-hidden'); // Toggle overflow-y-hidden on the body
    console.log('here')
  }

  $(document).ready(function () {
    $('#sidebar-overlay').on('click', function (event) {
      if (event.target === this) {
        toggleSidebar();
      }
    });
  });
</script>
