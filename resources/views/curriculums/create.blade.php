@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Add Curriculum" data-parent="Curriculums">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">

                <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                    @if ($errors->any())
                        <div id="errorAlert" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('curriculums.store') }}" method="POST">
                        @csrf

                        <div class="mb-6 text-left">
                            <label for="section_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                                Section
                            </label>
                            <select name="section_id" id="section_id" required
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select Section</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" data-grade-level="{{ $section->grade_level }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }} ({{ $section->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="subject_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                                Subject
                            </label>
                            <select name="subject_id" id="subject_id" required data-selected-subject="{{ old('subject_id', '') }}"
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select Subject</option>
                                {{-- Options will be populated by JavaScript --}}
                            </select>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="start_time" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Start
                                Time</label>
                            <input type="time" name="start_time" id="start_time" required placeholder="Start Time"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div class="mb-6 text-left">
                            <label for="end_time" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">End
                                Time</label>
                            <input type="time" name="end_time" id="end_time" required placeholder="End Time"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div class="flex justify-end gap-4 pt-4">
                            <a href="{{ route('curriculums.index') }}"
                                onclick="event.preventDefault(); 
                                    const curriculumLink = [...document.querySelectorAll('.nav-link')]
                                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Curriculums'); 
                                    loadContent('{{ route('curriculums.index') }}', curriculumLink || 'Curriculums Module','curriculums');"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-5 rounded-lg shadow transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Save
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
                <script>
                    (function() { // IIFE Start
                        const API_BASE_URL = '{{ url('') }}'; 

                        function initializeCurriculumSubjectFilter() {
                            const sectionDropdown = document.getElementById('section_id');
                        const subjectDropdown = document.getElementById('subject_id');

                        if (!sectionDropdown || !subjectDropdown) {
                            console.warn('Curriculum filter elements (section or subject dropdown) not found. Skipping initialization.');
                            return;
                        }

                        const persistedSubjectId = subjectDropdown.dataset.selectedSubject || null;

                        function updateSubjectDropdown(currentSubjectDropdown, gradeLevel, selectedSubjectId = null) {
                            currentSubjectDropdown.innerHTML = '<option value="">Loading subjects...</option>';

                            if (!gradeLevel) {
                                currentSubjectDropdown.innerHTML = '<option value="">Select Subject</option><option value="" disabled>Select a section first</option>';
                                return;
                            }

                            fetch(`${API_BASE_URL}/api/courses-by-grade-level?grade_level=${encodeURIComponent(gradeLevel)}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Network response was not ok: ${response.statusText}`);
                                    }
                                    return response.json();
                                })
                                .then(courses => {
                                    currentSubjectDropdown.innerHTML = '<option value="">Select Subject</option>';
                                    if (courses.length > 0) {
                                        courses.forEach(course => {
                                            const option = document.createElement('option');
                                            option.value = course.id;
                                            option.textContent = `${course.name} (${course.grade_level})`;
                                            if (selectedSubjectId && course.id == selectedSubjectId) {
                                                option.selected = true;
                                            }
                                            currentSubjectDropdown.appendChild(option);
                                        });
                                    } else {
                                        const option = document.createElement('option');
                                        option.value = "";
                                        option.disabled = true;
                                        option.textContent = "No subjects available for this grade level";
                                        currentSubjectDropdown.appendChild(option);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching subjects:', error);
                                    currentSubjectDropdown.innerHTML = `<option value="">Error loading subjects: ${error.message}</option>`;
                                });
                        }

                        // Initial call if a section is pre-selected
                        if (sectionDropdown.value && sectionDropdown.options[sectionDropdown.selectedIndex]) {
                            const initialSelectedOption = sectionDropdown.options[sectionDropdown.selectedIndex];
                            if (initialSelectedOption && initialSelectedOption.value !== "" && initialSelectedOption.dataset.gradeLevel) {
                                updateSubjectDropdown(subjectDropdown, initialSelectedOption.dataset.gradeLevel, persistedSubjectId);
                            } else {
                                updateSubjectDropdown(subjectDropdown, null, null); 
                            }
                        } else {
                            updateSubjectDropdown(subjectDropdown, null, null); 
                        }

                        sectionDropdown.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const gradeLevel = selectedOption ? selectedOption.dataset.gradeLevel : null;
                            updateSubjectDropdown(subjectDropdown, gradeLevel, null); 
                        });
                    }

                    function initializeCurriculumErrorAlertFading() {
                        const el = document.getElementById('errorAlert');
                        if (el) {
                            el.classList.remove('fade-out'); 
                            el.classList.add('fade-in');
                            setTimeout(() => {
                                el.classList.remove('fade-in');
                                el.classList.add('fade-out');
                            }, 5000);
                        }
                    }

                    // Resets the DOM here
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', () => {
                            initializeCurriculumSubjectFilter();
                            initializeCurriculumErrorAlertFading();
                        });
                    } else {
                        initializeCurriculumSubjectFilter();
                        initializeCurriculumErrorAlertFading();
                    }
                    })(); // IIFE End
                </script>
            </div>
        </div>
    </div>

@endsection
