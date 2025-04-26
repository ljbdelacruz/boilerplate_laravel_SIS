@extends('dashboard.admin')

@section('title', 'Add Subject') 

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button Left-Aligned --}}
        <div class="flex justify-start mt-6 mb-4">
            <a href="{{ route('courses.index') }}" 
                onclick="event.preventDefault(); 
                         const courseLink = [...document.querySelectorAll('.nav-link')]
                            .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Subjects'); 
                         loadContent('{{ route('courses.index') }}', courseLink || 'Subjects');"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                ← Back to List
            </a>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-yellow-100 shadow-lg rounded-lg p-8">
            <form action="{{ route('courses.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="mb-6 text-left">
                    <label for="code" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Subject Code</label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                           required>
                </div>

                <div class="text-left">
                    <label for="name" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Subject Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                           required>
                </div>

                <div class="text-left">
                    <label for="description" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('description') }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                        Add
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
