@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Batch Upload" data-parent="Teacher">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                {{-- Back Button --}}
                <div class="flex justify-start mt-6 mb-4">
                    <a href="{{ route('teachers.index') }}"
                        onclick="event.preventDefault(); 
                                                                            const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                                               .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Teachers'); 
                                                                           const title = schoolYearLink?.getAttribute('data-title') || 'Teachers'; 
                                                                            loadContent('{{ route('teachers.index') }}', title, 'teachers');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Teacher List
                    </a>
                </div>

                {{-- Session Error --}}
                @if(session('error'))
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Upload Form Card --}}
                <div class="bg-yellow-100 shadow-lg rounded-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Batch Upload Teachers</h2>

                    <form id="uploadForm" action="{{ route('teachers.upload.process') }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="text-left">
                            <label for="file" class="block text-black-800 font-medium mb-2" style="font-size: 22px;">Excel
                                File
                                (.xlsx, .xls)</label>
                            <input type="file" name="file" id="file" accept=".xlsx,.xls" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div class="text-left">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-gray-800 font-medium" style="font-size: 22px;">Template:</span>
                                <a href="{{ asset('templates/teacher_template.xlsx') }}"
                                    class="text-blue-600 hover:underline break-all">
                                    Download Template
                                </a>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="showConfirmation()"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Upload Teachers
                            </button>
                        </div>

                        {{-- Confirmation Modal --}}
                        <div id="confirmationModal"
                            class="fixed inset-0 bg-transparent hidden z-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
                                <h3 class="text-lg font-semibold text-gray-800 text-center">Confirm Upload</h3>
                                <p class="text-sm text-gray-600 text-center mt-2">
                                    Are you sure you want to upload this file? This will create new teacher accounts.
                                </p>
                                <div class="mt-4 flex justify-center gap-4">
                                    <button id="confirmUpload"
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow">
                                        Yes, Upload
                                    </button>
                                    <button onclick="hideConfirmation()"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <script>
                            function showConfirmation() {
                                const fileInput = document.getElementById('file');
                                if (!fileInput.files || fileInput.files.length === 0) {
                                    showCenteredAlert('⚠️ No uploaded file. Please choose a file.');
                                    return;
                                }

                                document.getElementById('confirmationModal').classList.remove('hidden');
                            }

                            function hideConfirmation() {
                                document.getElementById('confirmationModal').classList.add('hidden');
                            }

                            function showCenteredAlert(message) {
                                const alertBox = document.createElement('div');
                                alertBox.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-600 text-white text-lg font-semibold px-6 py-4 rounded-lg shadow-lg z-50 transition-opacity opacity-0';
                                alertBox.textContent = message;

                                document.body.appendChild(alertBox);

                                setTimeout(() => alertBox.classList.remove('opacity-0'), 10);
                                setTimeout(() => {
                                    alertBox.classList.add('opacity-0');
                                    setTimeout(() => alertBox.remove(), 300);
                                }, 2500);
                            }

                            // ✅ Directly submit form after confirmation
                            document.getElementById('confirmUpload').addEventListener('click', function () {
                                hideConfirmation();
                                document.getElementById('uploadForm').submit();
                            });


                            document.addEventListener('DOMContentLoaded', () => {
                                const fadeWithDelay = (id) => {
                                    const el = document.getElementById(id);
                                    if (el) {
                                        // Add fade-in immediately
                                        el.classList.add('fade-in');

                                        // Then remove fade-in and add fade-out after 5s
                                        setTimeout(() => {
                                            el.classList.remove('fade-in');
                                            el.classList.add('fade-out');
                                        }, 5000);
                                    }
                                };

                                fadeWithDelay('errorAlert');
                            });
                        </script>

                        <style>
                            .fade-in {
                                opacity: 1;
                                transform: translateY(0);
                                max-height: 500px;
                                /* enough space for content */
                                margin-bottom: 1rem;
                                padding-top: 1rem;
                                padding-bottom: 1rem;
                                transition: all 0.5s ease-in-out;
                                overflow: hidden;
                            }

                            .fade-out {
                                opacity: 0;
                                transform: translateY(20px);
                                max-height: 0;
                                margin-bottom: 0;
                                padding-top: 0;
                                padding-bottom: 0;
                                transition: all 0.5s ease-in-out;
                                overflow: hidden;
                            }
                        </style>

                </div>
@endsection