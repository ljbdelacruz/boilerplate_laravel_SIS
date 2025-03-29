<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold">Admin Dashboard</span>
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
                <h3 class="text-lg font-semibold mb-4">School Year Management</h3>
                <p class="text-gray-600">Manage school years and sections</p>
                <div class="mt-4">
                    <a href="{{ route('school-years.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New School Year
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Course Management</h3>
                <p class="text-gray-600">Manage courses and pricing</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('courses.index') }}" class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                        View All Courses
                    </a>
                    <a href="{{ route('courses.create') }}" class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                        Add New Course
                    </a>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">User Management</h3>
                <p class="text-gray-600">Manage system users and roles</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('users.index') }}" class="block bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                        View All Users
                    </a>
                    <a href="{{ route('users.create') }}" class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                        Add New User
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Student Management</h3>
                <p class="text-gray-600">Manage student records</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('students.index') }}" class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                        View All Students
                    </a>
                    <a href="{{ route('students.create') }}" class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                        Add New Student
                    </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Teacher Course Assignment</h3>
                <p class="text-gray-600">Manage teacher schedules and course assignments</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('teacher-courses.index') }}" class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                        View Assignments
                    </a>
                    <a href="{{ route('teacher-courses.create') }}" class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                        New Assignment
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>