<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

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
                    <table
                        class="min-w-full table-fixed text-sm text-gray-700 border border-gray-200 rounded-lg text-center">
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
                                    <td class="px-6 py-4 w-1/4">{{ date('h:i A', strtotime($schedule->start_time)) }} -
                                        {{ date('h:i A', strtotime($schedule->end_time)) }}
                                    </td>
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
                <!-- Filter Dropdowns -->
                <form method="GET" action="{{ route('teacher.dashboard') }}"
                    class="mb-6 flex flex-wrap items-center gap-4">
                    <label for="filter_course" class="text-base font-semibold text-gray-700">Filter by Subject/Course:</label>

                    <select id="filter_course" name="course_id"
                        class="custom-select w-60 px-3 py-2 rounded-lg border border-gray-300" required>
                        <option value="">Select a Course</option>
                        @foreach($teacherCourses as $option)
                            <option value="{{ $option->filter_value }}" {{ (request('course_id') == $option->filter_value || $selectedCourseId == $option->filter_value) ? 'selected' : '' }}>
                                {{ $option->display_text }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Filter
                    </button>
                </form>

                <!-- Course & Sections Info -->
                <div id="sectionInfo" class="fade-section w-full mb-4" style="display: none;">
                    @if($selectedCourseModel)
                        <div class="flex flex-wrap gap-6 mb-2">
                            <p class="text-lg font-bold text-gray-800">
                                Course:
                                <span class="text-blue-600 font-semibold">{{ $selectedCourseModel->name }}</span>
                            </p>
                        </div>
                        @if($sectionsForSelectedCourse->isNotEmpty())
                            <p class="text-lg font-bold text-gray-800">
                                Taught in Section{{ $sectionsForSelectedCourse->count() > 1 ? 's' : '' }}:
                                @foreach($sectionsForSelectedCourse as $section)
                                    <span class="text-green-600 font-semibold">{{ $section->grade_level }} - {{ $section->name }}</span>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        @else
                            <p class="text-gray-600">This course is not currently scheduled in any specific sections for you.</p>
                        @endif
                    @endif
                </div>
                <form method="POST">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $selectedCourseId ?? '' }}">
                    <input type="hidden" name="school_year_id" value="{{ $activeSchoolYearId ?? '' }}">

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full table-fixed text-sm text-gray-700 border border-gray-200 rounded-lg text-center">
                            <thead class="bg-yellow-100 text-gray-800 uppercase text-xs font-bold">
                                <tr>
                                    <th class="w-1/4 px-6 py-3">Name</th>
                                    <th class="w-1/6 px-6 py-3">1st Quarter</th>
                                    <th class="w-1/6 px-6 py-3">2nd Quarter</th>
                                    <th class="w-1/6 px-6 py-3">3rd Quarter</th>
                                    <th class="w-1/6 px-6 py-3">4th Quarter</th>
                                    <th class="w-1/6 px-6 py-3">Final Rating</th>
                                    <th class="w-1/6 px-6 py-3">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    @php
                                        $grades = null;
                                        if ($selectedCourseId && $activeSchoolYearId) {
                                            $grades = $student->grades 
                                                ->where('subject_id', (int)$selectedCourseId)
                                                ->where('school_year_id', $activeSchoolYearId)
                                                ->first();
                                        }

                                        $finalRating = null;
                                        $remarks = '';
                                        if ($grades) {
                                            $quarter_grades_values = array_filter([$grades->prelim, $grades->midterm, $grades->prefinal, $grades->final], 'is_numeric');
                                            if (count($quarter_grades_values) > 0) {
                                                $finalRating = round(array_sum($quarter_grades_values) / count($quarter_grades_values));
                                                $remarks = $finalRating >= 75 ? 'PASSED' : 'FAILED';
                                            }
                                        }
                                    @endphp

                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-yellow-50">
                                        <td class="px-6 py-4 w-1/4 text-left">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </td>
                                        <td class="px-6 py-4 w-1/6 text-center">
                                            <input type="number" name="grades[{{ $student->id }}][prelim]" min="0" max="100"
                                                step="0.01"
                                                class="w-20 text-center bg-yellow-100 px-2 py-1 border border-gray-300 rounded"
                                                value="{{ $grades->prelim ?? '' }}">
                                        </td>
                                        <td class="px-6 py-4 w-1/6 text-center">
                                            <input type="number" name="grades[{{ $student->id }}][midterm]" min="0"
                                                max="100" step="0.01"
                                                class="w-20 text-center bg-yellow-100 px-2 py-1 border border-gray-300 rounded"
                                                value="{{ $grades->midterm ?? '' }}">
                                        </td>
                                        <td class="px-6 py-4 w-1/6 text-center">
                                            <input type="number" name="grades[{{ $student->id }}][prefinal]" min="0"
                                                max="100" step="0.01"
                                                class="w-20 text-center bg-yellow-100 px-2 py-1 border border-gray-300 rounded"
                                                value="{{ $grades->prefinal ?? '' }}">
                                        </td>
                                        <td class="px-6 py-4 w-1/6 text-center">
                                            <input type="number" name="grades[{{ $student->id }}][final]" min="0" max="100"
                                                step="0.01"
                                                class="w-20 text-center bg-yellow-100 px-2 py-1 border border-gray-300 rounded"
                                                value="{{ $grades->final ?? '' }}">
                                        </td>
                                        <td class="px-6 py-4 w-1/6 text-center font-semibold">
                                            {{ $finalRating ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 w-1/6 text-center font-semibold {{ $remarks === 'PASSED' ? 'text-green-600' : ($remarks === 'FAILED' ? 'text-red-600' : '') }}">
                                            {{ $remarks }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-500">No students available for the selected course.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="submitAllGrades()"
                            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-2 rounded-lg text-sm">
                            Save Grades
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <style>
        select.custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 0.875rem;
            padding-right: 2.25rem;

            background-color: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            transition: box-shadow 0.2s ease-in-out;
        }

        .fade-in {
            opacity: 1;
            transform: translateY(0);
            max-height: 500px;
            margin-bottom: 1rem;
            padding-top: 1rem;
            padding-bottom: 1rem;
            transition: all 0.5s ease-in-out;
            overflow: hidden;
        }

        .fade-section {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .fade-section.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
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

        document.addEventListener('DOMContentLoaded', function () {
            const sectionInfo = document.getElementById('sectionInfo');
            const courseSelect = document.getElementById('filter_course'); 

            @if($selectedCourseId && $selectedCourseModel)
                sectionInfo.style.display = 'block'; 
                requestAnimationFrame(() => {
                    sectionInfo.classList.add('show');
                });
            @endif

    });

    function submitAllGrades() {
        const subjectId = document.querySelector('input[name="subject_id"]').value;
        const schoolYearId = document.querySelector('input[name="school_year_id"]').value;
        const studentRows = document.querySelectorAll('table tbody tr');
        let promises = [];
        let successCount = 0;
        let errorCount = 0;
        let errorMessages = [];

        if (!subjectId) {
            alert('Please select a subject/course first.');
            return;
        }

        studentRows.forEach(row => {
            const studentIdInput = row.querySelector('input[name^="grades["]');
            if (!studentIdInput) return; // Skip header or empty rows

            const studentIdMatch = studentIdInput.name.match(/grades\[(\d+)\]/);
            if (!studentIdMatch) return;
            const studentId = studentIdMatch[1];

            const prelim = row.querySelector(`input[name="grades[${studentId}][prelim]"]`).value;
            const midterm = row.querySelector(`input[name="grades[${studentId}][midterm]"]`).value;
            const prefinal = row.querySelector(`input[name="grades[${studentId}][prefinal]"]`).value;
            const final = row.querySelector(`input[name="grades[${studentId}][final]"]`).value;

            const formData = {
                subject_id: subjectId,
                school_year_id: schoolYearId,
                prelim: prelim || null, // Send null if empty
                midterm: midterm || null,
                prefinal: prefinal || null,
                final: final || null
            };

            promises.push(
                fetch(`/save-grades/${studentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data, studentId: studentId })))
                .then(result => {
                    if (result.body.success) {
                        successCount++;
                    } else {
                        errorCount++;
                        errorMessages.push(`Student ID ${result.studentId}: ${result.body.message || result.body.error || 'Unknown error'}`);
                    }
                })
                .catch(error => {
                    errorCount++;
                    errorMessages.push(`Student ID ${studentId}: Network or parsing error - ${error.message}`);
                })
            );
        });

        Promise.all(promises).then(() => {
            let alertMessage = `${successCount} student(s) grades saved successfully.`;
            if (errorCount > 0) {
                alertMessage += `\n${errorCount} student(s) grades failed to save.\nDetails:\n${errorMessages.join('\n')}`;
            }
            alert(alertMessage);
            if (errorCount === 0 && successCount > 0) {
                window.location.reload(); 
            }
        });
    }
    </script>
</body>

</html>