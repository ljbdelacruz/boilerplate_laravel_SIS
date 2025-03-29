<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold">Student Dashboard</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-4">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">My Information</h3>
                <p class="text-gray-600">View your student information</p>
                <a href="{{ route('students.show', Auth::user()->id) }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    View Details →
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">My Schedule</h3>
                <p class="text-gray-600">View your class schedule</p>
                <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    View Schedule →
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Course Enrollment</h3>
                <p class="text-gray-600">Manage your course enrollments</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('student.courses.available') }}" class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                        Enroll in Courses
                    </a>
                    <a href="{{ route('student.courses.enrolled') }}" class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                        My Enrolled Courses
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Payment Information</h3>
                <p class="text-gray-600">View your payment status and history</p>
                <div class="mt-4">
                    <a href="{{ route('student.payments') }}" class="block bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                        View Payments
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>