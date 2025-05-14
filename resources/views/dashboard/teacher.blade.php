<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher | Ususan Elementary School</title>
    <link rel="icon" href="{{ asset('icons/Logo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.13/lottie.min.js"></script>

    <style>
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
            pointer-events: auto;
        }

        #dropdownMenu .hover-red:hover {
            color: red;
        }

        #dropdownToggle {
            transition: opacity 0.3s ease;
        }

        #dropdownToggle:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-gray-100">
    <nav class="relative bg-gray-50 h-16 px-4 flex items-center justify-between sticky top-0 z-40 shadow-lg"
        style="background-color: #EAD180; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);">
        <!-- Title -->
        <div class="flex items-center gap-2">
            <!-- Removed burger icon -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('icons/Logo.png') }}" alt="Ususan Logo" class="h-10 w-10 object-contain">
                <span class="font-bold text-lg text-gray-900">Ususan Elementary School</span>
            </div>
        </div>

        {{-- Right: User Info + Dropdown (unchanged) --}}
        <div class="flex items-center gap-4 relative">
            <!-- User Info -->
            <div class="flex flex-col items-end leading-tight user-info">
                <span class="text-xs text-green-500 font-semibold">TEACHER</span>
                <span class="font-bold text-[20px]">{{ Auth::user()->name }}</span>
            </div>

            <div class="relative">
                <button id="dropdownToggle"
                    class="flex items-center justify-center w-7 h-7 rounded-full transition-transform duration-200 transform hover:scale-110 focus:outline-none"
                    style="background-color: #000000; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);">
                    <svg class="w-5 h-5 transition-colors duration-200" fill="none" stroke="currentColor"
                        stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="color: #ffffff;">
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

   <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <!-- Reserved for buttons -->
    </div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Teaching Schedule -->
        <div class="bg-white shadow-2xl rounded-2xl p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                <img src="{{ asset('icons/teaching.png') }}" alt="Teaching Icon" class="w-6 h-6">
                My Teaching Schedule
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed text-sm text-gray-700 border border-gray-200 rounded-lg text-center">
                    <thead class="bg-yellow-100 text-gray-800 uppercase text-xs font-bold">
                        <tr>
                            <th class="w-1/4 px-6 py-3">Time</th>
                            <th class="w-1/4 px-6 py-3">Course</th>
                            <th class="w-1/4 px-6 py-3">Grade Level</th>
                            <th class="w-1/4 px-6 py-3">Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-yellow-50">
                                <td class="px-6 py-4 w-1/4">{{ date('h:i A', strtotime($schedule->start_time)) }} - {{ date('h:i A', strtotime($schedule->end_time)) }}</td>
                                <td class="px-6 py-4 w-1/4">{{ $schedule->course->name }}</td>
                                <td class="px-6 py-4 w-1/4">{{ $schedule->section->grade_level }}</td>
                                <td class="px-6 py-4 w-1/4">{{ $schedule->section->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500">No schedule available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Student List -->
        <div class="bg-white shadow-2xl rounded-2xl p-6">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                <img src="{{ asset('icons/students.png') }}" alt="Students Icon" class="w-6 h-6">
                My Students
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed text-sm text-gray-700 border border-gray-200 rounded-lg text-center">
                    <thead class="bg-yellow-100 text-gray-800 uppercase text-xs font-bold">
                        <tr>
                            <th class="w-1/4 px-6 py-3">Name</th>
                            <th class="w-1/4 px-6 py-3">Grade Level</th>
                            <th class="w-1/4 px-6 py-3">Section</th>
                            <th class="w-1/4 px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-yellow-50">
                                <td class="px-6 py-4 w-1/4">{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td class="px-6 py-4 w-1/4">{{ $student->section->grade_level }}</td>
                                <td class="px-6 py-4 w-1/4">{{ $student->section->name }}</td>
                                <td class="px-6 py-4 w-1/4">
                                    <a href="{{ route('teacher.submit.grades', $student->id) }}"
                                       class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                        Submit Grades
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500">No students available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
        <script>
        const toggleButton = document.getElementById('dropdownToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');

        toggleButton.addEventListener('click', function (e) {
            e.preventDefault();
            dropdownMenu.classList.toggle('show');
        });

        window.addEventListener('click', function (e) {
            if (!toggleButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
        </script>
</body>

</html>