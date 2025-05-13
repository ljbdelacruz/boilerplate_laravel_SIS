@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="View Student" data-parent="Students">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold">Student Details</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('students.index') }}" onclick="event.preventDefault(); 
                                                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                       .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                                                   const title = schoolYearLink?.getAttribute('data-title') || 'Students'; 
                                                    loadContent('{{ route('students.index') }}', title, 'students');"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                            ← Back to Student List
                        </a>
                    </div>
                </div>

                <div class="bg-yellow-100 shadow-2xl rounded px-8 pt-6 pb-8 mb-4">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">LRN</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">{{ $student->lrn }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Student ID</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">{{ $student->student_id }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Name</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Birth Date</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ $student->birth_date->format('d-m-Y') }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Gender</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ ucfirst($student->gender) }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Address</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">{{ $student->address }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Contact Number</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ $student->contact_number ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Email</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ $student->user->email ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Guardian Name</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ $student->guardian_name }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">Guardian Contact</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ $student->guardian_contact }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-600 font-medium mb-1 text-left">School Year</label>
                            <div class="bg-gray-100 rounded-md px-4 py-2 text-gray-800 text-left">
                                {{ $student->schoolYear->start_year }} - {{ $student->schoolYear->end_year }}
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <a href="{{ route('students.edit', $student->id) }}"
                            onclick="event.preventDefault(); loadContent('{{ route('students.edit', $student->id) }}', 'Edit Student', 'students');"
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-5 rounded-lg shadow transition">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
@endsection