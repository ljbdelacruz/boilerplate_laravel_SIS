@extends('dashboard.admin')

@section('title', 'Add Schedule')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button --}}
        <div class="flex justify-start mt-6 mb-4">
            <a href="{{ route('schedules.index') }}" 
               onclick="event.preventDefault(); 
                        const scheduleLink = [...document.querySelectorAll('.nav-link')]
                            .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Schedules'); 
                        loadContent('{{ route('schedules.index') }}', scheduleLink || 'Schedules');"
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
            <form action="{{ route('schedules.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="teacher_id" class="block text-left text-gray-800 font-medium mb-2 !text-[22px]">Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="course_id" class="block text-left text-gray-800 font-medium mb-2 !text-[22px]">Course</label>
                    <select name="course_id" id="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="section_id" class="block text-left text-gray-800 font-medium mb-2 !text-[22px]">Section</label>
                    <select name="section_id" id="section_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }} (Grade {{ $section->grade_level }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="school_year_id" class="block text-left text-gray-800 font-medium mb-2 !text-[22px]">School Year</label>
                    <select name="school_year_id" id="school_year_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
                        <option value="">Select School Year</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear->id }}">{{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}</option>
                        @endforeach
                    </select>
                </div>
                    <div class="mb-4">
                        <label for="school_year_id" class="block text-gray-700 text-sm font-bold mb-2">School Year</label>
                        <select name="school_year_id" id="school_year_id"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @foreach($schoolYears ?? [] as $schoolYear)
                                <option value="{{ $schoolYear->id }}" {{ old('school_year_id') == $schoolYear->id ? 'selected' : '' }}>
                                    {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                                    @if($schoolYear->is_active) (Active) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                <div>
                    <label class="block text-left text-gray-800 font-medium mb-2 !text-[22px]">Days</label>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day)
                            <div class="flex items-center">
                                <input type="checkbox" name="days[]" id="{{ strtolower($day) }}" value="{{ $day }}" class="h-4 w-4 text-blue-600">
                                <label for="{{ strtolower($day) }}" class="ml-2 text-gray-700">{{ $day }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="start_time" class="block text-left text-gray-800 font-medium mb-2 !text-[22px]">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
                </div>

                <div>
                    <label for="end_time" class="block text-left text-gray-800 font-medium mb-2 !text-[22px]">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                        Create Schedule
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
