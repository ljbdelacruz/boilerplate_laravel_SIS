@extends('dashboard.admin')

@section('title', 'Edit Section')

@section('content')
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">

            {{-- Back Button --}}
            <div class="flex justify-start mb-4">
                <a href="{{ route('sections.index') }}"
                    onclick="event.preventDefault(); 
                            const sectionLink = [...document.querySelectorAll('.nav-link')]
                                .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Sections'); 
                            loadContent('{{ route('sections.index') }}', sectionLink || 'Sections');"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    ← Back to Section List
                </a>
            </div>

            {{-- Error Display --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Edit Section Form --}}
            <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                <form action="{{ route('sections.update', $section->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6 text-left">
                        <label for="name" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Section
                            Name</label>
                        <input type="text" name="name" id="name" required
                            value="{{ old('name', $section->name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div class="mb-6 text-left">
                        <label for="grade_level" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Grade
                            Level</label>
                        <input type="number" name="grade_level" id="grade_level" required
                            value="{{ old('grade_level', $section->grade_level) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div class="mb-6 text-left">
                        <label for="school_year_id" class="block text-gray-800 font-medium mb-2"
                            style="font-size: 22px;">School Year</label>
                        <select name="school_year_id" id="school_year_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            @foreach ($schoolYears as $schoolYear)
                                <option value="{{ $schoolYear->id }}"
                                    {{ old('school_year_id', $section->school_year_id) == $schoolYear->id ? 'selected' : '' }}>
                                    {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                                    @if ($schoolYear->is_active)
                                        (Active)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end pt-4">
                        <a href="#"
                            onclick="event.preventDefault();
                const form = this.closest('form');
                const formData = new FormData(form);
                const sectionLink = [...document.querySelectorAll('.nav-link')]
                    .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Sections');

                const loadingPopup = document.createElement('div');
                loadingPopup.className = 'fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50';
                loadingPopup.innerHTML = 
                    `<div class='bg-white p-6 rounded shadow text-center'>
                        <div class='custom-spinner h-10 w-10 mx-auto mb-2'></div>
                        <p class='text-gray-700 font-medium'>Updating Section...</p>
                    </div>`;
                document.body.appendChild(loadingPopup);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                }).then(response => {
                    if (response.ok) {
                        setTimeout(() => {
                            loadingPopup.remove();
                            const successPopup = document.createElement('div');
                            successPopup.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-500 text-white px-6 py-4 rounded shadow-lg text-lg font-semibold z-50';
                            successPopup.textContent = '✅ Section updated successfully!';
                            document.body.appendChild(successPopup);

                            setTimeout(() => {
                                successPopup.remove();
                                loadContent('{{ route('sections.index') }}', sectionLink || 'Sections');
                            }, 700);
                        }, 500);
                    } else {
                        loadingPopup.remove();
                        alert('Something went wrong. Please check your input.');
                    }
                }).catch(error => {
                    loadingPopup.remove();
                    alert('An error occurred while updating.');
                });"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Update Section
                        </a>
                    </div>
                    <style>
                        .custom-spinner {
                            border: 4px solid #3b82f6;
                            border-top-color: transparent;
                            border-radius: 50%;
                            animation: spin-slow 2s linear infinite;
                        }

                        @keyframes spin-slow {
                            0% {
                                transform: rotate(0deg);
                            }

                            100% {
                                transform: rotate(360deg);
                            }
                        }
                    </style>

                </form>
            </div>
        </div>
    </div>
@endsection
