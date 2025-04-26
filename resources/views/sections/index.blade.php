@extends('dashboard.admin')

@section('title')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Sections</h1>
        <a href="{{ route('sections.create') }}"
           onclick="event.preventDefault(); loadContent('{{ route('sections.create') }}', 'Add Section');"
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
            Add New Section
        </a>
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
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Section Name</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Grade Level</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">School Year</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sections as $section)
                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $section->name }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $section->grade_level }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $section->schoolYear->school_year }}</td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('sections.edit', $section) }}"
                           onclick="event.preventDefault(); loadContent('{{ route('sections.edit', $section) }}', 'Edit Section');"
                           class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                            Edit
                        </a>
                        <form action="{{ route('sections.archive', $section) }}" method="POST" class="inline-block">
                            @csrf @method('PUT')
                            <button type="submit"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
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
