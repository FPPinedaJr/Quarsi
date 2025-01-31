<header class="fixed top-0 left-0 right-0 z-20 flex justify-between w-full h-16 p-2 bg-teal-700 shadow-md align-center">
    <div class="flex items-center w-full min-h-full px-2 py-1 my-auto md:w-5/12 md:px-4 md:text-center">
        <a onclick="toggleSidebar()"
            class="md:mr-5 text-2xl md:text-4xl md:text-center hover:text-[#6a6b3a] cursor-pointer">

            <i class="text-white fa fa-bars" aria-hidden="true"></i></a>
        <div class="flex justify-start pl-4 text-center min-w-40 md:w-96 md:ml-8 md:mr-2">
            <h1 class="font-['merriweather_sans'] text-white font-bold text-xl md:text-3xl my-auto" id="header_title"
                onpageshow="changeHeaderTitle()">
            </h1>
        </div>
    </div>

    <!-- search -->
    <?php if (($_SERVER['REQUEST_METHOD'] == 'GET') && (isset($_GET['student']))) { ?>
        <div id="search " class="flex items-center justify-end w-64 gap-3 mr-10">
            <form id="search_form">
                <input type="text" name="search_input" id="search_input" placeholder="Search..."
                    value="<?= (isset($_GET['student']) && strpos($_GET['student'], '-') !== false) ? htmlspecialchars($_GET['student']) : '' ?>"
                    class="w-24 px-1 py-1 text-xs text-white bg-teal-700 border border-teal-300 rounded-lg shadow-sm md:text-sm placeholder:text-xs md:w-36 placeholder:text-white focus:ring-2 focus:ring-teal-400/0 focus:outline-none" autocomplete="off" pattern="\d{4}-\d{1,2}-\d{4}[\dA-Za-z]{0,2}">
                <button
                    class="px-2 py-1 text-xs font-semibold text-white transition bg-teal-700 border border-teal-300 rounded-lg md:text-sm hover:bg-teal-600">
                    Go
                </button>
            </form>
        </div>
    <?php } ?>
</header>