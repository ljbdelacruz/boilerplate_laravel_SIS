<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                <span class="inline-flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </span>
            </a>
        </div>

        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4">
                <h2 class="text-2xl font-bold mb-4">Create New User</h2>
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="password" 
                            type="password" 
                            name="password" 
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                            Confirm Password
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                            Role
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="role" 
                            name="role" 
                            required>
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>

                    <!-- Teacher Fields -->
                    <div id="teacherFields" class="hidden">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="specialization">
                                Specialization
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="specialization" 
                                type="text" 
                                name="specialization" 
                                value="{{ old('specialization') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                                Bio
                            </label>
                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="bio" 
                                name="bio" 
                                rows="3">{{ old('bio') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_number">
                                Contact Number
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="contact_number" 
                                type="text" 
                                name="contact_number" 
                                value="{{ old('contact_number') }}"
                                required>
                        </div>
                    </div>

                    <!-- Student Fields -->
                    <div id="studentFields" class="hidden">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                                Address
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="address" 
                                type="text" 
                                name="address" 
                                value="{{ old('address') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">
                                First Name
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="first_name" 
                                type="text" 
                                name="first_name" 
                                value="{{ old('first_name') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">
                                Last Name
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="last_name" 
                                type="text" 
                                name="last_name" 
                                value="{{ old('last_name') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="middle_name">
                                Middle Name
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="middle_name" 
                                type="text" 
                                name="middle_name" 
                                value="{{ old('middle_name') }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="student_id">
                                Student ID
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="student_id" 
                                type="text" 
                                name="student_id" 
                                value="{{ old('student_id') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="birth_date">
                                Birth Date
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="birth_date" 
                                type="date" 
                                name="birth_date" 
                                value="{{ old('birth_date') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">
                                Gender
                            </label>
                            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="gender" 
                                name="gender" 
                                required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_number">
                                Contact Number
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="contact_number" 
                                type="text" 
                                name="contact_number" 
                                value="{{ old('contact_number') }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="guardian_name">
                                Guardian Name
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="guardian_name" 
                                type="text" 
                                name="guardian_name" 
                                value="{{ old('guardian_name') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="guardian_contact">
                                Guardian Contact
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="guardian_contact" 
                                type="text" 
                                name="guardian_contact" 
                                value="{{ old('guardian_contact') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="lrn">
                                LRN (12 digits)
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="lrn" 
                                type="text" 
                                name="lrn" 
                                pattern="\d{12}"
                                title="LRN must be exactly 12 digits"
                                value="{{ old('lrn') }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="grade_level">
                                Grade Level
                            </label>
                            <select name="grade_level" id="grade_level" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Grade Level</option>
                                @foreach($gradeLevels as $gradeLevel)
                                    <option value="{{ $gradeLevel->grade_level }}" {{ old('grade_level') == $gradeLevel->grade_level ? 'selected' : '' }}>
                                        {{ $gradeLevel->grade_level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="section_id">
                                Section
                            </label>
                            <select name="section_id" id="section_id" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Section</option>
                                @foreach($sections ?? [] as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="school_year_id">
                                School Year
                            </label>
                            <select name="school_year_id" id="school_year_id" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select School Year</option>
                                @foreach($schoolYears ?? [] as $schoolYear)
                                    <option value="{{ $schoolYear->id }}" {{ old('school_year_id') == $schoolYear->id ? 'selected' : '' }}>
                                        {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                                        @if($schoolYear->is_active) (Active) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <script>
                        document.getElementById('role').addEventListener('change', function() {
                            const teacherFields = document.getElementById('teacherFields');
                            const studentFields = document.getElementById('studentFields');
                            
                            teacherFields.classList.add('hidden');
                            studentFields.classList.add('hidden');
                            
                            if (this.value === 'teacher') {
                                teacherFields.classList.remove('hidden');
                            } else if (this.value === 'student') {
                                studentFields.classList.remove('hidden');
                            }
                        });

                        // Show fields if role was previously selected
                        const currentRole = document.getElementById('role').value;
                        if (currentRole === 'teacher') {
                            document.getElementById('teacherFields').classList.remove('hidden');
                        } else if (currentRole === 'student') {
                            document.getElementById('studentFields').classList.remove('hidden');
                        }
                    </script>
                    <div class="flex items-center justify-between">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                            type="submit">
                            Create User
                        </button>
                    </div>