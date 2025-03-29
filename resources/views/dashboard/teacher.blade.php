<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold">Teacher Dashboard</span>
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
                <h3 class="text-lg font-semibold mb-4">My Classes</h3>
                <p class="text-gray-600">View and manage your classes</p>
                <div class="mt-4">
                    <a href="{{ route('teacher.classes') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        View Classes
                    </a>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Schedule Preferences</h3>
                <p class="text-gray-600">Set your teaching schedule preferences</p>
                <div class="mt-4">
                    <a href="{{ route('teacher.schedules.preferences') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Set Preferences
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>