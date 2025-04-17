<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Student Details</h1>
                <div class="flex space-x-4">
                    <a href="{{ route('dashboard') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="grid grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">LRN</label>
                        <p class="text-gray-900">{{ $student->lrn }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Student ID</label>
                        <p class="text-gray-900">{{ $student->student_id }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                        <p class="text-gray-900">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Birth Date</label>
                        <p class="text-gray-900">{{ $student->birth_date }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Gender</label>
                        <p class="text-gray-900">{{ ucfirst($student->gender) }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                        <p class="text-gray-900">{{ $student->address }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Contact Number</label>
                        <p class="text-gray-900">{{ $student->contact_number ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <p class="text-gray-900">{{ $student->email ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Guardian Name</label>
                        <p class="text-gray-900">{{ $student->guardian_name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Guardian Contact</label>
                        <p class="text-gray-900">{{ $student->guardian_contact }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">School Year</label>
                        <p class="text-gray-900">{{ $student->schoolYear->school_year }} - {{ $student->schoolYear->grade_level }} {{ $student->schoolYear->section_name }}</p>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <a href="{{ route('students.edit', $student->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>