<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
                <h2 class="text-2xl font-bold mb-4">Edit User</h2>
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
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
                            value="{{ old('email', $user->email) }}" 
                            required>
                    </div>

                    <!-- Add password update fields -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            New Password (leave blank to keep current password)
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="password" 
                            type="password" 
                            name="password">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                            Confirm New Password
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation">
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
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
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
                                value="{{ old('specialization', $user->teacher->specialization ?? '') }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                                Bio
                            </label>
                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="bio" 
                                name="bio" 
                                rows="3">{{ old('bio', $user->teacher->bio ?? '') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_number">
                                Contact Number
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="contact_number" 
                                type="text" 
                                name="contact_number" 
                                value="{{ old('contact_number', $user->teacher->contact_number ?? '') }}">
                        </div>
                    </div>

                    <!-- Student Fields -->
                    <div id="studentFields" class="hidden">
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
                                value="{{ old('lrn', $user->student->student_id ?? '') }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="grade_level">
                                Grade Level
                            </label>
                            <select name="grade_level" id="grade_level" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Grade Level</option>
                                @for ($i = 7; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('grade_level', $user->student->grade_level ?? '') == $i ? 'selected' : '' }}>
                                        Grade {{ $i }}
                                    </option>
                                @endfor
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
                                    <option value="{{ $section->id }}" {{ old('section_id', $user->student->section_id ?? '') == $section->id ? 'selected' : '' }}>
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
                                    <option value="{{ $schoolYear->id }}" {{ old('school_year_id', $user->student->school_year_id ?? '') == $schoolYear->id ? 'selected' : '' }}>
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
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>