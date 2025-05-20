@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Edit Student" data-parent="Students">
        <div class="container mx-auto px-4">
            <div class="w-full">

                {{-- Back Button --}}
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold">Student's Information</h1>
                    <a href="{{ route('students.index') }}"
                        onclick="event.preventDefault(); 
                                                                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                                       .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                                                                   const title = schoolYearLink?.getAttribute('data-title') || 'Students'; 
                                                                    loadContent('{{ route('students.index') }}', title, 'students');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Student List
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

                {{-- Edit Form  --}}
                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="bg-yellow-100 shadow-lg rounded-lg p-4 pb-6 overflow-x-auto">
                        <div class="min-w-[1100px]">
                            <div class="grid grid-cols-2 gap-6">
                                {{-- Personal Information --}}
                                <div class="px-2">
                                    <label for="lrn"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">LRN</label>
                                    <input type="text" id="lrn" name="lrn" value="{{ $student->lrn }}" readonly
                                        class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700" 
                                        pattern="[0-9]" title="Please enter only numbers."
                                        maxlength="12" minlength="12"/>
                                </div>

                                <div class="px-2">
                                    <label for="student_id"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Student ID</label>
                                    <input type="text" id="student_id" name="student_id"
                                        value="{{ $student->student_id }}" readonly
                                        class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700" 
                                        pattern="[0-9]+" title="Please enter only numbers."
                                        maxlength="10" minlength="10"/>
                                </div>

                                <div class="px-2">
                                    <label for="first_name"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">First Name</label>
                                    <input type="text" id="first_name" name="first_name"
                                        value="{{ old('first_name', $student->first_name) }}" required
                                        placeholder="Enter first name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                                        pattern="[A-Za-z]+" title="Please enter only letters."/>
                                </div>

                                <div class="px-2">
                                    <label for="last_name"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Last Name</label>
                                    <input type="text" id="last_name" name="last_name"
                                        value="{{ old('last_name', $student->last_name) }}" required
                                        placeholder="Enter last name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                                        pattern="[A-Za-z]+" title="Please enter only letters." />
                                </div>

                                <div class="px-2">
                                    <label for="middle_name"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Middle Name</label>
                                    <input type="text" id="middle_name" name="middle_name"
                                        value="{{ old('middle_name', $student->middle_name) }}"
                                        placeholder="Enter middle name (optional)"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                                        pattern="[A-Za-z]+" title="Please enter only letters."/>
                                </div>

                                <div class="px-2">
                                    <label for="suffix"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Suffix</label>
                                    <input type="text" id="suffix" name="suffix"
                                        value="{{ old('suffix', $student->suffix) }}"
                                        placeholder="Enter suffix (optional)"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                                        pattern="[A-Za-z]+" title="Please enter only letters."
                                        maxlength="5"/>
                                </div>

                                <div class="px-2">
                                    <label for="birth_date"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Birth Date</label>
                                    <input type="date" id="birth_date" name="birth_date"
                                        value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                                </div>

                                <div class="px-2">
                                    <label for="gender"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Gender</label>
                                    <select id="gender" name="gender" required
                                        class="custom-select w-56 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 block">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="male"
                                            {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female"
                                            {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                        <option value="other"
                                            {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Prefer Not To Say
                                        </option>
                                    </select>
                                </div>

                                <div class="col-span-2 px-2">
                                    <label for="address"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Address</label>
                                    <textarea id="address" name="address" required placeholder="Enter full home address"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('address', $student->address) }}</textarea>
                                </div>

                                <div class="px-2">
                                    <label for="contact_number"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Contact
                                        Number</label>
                                    <input type="text" id="contact_number" name="contact_number"
                                        value="{{ old('contact_number', $student->contact_number) }}"
                                        placeholder="Enter contact number"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                                        pattern="[0-9]+" title="Please enter only numbers."
                                        maxlength="11"/>
                                </div>

                                <div class="px-2">
                                    <label for="email"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Email Address</label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email', $student->email) }}"
                                        placeholder="Enter student email address"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                                </div>

                                <div class="px-2">
                                    <label for="guardian_name"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Guardian
                                        Name</label>
                                    <input type="text" id="guardian_name" name="guardian_name"
                                        value="{{ old('guardian_name', $student->guardian_name) }}" required
                                        placeholder="Enter guardian's full name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                                        pattern="[A-Za-z]+" title="Please enter only letters."/>
                                </div>

                                <div class="px-2">
                                    <label for="guardian_contact"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Guardian
                                        Contact</label>
                                    <input type="text" id="guardian_contact" name="guardian_contact"
                                        value="{{ old('guardian_contact', $student->guardian_contact) }}" required
                                        placeholder="Enter guardian's contact number"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                                        pattern="[0-9]+" title="Please enter only numbers."
                                        maxlength="11"/>
                                </div>

                                {{-- School Details --}}
                                <div class="px-2">
                                    <label for="school_year_id"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">School Year</label>
                                    <select id="school_year_id" name="school_year_id" required
                                        class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                        @foreach ($schoolYears as $schoolYear)
                                            <option value="{{ $schoolYear->id }}"
                                                {{ old('school_year_id') == $schoolYear->id ? 'selected' : '' }}>
                                                {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                                                @if ($schoolYear->is_active)
                                                    (Active)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="px-2">
                                    <label for="grade_level"
                                        class="block text-gray-800 font-medium mb-2 text-lg text-left">Grade Level</label>
                                    <select id="grade_level" name="grade_level" required
                                        class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                        <option value="" disabled>Select Grade Level</option>
                                        @foreach ($gradeLevels as $gradeLevel)
                                            <option value="{{ $gradeLevel->grade_level }}"
                                                {{ old('grade_level', $student->grade_level) == $gradeLevel->grade_level ? 'selected' : '' }}>
                                                {{ $gradeLevel->grade_level }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="px-2">
                                    <label for="section_id" class="block text-gray-800 font-medium mb-2 text-lg text-left">Section</label>
                                    <select id="section_id" name="section_id" required
                                        class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                        <option value="">Select Section</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" data-school-year-id="{{ $section->school_year_id }}"
                                                data-grade-level="{{ $section->grade_level }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }} ({{ $section->grade_level }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="col-span-2 flex justify-end px-2">
                                    <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                        Update Student
                                    </button>
                                </div>

                                <style>
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
                                </style>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
            (function() { // IIFE Start 

                function initializeStudentSectionFilter() {
                    console.log('[Student Create] Initializing section filter...');
                    const schoolYearSelect = document.getElementById('school_year_id');
                    const gradeLevelSelect = document.getElementById('grade_level');
                    const sectionSelect = document.getElementById('section_id');

                    if (!schoolYearSelect || !gradeLevelSelect || !sectionSelect) {
                        console.warn('[Student Create] One or more select elements (school_year_id, grade_level, section_id) not found. Skipping section filter initialization.');
                        return;
                    }

                    // Get all actual section options, excluding the placeholder
                    const allSectionOptionElements = Array.from(sectionSelect.options).filter(opt => opt.value !== "");

                    function filterSections() {
                        const selectedSchoolYearId = schoolYearSelect.value;
                        const selectedGradeLevel = gradeLevelSelect.value;
                        let currentSelectedSectionValue = sectionSelect.value;
                        let isCurrentSelectionStillVisible = false;

                        allSectionOptionElements.forEach(optionNode => {
                            const sectionSchoolYearId = optionNode.dataset.schoolYearId;
                            const sectionGradeLevel = optionNode.dataset.gradeLevel;
                            let show = false;

                            // Checks active school year and selected grade level to display sections
                            if (selectedSchoolYearId && selectedGradeLevel) {
                                if (sectionSchoolYearId === selectedSchoolYearId && sectionGradeLevel === selectedGradeLevel) {
                                    show = true;
                                }
                            }
                            optionNode.style.display = show ? '' : 'none';
                            if (show && optionNode.value === currentSelectedSectionValue) {
                                isCurrentSelectionStillVisible = true;
                            }
                        });

                        // If the previously selected option is now hidden, reset the select to placeholder
                        if (!isCurrentSelectionStillVisible && currentSelectedSectionValue !== "") {
                            sectionSelect.value = ""; 
                        }
                    }

                    schoolYearSelect.addEventListener('change', filterSections);
                    gradeLevelSelect.addEventListener('change', filterSections);

                    filterSections(); // Initial call to filter based on pre-selected values 
                    console.log('[Student Create] Section filter initialized.');
                }

                function initializeStudentAlertFading() {
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

                // Reset DOM
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        initializeStudentSectionFilter();
                        initializeStudentAlertFading();
                    });
                } else {
                    initializeStudentSectionFilter();
                    initializeStudentAlertFading();
                }

            })(); // IIFE End
        </script>
        </div>
    @endsection
