<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | Ususan Elementary School</title>
    <link rel="icon" href="{{ asset('icons/logo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

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
            /* Allow wrapping */
            word-break: break-word;
            /* Allow breaking words if necessary */
            overflow-wrap: break-word;
            /* Ensure wrapping happens correctly */
            line-height: 1.25;
            display: inline-block;
            height: 2.0rem;
            width: 10.0rem;
        }

        /* Make sure nav container layout stays consistent */
        #sidebar .nav-links-container {
            transition: padding 0.3s ease;
            padding-top: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* Prevent nav-links from jumping when expanding */
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


        /* Hide text and logo in collapsed mode */
        #sidebar.collapsed .sidebar-logo,
        #sidebar.collapsed .school-name,
        #sidebar.collapsed .sidebar-text {
            display: none !important;
        }

        /* Center icons vertically and horizontally */
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

        /* Keep full width and fixed height for uniform hover */
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

        /* ✅ Adjust main content */
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
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <!-- Sidebar -->
    <div class="flex">
        <div id="sidebar"
            class="bg-yellow-300 h-screen w-64 fixed top-0 left-0 transition-all duration-300 ease-in-out flex flex-col z-50 overflow-hidden round-lg"
            style="background-color: #edd26f">
            <!-- Logo -->
            <div class="h-16 px-4 flex items-center justify-between shadow-lg border-b sidebar-header"
                style="background-color: #EAD180;">
                <!-- Logo + School Name -->
                <div class="flex items-center space-x-3 overflow-hidden">
                    <img src="{{ asset('icons/logo.png') }}" class="h-10 w-10 flex-shrink-0 sidebar-logo"
                        alt="Logo" />
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
                    <a href="{{ route('dashboard.index') }}" title="Home"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link ">
                        <img src="{{ asset('icons/home.png') }}" class="h-5 w-5 mr-2" alt="Dashboard Icon" />
                        <span class="sidebar-text ml-2">Home</span>
                    </a>
                    <a href="{{ route('school-years.index') }}" title="School Years"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('school-years.*') ? 'active-link' : '' }}">
                        <img src="{{ asset('icons/schoolyr.png') }}" class="h-5 w-5 mr-2" alt="School Year Icon" />
                        <span class="sidebar-text ml-2">School Years</span>
                    </a>
                    <a href="{{ route('courses.index') }}" title="Subjects"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('courses.*') ? 'active-link' : '' }}">
                        <img src="{{ asset('icons/course.png') }}" class="h-5 w-5 mr-2" alt="Course Icon" />
                        <span class="sidebar-text ml-2">Subjects</span>
                    </a>
                    <a href="{{ route('users.index') }}" title="Users"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('users.*') ? 'active-link' : '' }}">
                        <img src="{{ asset('icons/user.png') }}" class="h-5 w-5 mr-2" alt="Users Icon" />
                        <span class="sidebar-text ml-2">Users</span>
                    </a>
                    <a href="{{ route('students.index') }}" title="Students"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('students.*') ? 'active-link' : '' }}">
                        <img src="{{ asset('icons/student.png') }}" class="h-5 w-5 mr-2" alt="Student Icon" />
                        <span class="sidebar-text ml-2">Students</span>
                    </a>
                    <a href="{{ route('teachers.index') }}" title="Teachers"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('teachers.*') ? 'active-link' : '' }}">
                        <img src="{{ asset('icons/teacher.png') }}" class="h-5 w-5 mr-2" alt="Teacher Icon" />
                        <span class="sidebar-text ml-2">Teachers</span>
                    </a>
                    <a href="{{ route('schedules.index') }}" title="Schedules"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('schedules.*') ? 'active-link' : '' }}">
                        <img src="{{ asset('icons/schedule.png') }}" class="h-5 w-5 mr-2" alt="Schedule Icon" />
                        <span class="sidebar-text ml-2">Schedules</span>
                    </a>
                    <a href="{{ route('sections.index') }}" title="Sections"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('sections.*') ? 'active-link' : '' }}">
                        <img src="{{ asset('icons/section.png') }}" class="h-5 w-5 mr-2" alt="Section Icon" />
                        <span class="sidebar-text ml-2">Sections</span>
                    </a>
                    <a href="{{ route('activity-logs.index') }}" title="Activity Logs"
                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link ">
                        <img src="{{ asset('icons/log.png') }}" class="h-5 w-5 mr-2" alt="Log Icon" />
                        <span class="sidebar-text ml-2">Activity Logs</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="ml-64 flex-1 min-h-screen flex flex-col transition-all duration-300 topbar" id="main">

            <nav class="relative bg-gray-50 h-16 px-4 flex items-center justify-between sticky top-0 z-40 shadow-lg"
                style="background-color: #EAD180;">
                <!-- Title -->
                <!-- Burger Icon for Small Screens -->
                <div class="flex items-center gap-2">
                    <img src="{{ asset('icons/burger-bar.png') }}" alt="Menu" id="main-burger-toggle"
                        class="h-6 w-6 cursor-pointer hover:opacity-80 transition hidden lg:block lg:hidden" />
                    <div id="page-title" class="text-xl font-semibold whitespace-nowrap">
                        Admin | Home
                    </div>
                </div>
                <!-- Right: User & Logout -->
                <div class="flex items-center gap-4">
                    <!-- User Info -->
                    <div class="flex flex-col items-end leading-tight user-info">
                        <span class="text-xs text-red-500 font-semibold">ADMIN</span>
                        <span class="font-bold text-[20px]">{{ Auth::user()->name }}</span>
                    </div>

                    <!-- Logout (Vertically Centered) -->
                    <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                        @csrf
                        <button type="submit" class="focus:outline-none">
                            <img src="{{ asset('icons/logout.png') }}" alt="Logout"
                                class="h-8 w-8 hover:opacity-80 transition" title="Logout" />
                        </button>
                    </form>
                </div>

            </nav>
            <!-- Dynamic Content -->
            <div class="p-6 overflow-y-auto bg-gray-50 flex-1 transition-opacity duration-300 opacity-100"
                id="content-frame">
                <div class="text-center text-gray-700 mt-4 space-y-10">
                    @yield('content')
                </div>
            </div>

            <script>
                const titleMap = {
                    'Home': 'Admin | Home',

                    'School Years': 'Admin | School Years Module',
                    'Add School Year': 'Admin | Adding School Year',
                    'Edit School Year': 'Admin | Edit School Year',

                    'Subjects': 'Admin | Subjects Module',
                    'Add Subject': 'Admin | Adding Subject',
                    'Edit Subject': 'Admin | Edit Subject',

                    'Users': 'Admin | Users Module',
                    'Add User': 'Admin | Create User',
                    'Edit User': 'Admin | Edit User',

                    'Students': 'Admin | Students Module',
                    'Add Student': 'Admin | Add Student',
                    'Batch Upload Student': 'Admin | Batch Upload',
                    'View Student': 'Admin | View Student',
                    'Edit Student': 'Admin | Edit Student',

                    'Teachers': 'Admin | Teachers Module',
                    'Add Teacher': 'Admin | Add New Teacher',
                    'Batch Upload Teacher': 'Admin | Batch Upload',
                    'Edit Teacher': 'Admin | Edit Teacher',

                    'Schedules': 'Admin | Schedules Module',
                    'Add Schedule': 'Admin | Add Schedule',
                    'Edit Schedule': 'Admin | Edit Schedule',

                    'Sections': 'Admin | Sections Module',
                    'Add Section': 'Admin | Add Section',
                    'Edit Section': 'Admin | Edit Section',


                    'Activity Logs': 'Admin | Activity Logs',
                    'View Activity Log': 'Admin | View Activity Log'
                };

                function updatePageTitle(moduleName) {
                    const titleEl = document.getElementById('page-title');
                    titleEl.textContent = titleMap[moduleName] || titleMap['Home'];
                }

                const contentFrame = document.getElementById("content-frame");
                const links = document.querySelectorAll(".nav-link");

                function loadContent(url, linkOrName) {
                    const moduleName = typeof linkOrName === 'string' ?
                        linkOrName :
                        linkOrName?.textContent.replace(/\s+/g, ' ').trim() || 'Home';

                    updatePageTitle(moduleName);

                    contentFrame.innerHTML = `
        <div class="flex flex-col justify-center items-center h-full w-full">
            <div id="lottie-loader" class="w-32 h-32"></div>
        </div>`;

                    lottie.loadAnimation({
                        container: document.getElementById('lottie-loader'),
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        path: '/icons/animations/bookanimation.json'
                    });

                    fetch(url)
                        .then(res => res.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, "text/html");
                            const newC = doc.querySelector("#content-frame")?.innerHTML || doc.body.innerHTML;

                            contentFrame.innerHTML = `<div class="fade-in">${newC}</div>`;
                            history.pushState(null, "", url);

                            links.forEach(l => l.classList.remove("active-link"));

                            if (linkOrName instanceof Element) {
                                linkOrName.classList.add("active-link");
                            } else {
                                const fallbackMap = {
                                    'Add School Year': 'School Years',
                                    'Edit School Year': 'School Years',

                                    'Add Subject': 'Subjects',
                                    'Edit Subject': 'Subjects',

                                    'Add User': 'Users',
                                    'Edit User': 'Users',

                                    'Add Student': 'Students',
                                    'Batch Upload Student': 'Students',
                                    'View Student': 'Students',
                                    'Edit Student': 'Students',

                                    'Add Teacher': 'Teachers',
                                    'Batch Upload Teacher': 'Teachers',
                                    'Edit Teacher': 'Teachers',

                                    'Add Schedule': 'Schedules',
                                    'Edit Schedule': 'Schedules',

                                    'Add Section': 'Sections',
                                    'Edit Section': 'Sections',

                                    'View Activity Log': 'Activity Logs'

                                };
                                const fallbackLabel = fallbackMap[linkOrName] || linkOrName;
                                const fallbackLink = [...links].find(l =>
                                    l.textContent.replace(/\s+/g, " ").trim() === fallbackLabel
                                );
                                if (fallbackLink) fallbackLink.classList.add("active-link");
                            }
                        })
                        .catch(err => {
                            contentFrame.innerHTML = '<p class="text-red-500">Failed to load content.</p>';
                            console.error(err);
                        });
                }

                // Click handlers
                links.forEach(link => {
                    link.addEventListener("click", e => {
                        e.preventDefault();
                        loadContent(link.href, link);
                    });
                });

                // On page load: load the current page OR fallback to Dashboard
                window.addEventListener("DOMContentLoaded", () => {
                    const path = window.location.pathname;
                    const activeLink = Array.from(links).find(l => l.pathname === path);

                    if (activeLink) {
                        loadContent(activeLink.href, activeLink);
                    } else {
                        const dashboardLink = Array.from(links).find(l =>
                            l.textContent.trim().toLowerCase() === 'home'
                        );

                        if (dashboardLink) {
                            loadContent(dashboardLink.href, dashboardLink);
                        } else {
                            contentFrame.innerHTML = `
            <div class="text-center text-red-500 mt-10 text-lg">
                Home module could not be loaded.
            </div>`;
                        }
                    }
                });

                // Handle browser back/forward navigation
                window.addEventListener("popstate", () => {
                    const path = window.location.pathname;
                    const match = Array.from(links).find(l => l.pathname === path);
                    loadContent(match?.href || window.location.href, match);
                });


                document.addEventListener('DOMContentLoaded', function() {
                    const sidebar = document.getElementById('sidebar');
                    const burgerSidebar = document.getElementById('burger-toggle'); // in sidebar
                    const burgerMain = document.getElementById('main-burger-toggle'); // in top nav
                    const main = document.getElementById('main');

                    // Track state for large vs small screens
                    function isSmallScreen() {
                        return window.innerWidth <= 1024;
                    }

                    function toggleSidebar() {
                        if (isSmallScreen()) {
                            // Mobile behavior: slide sidebar in/out
                            sidebar.classList.toggle('show');
                        } else {
                            // Desktop behavior: collapse sidebar
                            sidebar.classList.toggle('collapsed');
                        }
                    }

                    // ✅ Ensure burger visibility is correct on page load and resize
                    function enforceBurgerVisibility() {
                        if (burgerMain) {
                            if (window.innerWidth > 1024) {
                                burgerMain.style.display = 'none';
                            } else {
                                burgerMain.style.display = 'block';
                            }
                        }
                    }

                    // Initial check
                    enforceBurgerVisibility();

                    // Click from top burger (always available)
                    if (burgerMain) {
                        burgerMain.addEventListener('click', toggleSidebar);
                    }

                    // Click from sidebar burger (only on large screens)
                    if (burgerSidebar) {
                        burgerSidebar.addEventListener('click', function() {
                            if (isSmallScreen()) {
                                sidebar.classList.remove('show');
                            } else {
                                sidebar.classList.toggle('collapsed');
                            }
                        });
                    }

                    // OPTIONAL: hide sidebar when clicking outside (on small screens)
                    document.addEventListener('click', function(e) {
                        if (
                            isSmallScreen() &&
                            sidebar.classList.contains('show') &&
                            !sidebar.contains(e.target) &&
                            !burgerMain.contains(e.target)
                        ) {
                            sidebar.classList.remove('show');
                        }
                    });

                    // On resize, hide mobile sidebar and re-check burger visibility
                    window.addEventListener('resize', function() {
                        if (!isSmallScreen()) {
                            sidebar.classList.remove('show');
                        }
                        enforceBurgerVisibility();
                    });
                });
            </script>
            @stack('scripts')
</body>

</html>
