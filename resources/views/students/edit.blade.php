<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Edit Student</h1>
                <div class="flex space-x-4">
                    <a href="{{ route('dashboard') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
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
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="lrn">
                            LRN
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="lrn" type="text" name="lrn" value="{{ old('lrn', $student->lrn) }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="student_id">
                            Student ID
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               id="student_id"
                               type="text"
                               name="student_id"
                               value="{{ old('student_id', $student->student_id) }}"
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
                               value="{{ old('first_name', $student->first_name) }}"
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
                               value="{{ old('last_name', $student->last_name) }}"
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
                               value="{{ old('middle_name', $student->middle_name) }}">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="birth_date">
                            Birth Date
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               id="birth_date"
                               type="date"
                               name="birth_date"
                               value="{{ old('birth_date', $student->birth_date) }}"
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
                            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                            Address
                        </label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                  id="address"
                                  name="address"
                                  required>{{ old('address', $student->address) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_number">
                            Contact Number
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               id="contact_number"
                               type="text"
                               name="contact_number"
                               value="{{ old('contact_number', $student->contact_number) }}">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               id="email"
                               type="email"
                               name="email"
                               value="{{ old('email', $student->email) }}">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="guardian_name">
                            Guardian Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               id="guardian_name"
                               type="text"
                               name="guardian_name"
                               value="{{ old('guardian_name', $student->guardian_name) }}"
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
                               value="{{ old('guardian_contact', $student->guardian_contact) }}"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="school_year_id">
                            School Year
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="school_year_id"
                                name="school_year_id"
                                required>
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
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit">
                        Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>