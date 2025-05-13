@extends('dashboard.admin')

@section('content')
  <div id="page-meta" data-title="Edit Schedule" data-parent="Schedules">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">

                {{-- Back Button --}}
                <div class="flex justify-start mb-4">
                    <a href="{{ route('schedules.index') }}"
                        onclick="event.preventDefault(); 
                                            const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                               .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Schedules'); 
                                           const title = schoolYearLink?.getAttribute('data-title') || 'Schedules'; 
                                            loadContent('{{ route('schedules.index') }}', title, 'schedules');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Schedule List
                    </a>
                </div>

                {{-- Error Display --}}
                @if ($errors->any())
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Edit Form --}}
                <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                    <form action="{{ route('schedules.update', $schedule) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6 text-left">
                            <label for="teacher_id" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Teacher</label>
                            <select name="teacher_id" id="teacher_id" required
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="" disabled>Select Teacher</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}"
                                        {{ $schedule->teacher_id == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="course_id" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Subject</label>
                            <select name="course_id" id="course_id" required
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="" disabled>Select Subject</option>
                                @foreach ($courses as $course)
                                <option value="{{ $course->id }}" data-grade-level="{{ $course->grade_level }}" {{ old('course_id', $schedule->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} ({{ $course->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="section_id" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Section</label>
                            <select name="section_id" id="section_id" required
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="" disabled>Select Section</option>
                                @foreach ($sections as $section)
                                <option value="{{ $section->id }}" data-grade-level="{{ $section->grade_level }}" {{ old('section_id', $schedule->section_id) == $section->id ? 'selected' : '' }} style="display: none;">
                                        {{ $section->name }} ({{ $section->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="school_year_id" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">School
                                Year</label>
                            <select name="school_year_id" id="school_year_id" required
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="" disabled>Select School Year</option>
                                @foreach ($schoolYears ?? [] as $schoolYear)
                                    <option value="{{ $schoolYear->id }}"
                                        {{ $schedule->school_year_id == $schoolYear->id ? 'selected' : '' }}>
                                        {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}@if ($schoolYear->is_active)
                                            (Active)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="start_time" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Start Time</label>
                            <input type="time" name="start_time" id="start_time" required
                                value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div class="mb-6 text-left">
                            <label for="end_time" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">End
                                Time</label>
                            <input type="time" name="end_time" id="end_time" required
                                value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div class="flex justify-end pt-4">
                           <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Update Schedule
                        </button>
                        </div>

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

                            .fade-in {
                                opacity: 1;
                                transform: translateY(0);
                                max-height: 500px;
                                /* enough space for content */
                                margin-bottom: 1rem;
                                padding-top: 1rem;
                                padding-bottom: 1rem;
                                transition: all 0.5s ease-in-out;
                                overflow: hidden;
                            }

                            .fade-out {
                                opacity: 0;
                                transform: translateY(20px);
                                max-height: 0;
                                margin-bottom: 0;
                                padding-top: 0;
                                padding-bottom: 0;
                                transition: all 0.5s ease-in-out;
                                overflow: hidden;
                            }
                        </style>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const courseSelect = document.getElementById('course_id');
                const sectionSelect = document.getElementById('section_id');
                const sectionOptions = sectionSelect.querySelectorAll('option');

                courseSelect.addEventListener('change', function() {
                    const selectedCourseOption = this.options[this.selectedIndex];
                    const selectedGradeLevel = selectedCourseOption.dataset.gradeLevel;

                    sectionSelect.value = '';

                    sectionOptions.forEach(option => {
                        if (option.value !== '') {
                            option.style.display = 'none';
                        }
                    });

                    // Show matching sections
                    if (selectedGradeLevel) {
                        sectionOptions.forEach(option => {
                            if (option.dataset.gradeLevel === selectedGradeLevel) {
                                option.style.display = 'block';
                            }
                        });
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                const fadeWithDelay = (id) => {
                    const el = document.getElementById(id);
                    if (el) {
                        // Add fade-in immediately
                        el.classList.add('fade-in');

                        // Then remove fade-in and add fade-out after 5s
                        setTimeout(() => {
                            el.classList.remove('fade-in');
                            el.classList.add('fade-out');
                        }, 5000);
                    }
                };

                fadeWithDelay('errorAlert');
            });

            function initializeScheduleFiltering() {
            const courseSelect = document.getElementById('course_id');
            const sectionSelect = document.getElementById('section_id');

            // If the elements are not on the page, do nothing.
            if (!courseSelect || !sectionSelect) {
                return;
            }

            // Function to handle the actual filtering
            function updateSectionDropdown() {
                // Always get the fresh list of section options from the current DOM
                const allSectionOptionNodes = sectionSelect.querySelectorAll('option');
                const selectedCourseOption = courseSelect.options[courseSelect.selectedIndex];
                const selectedGradeLevel = selectedCourseOption ? selectedCourseOption.dataset.gradeLevel : null;
                const previouslySelectedSectionId = sectionSelect.value; 

                let placeholderOption = null;
                let firstMatchingOption = null;
                let currentSelectionIsValid = false;

                allSectionOptionNodes.forEach(optionNode => {
                    if (optionNode.value === "") { 
                        placeholderOption = optionNode;
                        // Don't hide the placeholder, but ensure it's selected if nothing else matches
                    } else {
                        // Hide and disable all actual section options initially
                        optionNode.style.display = 'none';
                        optionNode.disabled = true;
                        optionNode.selected = false;
                    }
                });
                
                // Reset to placeholder initially
                if (placeholderOption) {
                    sectionSelect.value = ""; 
                }


                if (selectedGradeLevel) {
                    allSectionOptionNodes.forEach(optionNode => {
                        if (optionNode.value !== "" && optionNode.dataset.gradeLevel === selectedGradeLevel) {
                            optionNode.style.display = 'block'; 
                            optionNode.disabled = false;
                            if (!firstMatchingOption) {
                                firstMatchingOption = optionNode; 
                            }
                            if (optionNode.value === previouslySelectedSectionId) {
                                currentSelectionIsValid = true; 
                            }
                        }
                    });
                }

                // Restore selection or select placeholder
                if (currentSelectionIsValid) {
                    sectionSelect.value = previouslySelectedSectionId;
                } else {
                    // If no valid selection or no course selected, ensure placeholder is selected
                    if (placeholderOption) {
                        sectionSelect.value = ""; 
                    }
                }
            }

            // Add event listener to the course select
            courseSelect.addEventListener('change', updateSectionDropdown);

            // Initial call to filter sections based on the currently selected course
            updateSectionDropdown();
        }

        function initializeErrorAlertFading() {
            const fadeWithDelay = (id) => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('fade-out'); 
                    el.classList.add('fade-in');
                    setTimeout(() => {
                        el.classList.remove('fade-in');
                        el.classList.add('fade-out');
                    }, 5000);
                }
            };
            fadeWithDelay('errorAlert');
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                initializeScheduleFiltering();
                initializeErrorAlertFading();
            });
        } else {
            // DOMContentLoaded has already fired or script is loaded after initial DOM parse
            initializeScheduleFiltering();
            initializeErrorAlertFading();
        }
        </script>
    </div>
  </div>
@endsection
