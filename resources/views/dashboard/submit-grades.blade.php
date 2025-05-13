<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher | Submit Grade</title>
    <link rel="icon" href="{{ asset('icons/logo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        select.custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.25rem;
            padding-right: 3rem;
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


            <div class="max-w-4xl mx-auto">
                {{-- Header Row: Title on the left, Button on the right --}}
                <div class="flex justify-between items-center mt-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 leading-snug">
                        Submit Grades
                    </h2>
                    <a href="{{ route('teacher.dashboard') }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Dashboard
                    </a>
                </div>

                {{-- Grades Form Container --}}
                <div class="bg-yellow-100 shadow-lg border border-yellow-200 rounded-2xl p-8">
                    <div class="mb-8">
                        <span class="text-2xl font-semibold text-gray-700 block mt-1">
                            Name: {{ $student->first_name }} {{ $student->last_name }}
                        </span>
                        <span class="text-2xl font-semibold text-gray-700 block">
                            School Year: {{ $student->schoolYear->school_year_display }}
                        </span>
                    </div>

                    {{-- Course Selection --}}
                    <div class="mb-6">
                        <label for="course_selection" class="block text-sm font-medium text-gray-800 mb-2">
                            Select Course
                        </label>
                        <select id="course_selection" name="course_selection"
                            class="custom-select w-full border border-gray-300 rounded-lg px-3 py-2.5 text-base bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($teacherCoursesForSection as $course)
                                <option value="{{ $course->id }}" {{ $course->id == $selectedCourse->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Grades Form --}}
                    <form id="gradesForm" class="space-y-6 mt-6">
                        {{-- Hidden Fields --}}
                        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                        <input type="hidden" name="school_year_id" value="{{ $schoolYearId }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">First Quarter</label>
                                <input type="number" name="prelim" min="0" max="100" step="0.01"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-base focus:ring-2 focus:ring-blue-500"
                                    value="{{ $grades?->prelim ?? '' }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Second Quarter</label>
                                <input type="number" name="midterm" min="0" max="100" step="0.01"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-base focus:ring-2 focus:ring-blue-500"
                                    value="{{ $grades?->midterm ?? '' }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Third Quarter</label>
                                <input type="number" name="prefinal" min="0" max="100" step="0.01"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-base focus:ring-2 focus:ring-blue-500"
                                    value="{{ $grades?->prefinal ?? '' }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Fourth Quarter</label>
                                <input type="number" name="final" min="0" max="100" step="0.01"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-base focus:ring-2 focus:ring-blue-500"
                                    value="{{ $grades?->final ?? '' }}">
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="button" onclick="submitGrades()"
                                class="bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition shadow">
                                Save Grades
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Handle course selection change
        document.getElementById('course_selection').addEventListener('change', function () {
            const selectedCourseId = this.value;
            // Reload the page with the selected course_id as a query parameter
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('course_id', selectedCourseId);
            window.location.href = currentUrl.toString();
        });

        // Function to submit grades
        function submitGrades() {
            const formData = {
                // Include the IDs in the data sent to the server
                subject_id: document.querySelector('input[name="subject_id"]').value,
                school_year_id: document.querySelector('input[name="school_year_id"]').value,
                prelim: document.querySelector('input[name="prelim"]').value,
                midterm: document.querySelector('input[name="midterm"]').value,
                prefinal: document.querySelector('input[name="prefinal"]').value,
                final: document.querySelector('input[name="final"]').value
            };

            fetch('{{ route("teacher.save.grades", $student->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Grades saved successfully!');
                        window.location.href = '{{ route("teacher.dashboard") }}';
                    } else {
                        alert('Error saving grades: ' + (data.message || 'Please check input values.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving grades. Please try again.');
                });
        }

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