@extends('dashboard.admin')

@section('title', 'Add School Year') 

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button Left-Aligned --}}
        <div class="flex justify-start mt-6 mb-4">
            <a href="{{ route('school-years.index') }}" 
                onclick="event.preventDefault(); 
                         const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                            .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'School Years'); 
                         loadContent('{{ route('school-years.index') }}', schoolYearLink || 'School Years');"
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
            
            <form action="{{ route('school-years.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="mb-4 text-left">
                    <label for="start_year" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Start Year</label>
                    <input type="number" 
                        name="start_year" 
                        id="start_year" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        min="2000" max="2099" 
                        value="{{ old('start_year', date('Y')) }}" 
                        required>
                </div>

                <div class="mb-4 text-left">
                    <label for="end_year" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">End Year</label>
                    <input type="number" 
                        name="end_year" 
                        id="end_year" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        min="2000" max="2099" 
                        value="{{ old('end_year', date('Y') + 1) }}" 
                        required>
                </div>

                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                        name="is_active" 
                        id="is_active"
                        class="form-checkbox h-5 w-5 text-blue-600 rounded"
                        {{ old('is_active') ? 'checked' : '' }}>
                    <label for="is_active" class="text-gray-800 font-medium" style="font-size: 22px;">
                        Set as Active School Year
                    </label>
                </div>

                <div class="flex justify-end">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition"
                            type="submit">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
