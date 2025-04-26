@extends('dashboard.admin')

@section('title', 'Add Student')

@section('content')
    <div class="container mx-auto px-4">
        <div class="w-full">

            {{-- Back Button Left-Aligned --}}
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Student's Information</h1>
                <a href="{{ route('students.index') }}"
                    onclick="event.preventDefault(); 
                 const studentLink = [...document.querySelectorAll('.nav-link')]
                    .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                 loadContent('{{ route('students.index') }}', studentLink || 'Students');"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow transition">
                    ← Back to List
                </a>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-yellow-100 shadow-lg rounded-lg p-4 overflow-x-auto">
    <div class="min-w-[1100px]">
        <form action="{{ route('students.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <div class="px-2">
                    <label for="lrn" class="block text-gray-800 font-medium mb-2 text-lg text-left">LRN</label>
                    <input id="lrn" name="lrn" value="{{ old('lrn') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="student_id" class="block text-gray-800 font-medium mb-2 text-lg text-left">Student ID</label>
                    <input id="student_id" name="student_id" value="{{ old('student_id') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="first_name" class="block text-gray-800 font-medium mb-2 text-lg text-left">First Name</label>
                    <input id="first_name" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="last_name" class="block text-gray-800 font-medium mb-2 text-lg text-left">Last Name</label>
                    <input id="last_name" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="middle_name" class="block text-gray-800 font-medium mb-2 text-lg text-left">Middle Name</label>
                    <input id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="birth_date" class="block text-gray-800 font-medium mb-2 text-lg text-left">Birth Date</label>
                    <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="gender" class="block text-gray-800 font-medium mb-2 text-lg text-left">Gender</label>
                    <select id="gender" name="gender" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- Address (Full Width) --}}
                <div class="col-span-2 px-2">
                    <label for="address" class="block text-gray-800 font-medium mb-2 text-lg text-left">Address</label>
                    <textarea id="address" name="address" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('address') }}</textarea>
                </div>

                <div class="px-2">
                    <label for="contact_number" class="block text-gray-800 font-medium mb-2 text-lg text-left">Contact Number</label>
                    <input id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="email" class="block text-gray-800 font-medium mb-2 text-lg text-left">Email Address</label>
                    <input id="email" type="email" name="email" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="guardian_name" class="block text-gray-800 font-medium mb-2 text-lg text-left">Guardian Name</label>
                    <input id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="guardian_contact" class="block text-gray-800 font-medium mb-2 text-lg text-left">Guardian Contact</label>
                    <input id="guardian_contact" name="guardian_contact" value="{{ old('guardian_contact') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                </div>
                <div class="px-2">
                    <label for="school_year_id" class="block text-gray-800 font-medium mb-2 text-lg text-left">School Year</label>
                    <select id="school_year_id" name="school_year_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        @foreach ($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear->id }}"
                                {{ old('school_year_id') == $schoolYear->id ? 'selected' : '' }}>
                                {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                                @if ($schoolYear->is_active) (Active) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="px-2">
                    <label for="grade_level" class="block text-gray-800 font-medium mb-2 text-lg text-left">Grade Level</label>
                    <select id="grade_level" name="grade_level" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select Grade Level</option>
                        @foreach ($gradeLevels as $gradeLevel)
                            <option value="{{ $gradeLevel->grade_level }}"
                                {{ old('grade_level') == $gradeLevel->grade_level ? 'selected' : '' }}>
                                {{ $gradeLevel->grade_level }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="px-2">
                    <label for="section_id" class="block text-gray-800 font-medium mb-2 text-lg text-left">Section</label>
                    <select id="section_id" name="section_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select Section</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}"
                                {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->name }} (Grade {{ $section->grade_level }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                    Add Student
                </button>
            </div>
        </form>
    </div>
</div>


        </div>
    </div>
    </div>
@endsection
