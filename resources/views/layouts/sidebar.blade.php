<!-- Sidebar -->
<div class="flex flex-col flex-1 h-full overflow-y-auto bg-white border-r dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center justify-center h-14 px-6 bg-white border-b dark:bg-gray-800 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center">
            <img src="{{ asset('logo.png') }}" class="h-10" alt="Logo" />
        </a>
    </div>

    <nav class="flex-1 px-2 py-4">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-md dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M3 12h18M3 21h18"></path>
            </svg>
            <span class="ml-4">Dashboard</span>
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

        <!-- Curriculum Management -->
        <a href="{{ route('curriculums.index') }}" title="Curriculum"
            class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link  {{ request()->routeIs('curriculums.*') ? 'active-link' : '' }}">
            <img src="{{ asset('icons/course.png') }}" class="h-5 w-5 mr-2" alt="Curriculum Icon" />
            <span class="sidebar-text ml-2">Curriculum</span>
        </a>

        <a href="{{ route('activity-logs.index') }}" title="Activity Logs"
            class="flex items-center px-4 py-2 hover:bg-gray-100 transition rounded nav-link ">
            <img src="{{ asset('icons/log.png') }}" class="h-5 w-5 mr-2" alt="Log Icon" />
            <span class="sidebar-text ml-2">Activity Logs</span>
        </a>
    </nav>
</div>
