@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="View Archived Student" data-parent="Students">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl p-10 border border-gray-300">

                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                    <div class="text-left">
                        <h1 class="text-4xl font-extrabold text-blue-900">Archived Student</h1>
                        <p class="text-xl text-gray-700 mt-3">
                            <span class="font-normal">Name:</span> <span class="font-bold">{{ $student->first_name }}
                                {{ $student->middle_name }} {{ $student->last_name }}</span>
                        </p>
                    </div>

                    <a href="{{ route('admin.students.archivedIndex') }}" onclick="event.preventDefault(); 
                            const studentLink = [...document.querySelectorAll('.nav-link')]
                                .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                            const title = studentLink?.getAttribute('data-title') || 'Students'; 
                            loadContent('{{ route('admin.students.archivedIndex') }}','View Archived Students', 'students');"
                        class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition text-base">
                        ← Back to Archived List
                    </a>
                </div>

                <!-- Student Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg text-gray-800 leading-relaxed text-left">
                    <div><span class="block font-semibold text-gray-700">LRN:</span> {{ $student->lrn }}</div>
                    <div><span class="block font-semibold text-gray-700">Student ID:</span> {{ $student->student_id }}</div>
                    <div><span class="block font-semibold text-gray-700">Email:</span> {{ $student->user->email ?? 'N/A' }}</div>
                    <div><span class="block font-semibold text-gray-700">Gender:</span> {{ ucfirst($student->gender) }}</div>
                    <div><span class="block font-semibold text-gray-700">Birth Date:</span> {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('F d, Y') : 'N/A' }}</div>
                    <div><span class="block font-semibold text-gray-700">Contact Number:</span> {{ $student->contact_number ?? 'N/A' }}</div>
                    <div><span class="block font-semibold text-gray-700">Grade Level (at archiving):</span> {{ $student->grade_level }}</div>
                    <div><span class="block font-semibold text-gray-700">Section (at archiving):</span> {{ $student->section->name ?? 'N/A' }}</div>
                    <div><span class="block font-semibold text-gray-700">School Year (at archiving):</span> {{ $student->schoolYear->start_year ?? 'N/A' }} - {{ $student->schoolYear->end_year ?? 'N/A' }}</div>
                    <div><span class="block font-semibold text-gray-700">Guardian:</span> {{ $student->guardian_name ?? 'N/A' }}</div>
                    <div><span class="block font-semibold text-gray-700">Guardian Contact:</span> {{ $student->guardian_contact ?? 'N/A' }}</div>
                    <div class="md:col-span-2"><span class="block font-semibold text-gray-700">Address:</span> {{ $student->address ?? 'N/A' }}</div>
                    <div class="md:col-span-2"><span class="block font-semibold text-gray-700">Archived At:</span> {{ $student->deleted_at ? $student->deleted_at->format('F d, Y h:i A') : 'N/A' }}</div>
                </div>

                <!-- Restore Button - Right Aligned -->
                <div class="mt-10 flex justify-end">
                    <form action="{{ route('admin.students.unarchive', $student->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to restore this student?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl shadow-lg transition text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v16h16V4H4zm4 8l4 4 4-4m0-4l-4 4-4-4"/>
                            </svg>
                            Restore Student
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
