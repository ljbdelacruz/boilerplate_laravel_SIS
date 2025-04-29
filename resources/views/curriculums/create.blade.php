@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-semibold mb-4">Add Curriculum</h2>
    <form action="{{ route('curriculums.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Section</label>
            <select name="section_id" class="w-full border rounded px-3 py-2">
                <option value="">Select Section</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                        {{ $section->name }}
                    </option>
                @endforeach
            </select>
            @error('section_id')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Subject</label>
            <select name="subject_id" class="w-full border rounded px-3 py-2">
                <option value="">Select Subject</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('subject_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
            @error('subject_id')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Time</label>
            <input type="text" name="time" value="{{ old('time') }}" class="w-full border rounded px-3 py-2" placeholder="e.g. 08:00 AM - 09:00 AM">
            @error('time')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
        <a href="{{ route('curriculums.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </form>
</div>
@endsection
