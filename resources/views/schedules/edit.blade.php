@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Edit Schedule</h2>
            <a href="{{ route('schedules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('schedules.update', $schedule) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="teacher_id" class="block text-gray-700 text-sm font-bold mb-2">Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $schedule->teacher_id == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="course_id" class="block text-gray-700 text-sm font-bold mb-2">Course</label>
                    <select name="course_id" id="course_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $schedule->course_id == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="section_id" class="block text-gray-700 text-sm font-bold mb-2">Section</label>
                    <select name="section_id" id="section_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ $schedule->section_id == $section->id ? 'selected' : '' }}>
                                {{ $section->name }} (Grade {{ $section->grade_level }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="school_year_id" class="block text-gray-700 text-sm font-bold mb-2">School Year</label>
                    <select name="school_year_id" id="school_year_id" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select School Year</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear->id }}" {{ $schedule->school_year_id == $schoolYear->id ? 'selected' : '' }}>
                                {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="day_of_week" class="block text-gray-700 text-sm font-bold mb-2">Day</label>
                    <select name="day_of_week" id="day_of_week" class="form-select rounded-md shadow-sm mt-1 block w-full" required>
                        <option value="">Select Day</option>
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                            <option value="{{ $day }}" {{ $schedule->day_of_week == $day ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Start Time</label>
                    <input type="time" name="start_time" id="start_time" 
                        value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                        class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                </div>

                <div class="mb-4">
                    <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">End Time</label>
                    <input type="time" name="end_time" id="end_time" 
                        value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                        class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection