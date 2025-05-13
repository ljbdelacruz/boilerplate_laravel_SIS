@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Edit Curriculum" data-parent="Curriculums">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">

                {{-- Validation Errors --}}
                @if ($errors->has('time_conflict'))
                    <div id="infoAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ $errors->first('time_conflict') }}
                    </div>
                @endif

                @if ($errors->has('duplicate_subject'))
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ $errors->first('duplicate_subject') }}
                    </div>
                @endif

                {{-- Form --}}
                <div class="bg-yellow-100 shadow-lg rounded-lg p-8">
                    <form action="{{ route('curriculums.update', $curriculum->id) }}" method="POST" id="curriculum-form">
                        @csrf
                        @method('PUT')

                        {{-- Section --}}
                        <div class="mb-6 text-left">
                            <label for="section_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                                Section
                            </label>
                            <select name="section_id" id="section_id" required
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" data-grade-level="{{ $section->grade_level }}" {{ old('section_id', $curriculum->section_id) == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }} ({{ $section->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div class="mb-6 text-left">
                            <label for="subject_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                                Subject
                            </label>
                            <select name="subject_id" id="subject_id" required data-selected-subject="{{ old('subject_id', $curriculum->subject_id) }}"
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select Subject</option>
                            </select>
                            @error('subject_id')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Start Time --}}
                        <div class="mb-4 text-left">
                            <label for="start_time" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Start Time</label>
                            <input type="time" name="start_time" id="start_time"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                value="{{ old('start_time', \Carbon\Carbon::parse($curriculum->start_time)->format('H:i')) }}"
                                required>
                            @error('start_time')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- End Time --}}
                        <div class="mb-4 text-left">
                            <label for="end_time" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">End
                                Time</label>
                            <input type="time" name="end_time" id="end_time"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                value="{{ old('end_time', \Carbon\Carbon::parse($curriculum->end_time)->format('H:i')) }}"
                                required>
                            @error('end_time')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-4 pt-4">
                            {{-- Cancel Button --}}
                            <a href="{{ route('curriculums.index') }}"
                                onclick="event.preventDefault(); 
                                                const curriculumLink = [...document.querySelectorAll('.nav-link')]
                                                    .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Curriculums'); 
                                                loadContent('{{ route('curriculums.index') }}', curriculumLink || 'Curriculums Module','curriculums');"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-5 rounded-lg shadow transition">
                                Cancel
                            </a>

                            {{-- Update Button --}}
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Update
                            </button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        select.custom-select {
            appearance: none;
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
    <script>
        (function() { // IIFE Start
            const API_BASE_URL = '{{ url('') }}'; 

            function initializeCurriculumSubjectFilter() {
                console.log('[Curriculum Edit] Attempting to initialize Subject Filter...');
            const sectionDropdown = document.getElementById('section_id');
            const subjectDropdown = document.getElementById('subject_id');

            if (!sectionDropdown || !subjectDropdown) {
                console.warn('[Curriculum Edit] Filter elements (section or subject dropdown) NOT FOUND. Expected IDs: section_id, subject_id. Skipping initialization.');
                return;
            }
            console.log('[Curriculum Edit] Filter elements found. Section:', sectionDropdown, 'Subject:', subjectDropdown);
            
            const persistedSubjectId = subjectDropdown.dataset.selectedSubject || null;
            console.log('[Curriculum Edit] Persisted Subject ID from data attribute:', persistedSubjectId);

            function updateSubjectDropdown(currentSubjectDropdown, gradeLevel, selectedSubjectId = null) {
                currentSubjectDropdown.innerHTML = '<option value="">Loading subjects...</option>';

                if (!gradeLevel) {
                    currentSubjectDropdown.innerHTML = '<option value="">Select Subject</option><option value="" disabled>Select a section first</option>';
                    return;
                }
                console.log(`[Curriculum Edit] Fetching subjects for grade level: ${gradeLevel}`);

                fetch(`${API_BASE_URL}/api/courses-by-grade-level?grade_level=${encodeURIComponent(gradeLevel)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Network response was not ok: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(courses => {
                        console.log('[Curriculum Edit] Received courses:', courses);
                        currentSubjectDropdown.innerHTML = '<option value="">Select Subject</option>';
                        if (courses.length > 0) {
                            courses.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.id;
                                option.textContent = `${course.name} (${course.grade_level})`;
                                if (selectedSubjectId && course.id == selectedSubjectId) {
                                    option.selected = true;
                                    console.log(`[Curriculum Edit] Auto-selecting subject: ${course.name} (ID: ${course.id})`);
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
                        console.error('[Curriculum Edit] Error fetching subjects:', error);
                        currentSubjectDropdown.innerHTML = `<option value="">Error loading subjects: ${error.message}</option>`;
                    });
            }

            // Initial call if a section is pre-selected 
            if (sectionDropdown.value && sectionDropdown.options[sectionDropdown.selectedIndex]) {
                const initialSelectedOption = sectionDropdown.options[sectionDropdown.selectedIndex];
                if (initialSelectedOption && initialSelectedOption.value !== "" && initialSelectedOption.dataset.gradeLevel) {
                    console.log('[Curriculum Edit] Initial section selected:', initialSelectedOption.textContent, 'Grade Level:', initialSelectedOption.dataset.gradeLevel);
                    updateSubjectDropdown(subjectDropdown, initialSelectedOption.dataset.gradeLevel, persistedSubjectId);
                } else {
                    console.log('[Curriculum Edit] Initial section is placeholder or has no grade level. Resetting subject dropdown.');
                     updateSubjectDropdown(subjectDropdown, null, null); 
                }
            } else {
                console.log('[Curriculum Edit] No initial section selected. Resetting subject dropdown.');
                 updateSubjectDropdown(subjectDropdown, null, null); 
            }

            sectionDropdown.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                console.log('[Curriculum Edit] Section changed. Selected option:', selectedOption ? selectedOption.textContent : 'None', 'Grade Level:', selectedOption ? selectedOption.dataset.gradeLevel : 'None');
                const gradeLevel = selectedOption ? selectedOption.dataset.gradeLevel : null; 
                updateSubjectDropdown(subjectDropdown, gradeLevel, null); 
            });
        }

        function initializeCurriculumErrorAlertFading() {
            ['errorAlert', 'infoAlert'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('fade-out'); 
                    el.classList.add('fade-in');
                    setTimeout(() => {
                        el.classList.remove('fade-in');
                        el.classList.add('fade-out');
                    }, 5000);
                }
            });
        }

        // Resets the dom
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

@endsection