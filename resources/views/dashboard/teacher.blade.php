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
        <!-- Add this new button at the top of the content area -->
        <div class="mb-6">
            <a href="{{ route('teacher.schedule') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                View My Schedule
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">View Students by Section</h3>
                
                <form action="{{ route('teacher.view.students') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="school_year" class="block text-sm font-medium text-gray-700">School Year</label>
                        <select name="school_year_id" id="school_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($schoolYears as $year)
                                <option value="{{ $year->id }}" {{ request('school_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->start_year }} - {{ $year->end_year }}
                                    @if($year->is_active) (Active) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                        <select name="section_id" id="section" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }} (Grade {{ $section->grade_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        View Students
                    </button>
                </form>

                @if(isset($students))
                    <div class="mt-6">
                        <h4 class="text-lg font-medium mb-4">Student List</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guardian</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $student)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->student_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->first_name }} {{ $student->last_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->contact_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $student->guardian_name }} ({{ $student->guardian_contact }})
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('teacher.submit.grades', $student->id) }}" 
                                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                                    Submit Grades
                                                </a>
                                                <a href="{{ route('teacher.student.sf10', $student->id) }}" 
                                                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                    SF10
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>