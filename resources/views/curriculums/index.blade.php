@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Curriculum Management</h2>
        <a href="{{ route('curriculums.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Curriculum</a>
    </div>
    @if(!$activeSchoolYear)
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">No active school year found.</div>
    @else
    <div class="mb-4">
        <span class="font-semibold">Active School Year:</span> {{ $activeSchoolYear->getSchoolYearDisplayAttribute() }}
    </div>
    @foreach($sections as $section)
        <div>
            <h3>{{ $section->name }}</h3>
            <ul>
                @foreach($section->curriculums as $curriculum)
                    <li>
                        {{ $curriculum->subject->name ?? 'No Subject' }} ({{ $curriculum->time }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
    @endif
</div>
@endsection
