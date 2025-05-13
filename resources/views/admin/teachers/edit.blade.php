@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Edit Teacher" data-parent="Teacher">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                {{-- Back Button --}}
                <div class="flex justify-start mb-4">
                    <a href="{{ route('teachers.index') }}" onclick="event.preventDefault(); 
                                            const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                               .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Teachers'); 
                                           const title = schoolYearLink?.getAttribute('data-title') || 'Teachers'; 
                                            loadContent('{{ route('teachers.index') }}', title, 'teachers');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Teacher List
                    </a>
                </div>

                {{-- Error Display --}}
                @if ($errors->any())
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Edit Form --}}
                <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                    <form action="{{ route('teachers.update', $teacher->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6 text-left">
                            <label for="name" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $teacher->name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div class="mb-6 text-left">
                            <label for="email" class="block text-gray-800 font-medium mb-2"
                                style="font-size: 22px;">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $teacher->email) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Update Teacher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
            <script>
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
        </div>
    </div>
@endsection