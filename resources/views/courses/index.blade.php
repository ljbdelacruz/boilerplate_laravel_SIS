@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Subjects</h1>
        <a href="{{ route('courses.create') }}"
                onclick="event.preventDefault(); loadContent('{{ route('courses.create') }}', 'Add Subject');"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Add New Subject
            </a>
        
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
    <table class="min-w-full text-center text-sm divide-y divide-gray-300">
        <thead class="bg-yellow-100">
            <tr>
                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($courses as $course)
                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $course->code }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $course->name }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $course->description }}</td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('courses.edit', $course->id) }}" 
                        onclick="event.preventDefault(); loadContent('{{ route('courses.edit', $course->id) }}', 'Edit Subject');"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                            Edit
                        </a>
                        <form action="{{ route('courses.archive', $course->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <button type="submit" onclick="return confirm('Are you sure you want to archive this course?')" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition duration-200">
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
@endsection
