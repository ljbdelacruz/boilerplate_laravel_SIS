@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-semibold mb-4">Auto Generate Schedule</h2>
    <form action="{{ route('schedules.auto-generate') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block mb-1 font-semibold">School Year</label>
            <select name="school_year_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select School Year</option>
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}">{{ $sy->getSchoolYearDisplayAttribute() }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Section</label>
            <select name="section_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Section</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Teacher</label>
            <select name="teacher_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Teacher</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Generate Schedule</button>
    </form>
</div>
@endsection
