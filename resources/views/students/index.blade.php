@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Student Information</h1>
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'teacher')
            <div class="flex gap-4">
                <a href="{{ route('students.create') }}"
                   onclick="event.preventDefault(); loadContent('{{ route('students.create') }}', 'Add Student');"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                   Add New Student
                </a>
                <a href="{{ route('students.upload') }}"
                   onclick="event.preventDefault(); loadContent('{{ route('students.upload') }}', 'Batch Upload Student');"
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                   Batch Upload
                </a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full text-center text-sm divide-y divide-gray-300">
            <thead class="bg-yellow-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Student ID</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">School Year</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($students as $student)
                    <tr class="hover:bg-yellow-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $student->student_id }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->last_name }}, {{ $student->first_name }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->schoolYear->start_year }} - {{ $student->schoolYear->end_year }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('students.show', $student) }}"
                               onclick="event.preventDefault(); loadContent('{{ route('students.show', $student) }}', 'View Student');"
                               class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                               View
                            </a>
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'teacher')
                            <a href="{{ route('students.edit', $student) }}"
                               onclick="event.preventDefault(); loadContent('{{ route('students.edit', $student) }}', 'Edit Student');"
                               class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                               Edit
                            </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
