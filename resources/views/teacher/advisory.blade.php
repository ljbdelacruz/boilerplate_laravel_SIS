<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher | Advisory Class</title>
    <link rel="icon" href="{{ asset('icons/Logo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
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
        select.custom-filter-select {
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
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-gray-100">
    <nav class="relative bg-gray-50 h-16 px-4 flex items-center justify-between sticky top-0 z-40 shadow-lg"
        style="background-color: #EAD180; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);">
        <div class="flex items-center gap-2">
            <img src="{{ asset('icons/Logo.png') }}" alt="Ususan Logo" class="h-10 w-10 object-contain">
            <span class="font-bold text-lg text-gray-900">Ususan Elementary School</span>
        </div>

        {{-- Right: User Info + Dropdown --}}
        <div class="flex items-center gap-4 relative">
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
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 leading-snug">
                My Advisory Class
                @if($advisorySection)
                    : {{ $advisorySection->grade_level }} - {{ $advisorySection->name }}
                @endif
            </h2>
            <a href="{{ route('teacher.dashboard') }}"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                ← Back to Dashboard
            </a>
        </div>
        <!-- Filter and Course Info -->
        @if($advisorySection)
        <div class="mb-6 p-4 bg-white shadow-xl rounded-lg">
            <form method="GET" action="{{ route('advisory.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div>
                    <label for="filter_course" class="text-base font-semibold text-gray-700 mr-2">View Grades for Subject:</label>
                    <select id="filter_course" name="course_id"
                            class="custom-filter-select w-full sm:w-72 px-3 py-2 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                            onchange="this.form.submit()">
                        <option value="">-- Select a Subject --</option>
                        @foreach($coursesForFilter as $course)
                            <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if($selectedCourseModel)
                <div class="mt-4 pt-2">
                    <p class="text-lg font-bold text-gray-800">
                        Displaying Grades for:
                        <span class="text-purple-600 font-semibold">{{ $selectedCourseModel->name }}</span>
                    </p>
                    <p class="text-sm text-gray-600">
                        School Year: {{ $activeSchoolYear ? ($activeSchoolYear->start_year . ' - ' . $activeSchoolYear->end_year) : 'N/A' }}
                    </p>
                </div>
            @elseif($selectedCourseId)
                <div class="mt-4 pt-2 border-t border-yellow-200">
                    <p class="text-red-600 font-semibold">The selected subject (ID: {{ $selectedCourseId }}) could not be found or is not applicable.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Advisory Class Student List -->
        <div class="bg-white shadow-xl rounded-2xl p-6">
            @if($advisorySection && $advisoryStudents->isNotEmpty())
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Student List</h3>
                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full table-auto text-sm text-gray-700 border border-gray-200 rounded-lg">
                        <thead class="bg-yellow-100 text-gray-800 uppercase text-xs font-medium text-center">
                            <tr class="text-center">
                                <th class="px-4 py-3  text-center">LRN</th>
                                <th class="px-4 py-3  text-center">Name</th>
                                @if($selectedCourseModel)
                                <th class="px-3 py-3  text-center">Q1</th>
                                <th class="px-3 py-3  text-center">Q2</th>
                                <th class="px-3 py-3  text-center">Q3</th>
                                <th class="px-3 py-3  text-center">Q4</th>
                                <th class="px-3 py-3  text-center">Final</th>
                                <th class="px-3 py-3  text-center">Remarks</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($advisoryStudents as $student)
                                @php
        $grades = null;
        $finalRating = null;
        $remarks = '';
        if ($selectedCourseModel && $activeSchoolYear) {
            $grades = $student->grades->where('subject_id', $selectedCourseModel->id)->where('school_year_id', $activeSchoolYear->id)->first();
            if ($grades) {
                $quarter_grades_values = array_filter([$grades->prelim, $grades->midterm, $grades->prefinal, $grades->final], 'is_numeric');
                if (count($quarter_grades_values) > 0) {
                    $finalRating = round(array_sum($quarter_grades_values) / count($quarter_grades_values));
                    $remarks = $finalRating >= 75 ? 'PASSED' : 'FAILED';
                }
            }
        }
                                @endphp
                                    <tr class="hover:bg-yellow-50">
                                        <td class="px-4 py-3  text-center">{{ $student->lrn ?? 'N/A' }}</td>
                                        <td class="px-4 py-3  text-center">{{ $student->first_name }} {{ $student->last_name }}</td>
                                        @if($selectedCourseModel)
                                        <td class="px-3 py-3 text-center">{{ $grades->prelim ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center">{{ $grades->midterm ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center">{{ $grades->prefinal ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center">{{ $grades->final ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center font-semibold">{{ $finalRating ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center font-semibold {{ $remarks === 'PASSED' ? 'text-green-600' : ($remarks === 'FAILED' ? 'text-red-600' : '') }}">
                                            {{ $remarks }}
                                        </td>
                                        @endif
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Confirm Grades Button -->
                <div class="mt-6 flex justify-end">
                    <button id="confirmGradesButton" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition hidden">
                        Confirm Grades
                    </button>
                </div>
            @elseif($advisorySection && $advisoryStudents->isEmpty())
                <p class="text-gray-600">No students found in your advisory class: {{ $advisorySection->grade_level }} - {{ $advisorySection->name }}.</p>
                @elseif(!$advisorySection && Auth::user()->role === 'teacher')
                <p class="text-gray-600">You are not currently assigned as an adviser to any section for the active school year, or no advisory class information is available.</p>
            @else
                <p class="text-gray-600">You are not currently assigned as an adviser to any section, or no advisory class information is available.</p>
            @endif
        </div>
    </div>

    <!-- Grades Confirmed Modal -->
    <div id="gradesConfirmedModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] hidden">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md text-center border border-gray-300">
            <p class="text-gray-800 font-medium text-sm whitespace-nowrap overflow-hidden text-ellipsis">
                Grades have been successfully confirmed.
            </p>
            <div class="mt-6">
                <button id="closeModalButton"
                    class="bg-purple-500 hover:bg-purple-600 text-white text-sm font-semibold py-2 px-6 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-300 transition-all duration-200">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>




    <script>
        const toggleButton = document.getElementById('dropdownToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (toggleButton && dropdownMenu) {
            toggleButton.addEventListener('click', function (e) {
                e.preventDefault();
                dropdownMenu.classList.toggle('show');
            });

            window.addEventListener('click', function (e) {
                const isClickInsideButton = toggleButton && toggleButton.contains(e.target);
                const isClickInsideMenu = dropdownMenu && dropdownMenu.contains(e.target);

                if (!isClickInsideButton && !isClickInsideMenu) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }

        const confirmGradesBtn = document.getElementById('confirmGradesButton');
        const gradesModal = document.getElementById('gradesConfirmedModal');
        const closeModalBtn = document.getElementById('closeModalButton');
        const courseFilterSelect = document.getElementById('filter_course');

        function toggleConfirmButtonVisibility() {
            if (courseFilterSelect && confirmGradesBtn) {
                if (courseFilterSelect.value) { 
                    confirmGradesBtn.classList.remove('hidden');
                } else {
                    confirmGradesBtn.classList.add('hidden');
                }
            }
        }

        toggleConfirmButtonVisibility();

        if (confirmGradesBtn && gradesModal) { 
            confirmGradesBtn.addEventListener('click', function() {
                gradesModal.classList.remove('hidden');
            });
        }

        if (closeModalBtn && gradesModal) {
            closeModalBtn.addEventListener('click', function() {
                gradesModal.classList.add('hidden'); 
                window.location.href = "{{ route('teacher.dashboard') }}"; 
            });
        }

        if (gradesModal) {
            gradesModal.addEventListener('click', function(event) {
                if (event.target === gradesModal) { 
                    gradesModal.classList.add('hidden');
                }
            });
        }

        if (courseFilterSelect) {
            courseFilterSelect.addEventListener('change', toggleConfirmButtonVisibility);
        }
    </script>
</body>
</html>
