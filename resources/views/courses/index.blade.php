@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Subjects</h1>
        </div>

        @if(session('success'))
            <div id="successAlert"
                class="transition-all duration-500 ease-in-out max-h-40 opacity-100 translate-y-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
            {{-- Filter and Add Button Row Inside Table Container --}}
            <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="GET" action="{{ route('courses.index') }}" class="flex items-center space-x-2">
                    <label for="grade_level" class="text-sm font-medium text-gray-700">Filter by:</label>
                    <select id="grade_level" name="grade_level" onchange="this.form.submit()"
                        class="custom-select block w-48 pl-3 pr-10 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Grade Levels</option>
                        @foreach($gradeLevels as $grade)
                            <option value="{{ $grade->grade_level }}" {{ $selectedGradeLevel == $grade->grade_level ? 'selected' : '' }}>
                                {{ $grade->grade_level }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('courses.create') }}"
                    onclick="event.preventDefault(); loadContent('{{ route('courses.create') }}', 'Add Subject', 'courses');"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Add New Subject
                </a>
            </div>

            <table class="min-w-full text-center text-sm divide-y divide-gray-300 fade-in">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($courses as $course)
                        <tr class="hover:bg-yellow-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $course->code }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $course->name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $course->description }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $course->grade_level }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('courses.edit', $course->id) }}"
                                    onclick="event.preventDefault(); loadContent('{{ route('courses.edit', $course->id) }}', 'Edit Subject', 'courses');"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                                    Edit
                                </a>
                                <form action="{{ route('courses.archive', $course->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to archive this course?')"
                                            class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition duration-200">
                                        Archive
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <style>
        select.custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 0.875rem;
            padding-right: 2.25rem;

            background-color: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            transition: box-shadow 0.2s ease-in-out;
        }

        .fade-in {
            opacity: 1;
            transform: translateY(0);
            max-height: 500px;
            /* enough space for content */
            margin-bottom: 1rem;
            padding-top: 1rem;
            padding-bottom: 1rem;
            transition: all 0.5s ease-in-out;
            overflow: hidden;
        }

        .fade-out {
            opacity: 0;
            transform: translateY(20px);
            max-height: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            transition: all 0.5s ease-in-out;
            overflow: hidden;
        }
    </style>

    <script>
       document.addEventListener('DOMContentLoaded', () => {
                const fadeWithDelay = (id) => {
                    const el = document.getElementById(id);
                    if (el) {
                        // Add fade-in immediately
                        el.classList.add('fade-in');

                        // Then remove fade-in and add fade-out after 5s
                        setTimeout(() => {
                            el.classList.remove('fade-in');
                            el.classList.add('fade-out');
                        }, 5000);
                    }
                };

                fadeWithDelay('successAlert');
                fadeWithDelay('errorAlert');
            });
    </script>
    </style>
@endsection