@extends('dashboard.admin')

@section('content')
<div id="page-meta" data-title="Edit User" data-parent="Users">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="flex justify-start mt-6 mb-4">
                <a href="{{ route('users.index') }}"
                    onclick="event.preventDefault(); 
                                                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                       .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Users'); 
                                                   const title = schoolYearLink?.getAttribute('data-title') || 'Users'; 
                                                    loadContent('{{ route('users.index') }}', 'Users Module', 'users');"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    ← Back to List
                </a>
            </div>

            @if ($errors->any())
                <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                <form id="update-user-form" action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-6 text-left">
                        <label for="name" class="block text-gray-800 font-medium mb-2">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="mb-6 text-left">
                        <label for="email" class="block text-gray-800 font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-6 text-left relative">
                        <label for="password" class="block text-gray-800 font-medium mb-2">New Password</label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
                        <img src="{{ asset('icons/Hidden.png') }}" alt="Toggle visibility"
                            class="toggle-password cursor-pointer w-5 h-5 absolute right-4 top-11"
                            data-target="password">
                        <p class="text-sm text-gray-500 mt-1">*Leave blank to keep current password.</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6 text-left relative">
                        <label for="password_confirmation" class="block text-gray-800 font-medium mb-2">Confirm
                            Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
                        <img src="{{ asset('icons/Hidden.png') }}" alt="Toggle visibility"
                            class="toggle-password cursor-pointer w-5 h-5 absolute right-4 top-11"
                            data-target="password_confirmation">
                        <p class="text-sm text-gray-500 mt-1">*Re-enter new password if you're changing it.</p>
                    </div>

                    <!-- Role -->
                    <div class="mb-6 text-left">
                        <label for="role" class="block text-gray-800 font-medium mb-2">Role</label>
                        <select name="role" id="role"
                            class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                            required>
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                            </option>
                            <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher
                            </option>
                        </select>
                    </div>

                    <!-- Teacher Fields -->
                    <div id="teacher_fields"
                        class="mb-6 text-left {{ old('role', $user->role) === 'teacher' ? '' : 'hidden' }}">
                        <label class="block text-gray-800 font-medium mb-2" for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization"
                            value="{{ old('specialization', $user->teacher->specialization ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">

                        <label class="block text-gray-800 font-medium mb-2 mt-4" for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">{{ old('bio', $user->teacher->bio ?? '') }}</textarea>

                        <label class="block text-gray-800 font-medium mb-2 mt-4" for="contact_number">Contact
                            Number</label>
                        <input type="text" id="contact_number" name="contact_number"
                            value="{{ old('contact_number', $user->teacher->contact_number ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Update User
                        </button>
                    </div>
                    </form>
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
        </div>
    </div>
</div>
@endsection