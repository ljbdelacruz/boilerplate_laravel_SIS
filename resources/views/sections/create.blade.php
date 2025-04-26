@extends('dashboard.admin')

@section('title', 'Add Section') 

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button Left-Aligned --}}
        <div class="flex justify-start mt-6 mb-4">
            <a href="{{ route('sections.index') }}" 
                onclick="event.preventDefault(); 
                         const sectionLink = [...document.querySelectorAll('.nav-link')]
                            .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Sections'); 
                         loadContent('{{ route('sections.index') }}', sectionLink || 'Sections');"
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

            <form action="{{ route('sections.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="text-left">
                    <label for="name" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                        Section Name
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                           value="{{ old('name') }}"
                           required>
                </div>

                <div class="text-left">
                    <label for="grade_level" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                        Grade Level
                    </label>
                    <select name="grade_level" id="grade_level"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select Grade Level</option>
                        @foreach($gradeLevels as $gradeLevel)
                            <option value="{{ $gradeLevel->grade_level }}" {{ old('grade_level') == $gradeLevel->grade_level ? 'selected' : '' }}>
                                {{ $gradeLevel->grade_level }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-left">
                    <label for="school_year_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                        School Year
                    </label>
                    <select name="school_year_id" id="school_year_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select School Year</option>
                        @foreach($schoolYears ?? [] as $schoolYear)
                            <option value="{{ $schoolYear->id }}" {{ old('school_year_id') == $schoolYear->id ? 'selected' : '' }}>
                                {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                                @if($schoolYear->is_active) (Active) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition"
                            type="submit">
                        Add Section
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
