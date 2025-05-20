@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Add User" data-parent="Users">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">

                {{-- Back Button --}}
                <div class="flex justify-start mt-6 mb-4">
                    <a href="{{ route('users.index') }}" onclick="event.preventDefault(); 
                                            const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                               .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Users'); 
                                           const title = schoolYearLink?.getAttribute('data-title') || 'Users'; 
                                            loadContent('{{ route('users.index') }}', title, 'users');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Users
                    </a>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Card --}}
                <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-6 text-left">
                            <label for="name" class="block text-gray-800 font-medium mb-2">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter full name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                pattern="[A-Za-z\s]+" title="Please enter a valid name" required>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="email" class="block text-gray-800 font-medium mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                placeholder="example@domain.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                required>
                        </div>

                        <div class="mb-6 text-left relative">
                            <label for="password" class="block text-gray-800 font-medium mb-2">Password</label>
                            <input type="password" name="password" id="password" placeholder="Create a secure password"
                                class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                minlength="8"
                                required>
                            <img src="{{ asset('icons/Eye.png') }}" alt="Show Password"
                                class="toggle-password cursor-pointer w-5 h-5 absolute right-4 top-11"
                                data-target="password">
                            <p class="text-sm text-gray-500 mt-1">*Password must be at least 8 characters.</p>
                        </div>

                        <div class="mb-6 text-left relative">
                            <label for="password_confirmation" class="block text-gray-800 font-medium mb-2">Confirm
                                Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                minlength="8"
                                required>
                            <img src="{{ asset('icons/Eye.png') }}" alt="Show Password"
                                class="toggle-password cursor-pointer w-5 h-5 absolute right-4 top-11"
                                data-target="password_confirmation">
                            <p class="text-sm text-gray-500 mt-1">*Re-enter your password to confirm.</p>
                        </div>

                        <div class="mb-6 text-left">
                            <label for="role" class="block text-gray-800 font-medium mb-2">Role</label>
                            <select name="role" id="role"
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                                required>
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            </select>
                        </div>

                        <div id="teacherFields" class="hidden">
                            <div class="mb-6 text-left">
                                <label for="specialization"
                                    class="block text-gray-800 font-medium mb-2">Specialization</label>
                                <input type="text" name="specialization" id="specialization"
                                    value="{{ old('specialization') }}" placeholder="e.g., Mathematics, Science"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>

                            <div class="mb-6 text-left">
                                <label for="bio" class="block text-gray-800 font-medium mb-2">Bio</label>
                                <textarea name="bio" id="bio" rows="3"
                                    placeholder="Write a brief description about yourself"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('bio') }}</textarea>
                            </div>

                            <div class="mb-6 text-left">
                                <label for="contact_number" class="block text-gray-800 font-medium mb-2">Contact
                                    Number</label>
                                <input type="text" name="contact_number" id="contact_number"
                                    value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX" pattern="[0-9]{11}"
                                    maxlength="11" minlength="11"
                                    title="Please enter a valid 11-digit phone number"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="sumit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Create New User
                            </button>
                        </div>

                        <style>
                            select.custom-select {
                                appearance: none;
                                -webkit-appearance: none;
                                -moz-appearance: none;
                                background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                                background-repeat: no-repeat;
                                background-position: right 1rem center;
                                background-size: 1.25rem;
                                padding-right: 3rem;
                            }

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
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection