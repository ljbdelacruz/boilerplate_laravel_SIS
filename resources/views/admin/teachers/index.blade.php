@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Teacher Information</h1>
        <div class="flex gap-4">
            <a href="{{ route('teachers.upload') }}"
               onclick="event.preventDefault(); loadContent('{{ route('teachers.upload') }}', 'Batch Upload Teacher');"
               class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
               Batch Upload
            </a>
        </div>
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
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($teachers as $teacher)
                    <tr class="hover:bg-yellow-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ $teacher->name }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $teacher->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('teachers.edit', $teacher->id) }}"
                               onclick="event.preventDefault(); loadContent('{{ route('teachers.edit', $teacher->id) }}', 'Edit Teacher');"
                               class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                               Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
