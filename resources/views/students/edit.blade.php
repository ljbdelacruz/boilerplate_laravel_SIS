@extends('dashboard.admin')

@section('title', 'Edit Student')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Student</h1>
            <div class="flex space-x-4">
            <a href="{{ route('students.index') }}"
                onclick="event.preventDefault(); 
                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                    loadContent('{{ route('students.index') }}', schoolYearLink || 'Students');"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow transition">
                ← Back to List
            </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('students.update', $student->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-6">
                <!-- LRN -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="lrn">LRN</label>
                    <input id="lrn" type="text" name="lrn" value="{{ old('lrn', $student->lrn) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <!-- Student ID -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="student_id">Student ID</label>
                    <input id="student_id" type="text" name="student_id" value="{{ old('student_id', $student->student_id) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- First Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">First Name</label>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Last Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">Last Name</label>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Middle Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="middle_name">Middle Name</label>
                    <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Birth Date -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="birth_date">Birth Date</label>
                    <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Gender -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">Gender</label>
                    <select id="gender" name="gender" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Address -->
                <div class="mb-4 col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Address</label>
                    <textarea id="address" name="address" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('address', $student->address) }}</textarea>
                </div>

                <!-- Contact Number -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_number">Contact Number</label>
                    <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $student->email) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Guardian Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="guardian_name">Guardian Name</label>
                    <input id="guardian_name" type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Guardian Contact -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="guardian_contact">Guardian Contact</label>
                    <input id="guardian_contact" type="text" name="guardian_contact" value="{{ old('guardian_contact', $student->guardian_contact) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- School Year -->
                <div class="mb-4 col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="school_year_id">School Year</label>
                    <select id="school_year_id" name="school_year_id" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select School Year</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear->id }}" {{ old('school_year_id', $student->school_year_id) == $schoolYear->id ? 'selected' : '' }}>
                                {{ $schoolYear->school_year }} - {{ $schoolYear->grade_level }} {{ $schoolYear->section_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
