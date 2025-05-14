<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Ususan Elementary School</title>
    <link rel="icon" href="{{ asset('icons/Logo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.13/lottie.min.js"></script>

    <style>
        .fade-in {
            animation: fadeIn 0.15s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .active-link {
            background-color: #f3f4f6;
            font-weight: 600;
        }

        .nav-link {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.75rem 1.5rem;
            box-sizing: border-box;
            transition: background-color 0.3s ease, padding 0.3s ease;
        }

        .nav-link:hover {
            padding-left: 2rem;
            background-color: #f3f4f6;
        }


        .nav-link img {
            height: 20px;
            width: 20px;
            min-width: 20px;
            min-height: 20px;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }


        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .section-paragraph {
            max-width: 700px;
            margin: 0 auto;
            font-size: 1rem;
            color: #4b5563;
            line-height: 1.6;
        }

        #sidebar {
            width: 256px;
            transition: width 0.3s ease;
            overflow-x: hidden;
        }

        #sidebar .school-name {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: break-word;
            line-height: 1.25;
            display: inline-block;
            height: 2.0rem;
            width: 10.0rem;
        }


        #sidebar .nav-links-container {
            transition: padding 0.3s ease;
            padding-top: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        #sidebar .nav-link {
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            gap: 0.75rem;
            white-space: nowrap;
        }

        #sidebar.collapsed {
            width: 60px;
        }



        #sidebar.collapsed .sidebar-logo,
        #sidebar.collapsed .school-name,
        #sidebar.collapsed .sidebar-text {
            display: none !important;
        }

        #sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.75rem 0;
            width: 100%;
        }

        #sidebar.collapsed .nav-links-container {
            padding-top: 1rem;
        }

        #sidebar.collapsed .nav-link img {
            margin-right: 0;
        }


        #sidebar.collapsed .nav-link:hover,
        #sidebar.collapsed .active-link {
            background-color: #f3f4f6;
            padding: 0.75rem 0;
            border-left: none;
        }

        #sidebar .nav-links-container {
            padding-top: 0rem;
        }

        #sidebar.collapsed .nav-links-container {
            padding-top: 0rem;
        }


        #main {
            transition: margin-left 0.3s ease;
        }

        #sidebar.collapsed~#main {
            margin-left: 60px;
        }

        /* === Sidebar === */
        @media (max-width: 1024px) {

            /* Sidebar styles */
            #sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                z-index: 50;
                transition: transform 0.3s ease;
            }

            #sidebar.show {
                transform: translateX(0);
            }

            /* Hide sidebar's internal burger icon initially */
            #sidebar .sidebar-header img#burger-toggle {
                display: none;
            }

            #sidebar.show .sidebar-header img#burger-toggle {
                display: block;
            }

            /* Remove sidebar spacing from main content */
            #main {
                margin-left: 0 !important;
            }

            /* ✅ Fix page title alignment */
            #page-title {
                text-align: left;
                margin-left: 0.5rem;
            }
        }

        /* ✅ Show burger icon in navbar explicitly */
        #main-burger-toggle {
            display: block !important;
        }


        /* DESKTOP: > 1024px */
        @media (min-width: 1025px) {

            /* Optionally hide burger icon in header for desktops */
            #main-burger-toggle {
                display: none !important;
            }

            /* Reset page title layout if needed */
            #page-title {
                text-align: left;
                margin-left: 0.5rem;
            }
        }


        @media (max-width: 325px) {

            /* Make sure the whole top bar is laid out as a row */
            .topbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 1rem;
            }

            #page-title {
                font-size: 0.75rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 120px;
                margin-left: 0.25rem;
                padding: 0;
            }

            /* Shrink user text */
            .user-section span.text-xs {
                font-size: 0.65rem;
            }

            .user-section span.text-[20px] {
                font-size: 0.8rem !important;
            }

            /* Shrink logout icon */
            .user-section img {
                height: 1.5rem;
                width: 1.5rem;
            }

            /* Stack inner user info better */
            .user-section {
                gap: 0.75rem;
            }

            .user-section .user-info {
                align-items: flex-end;
                gap: 0.2rem;
            }

            #main {
                margin-left: 0 !important;
            }
        }

        #dropdownMenu {
            min-width: 7rem;
            background-color: #e6db8b;
            border-radius: 0.6rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.16);
            z-index: 50;
            align-items: center;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        #dropdownMenu.show {
            opacity: 1;
            /* Fully visible when 'show' class is added */
            pointer-events: auto;
            /* Allow interactions when shown */
        }

        #dropdownMenu .hover-red:hover {
            color: red;
        }

        #dropdownMenu button {
            transition: all 0.2s ease;
        }

        #dropdownToggle {
            transition: opacity 0.2s ease;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="flex">
        <div id="sidebar"
            class="bg-yellow-300 h-screen w-64 fixed top-0 left-0 transition-all duration-300 ease-in-out flex flex-col z-50 overflow-hidden round-lg"
            style="background-color: #edd26f">
            <!-- Logo -->
            <div class="h-16 px-4 flex items-center justify-between shadow-lg sidebar-header"
                style="background-color: #EAD180; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);">
                <!-- Logo + School Name -->
                <div class="flex items-center space-x-3 overflow-hidden">
                    <img src="{{ asset('icons/Logo.png') }}" class="h-10 w-10 flex-shrink-0 sidebar-logo" alt="Logo" />
                    <div class="w-full overflow-hidden">
                        <span class="text-sm font-semibold sidebar-text school-name block">
                            Ususan Elementary School
                        </span>
                    </div>
                </div>
                <!-- Burger Icon -->
                <img src="{{ asset('icons/burger-bar.png') }}" alt="Menu" id="burger-toggle"
                    class="h-6 w-6 cursor-pointer hover:opacity-80 transition" />
            </div>

            <!-- Modules Navigation -->
            <div class="nav-links-container flex flex-col flex-grow">
                <nav class="space-y-1 mt-4 flex-1 overflow-y-auto px-2" id="nav-links">
                    <a href="{{ route('dashboard.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('dashboard.index') }}" data-title="Home">
                        <img src="{{ asset('icons/home.png') }}" class="h-5 w-5 mr-2" alt="dashboard Icon" />
                        <span class="sidebar-text ml-2">Home</span>
                    </a>
                    <a href="{{ route('school-years.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('school-years.index') }}" data-title="School Years Module"
                        data-group="school-years">
                        <img src="{{ asset('icons/schoolyr.png') }}" class="h-5 w-5 mr-2" alt="School Year Icon" />
                        <span class="sidebar-text ml-2">School Years</span>
                    </a>
                    <a href="{{ route('courses.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('courses.index') }}" data-title="Subjects Module" data-group="courses">
                        <img src="{{ asset('icons/course.png') }}" class="h-5 w-5 mr-2" alt="Subject Icon" />
                        <span class="sidebar-text ml-2">Subjects</span>
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('users.index') }}" data-title="Users Module" data-group="users">
                        <img src="{{ asset('icons/user.png') }}" class="h-5 w-5 mr-2" alt="User Icon" />
                        <span class="sidebar-text ml-2">Users</span>
                    </a>
                    <a href="{{ route('students.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('students.index') }}" data-title="Students Module" data-group="students">
                        <img src="{{ asset('icons/student.png') }}" class="h-5 w-5 mr-2" alt="Student Icon" />
                        <span class="sidebar-text ml-2">Students</span>
                    </a>
                    <a href="{{ route('teachers.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('teachers.index') }}" data-title="Teachers Module" data-group="teachers">
                        <img src="{{ asset('icons/teacher.png') }}" class="h-5 w-5 mr-2" alt="Teacher Icon" />
                        <span class="sidebar-text ml-2">Teachers</span>
                    </a>
                    <a href="{{ route('curriculums.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('curriculums.index') }}" data-title="Curriculums Module"
                        data-group="curriculums">
                        <img src="{{ asset('icons/curriculum.png') }}" class="h-5 w-5 mr-2" alt="Curriculum Icon" />
                        <span class="sidebar-text ml-2">Curriculums</span>
                    </a>
                    <a href="{{ route('schedules.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('schedules.index') }}" data-title="Schedules Module" data-group="schedules">
                        <img src="{{ asset('icons/schedule.png') }}" class="h-5 w-5 mr-2" alt="Schedule Icon" />
                        <span class="sidebar-text ml-2">Schedules</span>
                    </a>
                    <a href="{{ route('sections.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('sections.index') }}" data-title="Sections Module" data-group="sections">
                        <img src="{{ asset('icons/section.png') }}" class="h-5 w-5 mr-2" alt="Section Icon" />
                        <span class="sidebar-text ml-2">Sections</span>
                    </a>
                    <a href="{{ route('activity-logs.index') }}"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link"
                        data-url="{{ route('activity-logs.index') }}" data-title="Activity Logs"
                        data-group="activity-logs">
                        <img src="{{ asset('icons/log.png') }}" class="h-5 w-5 mr-2" alt="Activity Log Icon" />
                        <span class="sidebar-text ml-2">Activity Logs</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="ml-64 flex-1 min-h-screen flex flex-col transition-all duration-300 topbar" id="main">
            <nav class="relative bg-gray-50 h-16 px-4 flex items-center justify-between sticky top-0 z-40 shadow-lg"
                style="background-color: #EAD180; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);">
                <!-- Title -->
                <!-- Burger Icon for Small Screens -->
                <div class="flex items-center gap-2">
                    <img src="{{ asset('icons/burger-bar.png') }}" alt="Menu" id="main-burger-toggle"
                        class="h-6 w-6 cursor-pointer hover:opacity-80 transition hidden lg:block lg:hidden" />
                    <div id="page-title" class="text-xl font-semibold whitespace-nowrap">
                        Admin | Home
                    </div>
                </div>

                {{-- Right: User Info + Dropdown (unchanged) --}}
                <div class="flex items-center gap-4 relative">
                    <!-- User Info -->
                    <div class="flex flex-col items-end leading-tight user-info">
                        <span class="text-xs text-red-500 font-semibold">ADMIN</span>
                        <span class="font-bold text-[20px]">{{ Auth::user()->name }}</span>
                    </div>

                    <div class="relative">
                        <button id="dropdownToggle"
                            class="flex items-center justify-center w-7 h-7 rounded-full transition-transform duration-200 transform hover:scale-110 focus:outline-none"
                            style="background-color: #000000; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);">
                            <svg class="w-5 h-5 transition-colors duration-200" fill="none" stroke="currentColor"
                                stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                style="color: #ffffff;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdownMenu"
                            class="absolute right-0 top-9 mt-2 border border-gray-300 shadow-lg z-50 opacity-0 pointer-events-none transition-opacity duration-300">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-[#e6db8b] hover-red font-bold">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
            </nav>

            <!-- Dynamic Content -->
            <div class="p-6 overflow-y-auto bg-gray-50 flex-1 transition-opacity duration-300 opacity-100"
                id="content-frame">
                <div class="text-center text-gray-700 mt-4 space-y-10">
                    @yield('content')
                </div>
            </div>

        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const contentFrame = document.getElementById("content-frame");
            const pageTitle = document.getElementById("page-title");
            const pageMeta = document.getElementById("page-meta");
            const sidebar = document.getElementById('sidebar');
            const burgerSidebar = document.getElementById('burger-toggle');
            const burgerMain = document.getElementById('main-burger-toggle');
            const toggleButton = document.getElementById('dropdownToggle');
            const dropdownMenu = document.getElementById('dropdownMenu');
            let lottieAnimation = null;

            function setActiveLink(url) {
                const currentPath = new URL(url, window.location.origin).pathname;

                document.querySelectorAll('.nav-link').forEach(link => {
                    const linkUrl = new URL(link.getAttribute('data-url'), window.location.origin).pathname;
                    const linkGroup = link.getAttribute('data-group');
                    const linkTitle = link.getAttribute('data-title');

                    const isMatch = currentPath === linkUrl ||
                        currentPath.startsWith(linkUrl) ||
                        (linkGroup && currentPath.includes(linkGroup));

                    link.classList.toggle('active-link', isMatch);

                    if (isMatch && pageTitle) {
                        pageTitle.textContent = 'Admin | ' + linkTitle;
                    }
                });
            }

            function showLoaderInFrame() {
                contentFrame.innerHTML = `
            <div class="flex flex-col justify-center items-center h-full w-full">
                <div id="lottie-loader" class="w-32 h-32"></div>
            </div>
        `;
                contentFrame.style.opacity = 1;

                lottieAnimation = lottie.loadAnimation({
                    container: document.getElementById('lottie-loader'),
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: '/icons/animations/bookanimation.json'
                });lottieAnimation.setSpeed(0.5);
            }

            function loadPage(url, push = true) {
                showLoaderInFrame();

                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('#content-frame')?.innerHTML;

                        setTimeout(() => {
                            if (lottieAnimation) {
                                lottieAnimation.destroy();
                                lottieAnimation = null;
                            }

                            if (newContent) {
                                contentFrame.style.opacity = 0;
                                contentFrame.innerHTML = newContent;

                                doc.querySelectorAll("script").forEach(oldScript => {
                                    const newScript = document.createElement("script");
                                    if (oldScript.src) {
                                        if (!document.querySelector(`script[src="${oldScript.src}"]`)) {
                                            newScript.src = oldScript.src;
                                            document.body.appendChild(newScript);
                                        }
                                    } else {
                                        newScript.textContent = oldScript.textContent;
                                        document.body.appendChild(newScript);
                                    }
                                    oldScript.remove();
                                });

                                const meta = contentFrame.querySelector('#page-meta');
                                if (meta) {
                                    const newTitle = meta.getAttribute('data-title');
                                    if (pageTitle && newTitle) {
                                        pageTitle.textContent = 'Admin | ' + newTitle;
                                    }
                                } else {
                                    setActiveLink(url);
                                }

                                if (url.includes('users/create') || url.match(/users\/\d+\/edit/)) {
                                    if (typeof initializeUserFormScripts === 'function') {
                                        initializeUserFormScripts();
                                    }
                                }

                                if (url.includes('curriculum')) {
                                    if (typeof initCurriculumView === 'function') {
                                        initCurriculumView();
                                    }
                                }

                                contentFrame.style.opacity = 1;

                                if (push) {
                                    history.pushState({ path: url }, '', url);
                                }
                            } else {
                                contentFrame.innerHTML = "<p class='text-red-500'>Error loading content.</p>";
                            }
                        }, 400);
                    })
                    .catch(() => {
                        contentFrame.innerHTML = "<p class='text-red-500'>Failed to load page.</p>";
                    });
            }

            function loadContent(url, title, group = '') {
                if (pageTitle && title) {
                    pageTitle.textContent = 'Admin | ' + title;
                }
                loadPage(url);
            }

            window.loadContent = loadContent;

            // Set initial active link
            const currentUrl = window.location.href;
            setActiveLink(currentUrl);

            if (pageMeta) {
                const customTitle = pageMeta.getAttribute('data-title');
                if (customTitle && pageTitle) {
                    pageTitle.textContent = 'Admin | ' + customTitle;
                }

                const parentModule = pageMeta.dataset.parent?.trim();
                if (parentModule) {
                    document.querySelectorAll(".nav-link").forEach(link => link.classList.remove("active-link"));

                    const matchingLink = [...document.querySelectorAll(".nav-link")].find(link =>
                        link.textContent.trim() === parentModule
                    );

                    if (matchingLink) {
                        matchingLink.classList.add("active-link");

                        const submenu = matchingLink.nextElementSibling;
                        if (submenu?.classList.contains("submenu")) {
                            submenu.style.display = 'block';
                        }
                    }
                }
            }

            if (currentUrl.includes('users/create') || currentUrl.match(/users\/\d+\/edit/)) {
                if (typeof initializeUserFormScripts === 'function') {
                    initializeUserFormScripts();
                }
            }

            if (currentUrl.includes('curriculum')) {
                if (typeof initCurriculumView === 'function') {
                    initCurriculumView();
                }
            }

            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    loadContent(url, this.getAttribute('data-title'), this.getAttribute('data-group'));
                });
            });

            window.addEventListener('popstate', function (e) {
                if (e.state?.path) {
                    loadPage(e.state.path, false);
                }
            });

            if (window.location.pathname === '/dashboard') {
                const defaultUrl = document.querySelector('.nav-link[data-title="Home"]')?.getAttribute('data-url');
                if (defaultUrl) {
                    loadContent(defaultUrl, 'Home');
                }
            }

            // SIDEBAR BEHAVIOR
            function isSmallScreen() {
                return window.innerWidth <= 1024;
            }

            function toggleSidebar() {
                if (isSmallScreen()) {
                    sidebar.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                }
            }

            function enforceBurgerVisibility() {
                if (burgerMain) {
                    burgerMain.style.display = window.innerWidth > 1024 ? 'none' : 'block';
                }
            }

            enforceBurgerVisibility();

            if (burgerMain) {
                burgerMain.addEventListener('click', toggleSidebar);
            }

            if (burgerSidebar) {
                burgerSidebar.addEventListener('click', function () {
                    if (isSmallScreen()) {
                        sidebar.classList.remove('show');
                    } else {
                        sidebar.classList.toggle('collapsed');
                    }
                });
            }

            document.addEventListener('click', function (e) {
                if (
                    isSmallScreen() &&
                    sidebar.classList.contains('show') &&
                    !sidebar.contains(e.target) &&
                    !burgerMain.contains(e.target)
                ) {
                    sidebar.classList.remove('show');
                }
            });

            window.addEventListener('resize', function () {
                if (!isSmallScreen()) {
                    sidebar.classList.remove('show');
                }
                enforceBurgerVisibility();
            });

            // DROPDOWN
            if (toggleButton && dropdownMenu) {
                toggleButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    dropdownMenu.classList.toggle('show');
                });

                window.addEventListener('click', function (e) {
                    if (!toggleButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        });

        // FORM INITIALIZER
        function initializeUserFormScripts() {
            const roleSelect = document.getElementById('role');
            if (roleSelect) {
                roleSelect.addEventListener('change', function () {
                    const teacherFields = document.getElementById('teacherFields');
                    const showTeacher = this.value === 'teacher';
                    teacherFields.classList.toggle('hidden', !showTeacher);

                    [...teacherFields.querySelectorAll('input, textarea')].forEach(el => {
                        el.disabled = !showTeacher;
                    });
                });

                roleSelect.dispatchEvent(new Event('change'));
            }

            document.querySelectorAll('.toggle-password').forEach(icon => {
                icon.addEventListener('click', function () {
                    const targetId = this.dataset.target;
                    const input = document.getElementById(targetId);

                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';

                    this.src = isPassword ? "/icons/Hidden.png" : "/icons/Eye.png";
                    this.alt = isPassword ? 'Hide Password' : 'Show Password';
                });
            });
        }
    </script>
</body>

</html>