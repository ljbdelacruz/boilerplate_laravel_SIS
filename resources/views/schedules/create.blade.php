@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Add New Schedule</h2>
            <a href="{{ route('schedules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form action="{{ route('schedules.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="teacher_id" class="block text-gray-700 text-sm font-bold mb-2">Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="course_id" class="block text-gray-700 text-sm font-bold mb-2">Course</label>
                    <select name="course_id" id="course_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="section_id" class="block text-gray-700 text-sm font-bold mb-2">Section</label>
                    <select name="section_id" id="section_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }} (Grade {{ $section->grade_level }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="school_year_id" class="block text-gray-700 text-sm font-bold mb-2">School Year</label>
                    <select name="school_year_id" id="school_year_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select School Year</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear->id }}">{{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Days</label>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="days[]" id="monday" value="Monday" class="h-4 w-4 text-blue-600">
                            <label for="monday" class="ml-2 text-gray-700">Monday</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="days[]" id="tuesday" value="Tuesday" class="h-4 w-4 text-blue-600">
                            <label for="tuesday" class="ml-2 text-gray-700">Tuesday</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="days[]" id="wednesday" value="Wednesday" class="h-4 w-4 text-blue-600">
                            <label for="wednesday" class="ml-2 text-gray-700">Wednesday</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="days[]" id="thursday" value="Thursday" class="h-4 w-4 text-blue-600">
                            <label for="thursday" class="ml-2 text-gray-700">Thursday</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="days[]" id="friday" value="Friday" class="h-4 w-4 text-blue-600">
                            <label for="friday" class="ml-2 text-gray-700">Friday</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                </div>

                <div class="mb-4">
                    <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection