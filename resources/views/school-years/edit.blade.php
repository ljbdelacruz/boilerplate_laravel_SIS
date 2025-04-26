@extends('dashboard.admin')

@section('title', 'Edit School Year')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button Left-Aligned --}}
        <div class="flex justify-start mb-4">
            <a href="{{ route('school-years.index') }}"
                onclick="event.preventDefault(); 
                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'School Years'); 
                    loadContent('{{ route('school-years.index') }}', schoolYearLink || 'School Years');"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow transition">
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

        {{-- Form --}}
        <div class="bg-yellow-100 shadow-lg rounded-lg p-8">

            <form action="{{ route('school-years.update', $schoolYear->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4 text-left">
                    <label for="start_year" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Start Year</label>
                    <input type="number" 
                        name="start_year" 
                        id="start_year" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        min="2000" max="2099" 
                        value="{{ old('start_year', $schoolYear->start_year) }}" 
                        required>
                </div>

                <div class="mb-4 text-left">
                    <label for="end_year" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">End Year</label>
                    <input type="number" 
                        name="end_year" 
                        id="end_year" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        min="2000" max="2099" 
                        value="{{ old('end_year', $schoolYear->end_year) }}" 
                        required>
                </div>

                {{-- Update Button --}}
                <div class="flex justify-end pt-4">
                    <a href="#"
                        onclick="event.preventDefault();
                        const form = this.closest('form');
                        const formData = new FormData(form);
                        const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                            .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'School Years');

                        const loadingPopup = document.createElement('div');
                        loadingPopup.className = 'fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50';
                        loadingPopup.innerHTML = `
                            <div class='bg-white p-6 rounded shadow text-center'>
                                <div class='custom-spinner h-10 w-10 mx-auto mb-2'></div>
                                <p class='text-gray-700 font-medium'>Updating School Year...</p>
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
                                    successPopup.textContent = '✅ School Year updated successfully!';
                                    document.body.appendChild(successPopup);

                                    setTimeout(() => {
                                        successPopup.remove();
                                        loadContent('{{ route('school-years.index') }}', schoolYearLink || 'School Years');
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
                        Update
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
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            </form>
        </div>
    </div>
</div>
@endsection
