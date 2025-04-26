@extends('dashboard.admin')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">

        <div class="flex justify-start mb-4">
            <a href="{{ route('users.index') }}"
                onclick="event.preventDefault(); 
                    const userLink = [...document.querySelectorAll('.nav-link')]
                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Users'); 
                    loadContent('{{ route('users.index') }}', userLink || 'Users');"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                ← Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
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

                <div class="mb-6 text-left">
                    <label class="block text-gray-800 font-medium mb-2" for="name">Name</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
                </div>

                <div class="mb-6 text-left">
                    <label class="block text-gray-800 font-medium mb-2" for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
                </div>

                <div class="mb-6 text-left">
                    <label class="block text-gray-800 font-medium mb-2" for="password">New Password</label>
                    <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
                    <p class="text-sm text-gray-500 mt-1">*Leave blank to keep current password.</p>
                </div>

                <div class="mb-6 text-left">
                    <label class="block text-gray-800 font-medium mb-2" for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
                    <p class="text-sm text-gray-500 mt-1">*Re-enter new password if you're changing it.</p>
                </div>

                <div class="mb-6 text-left">
                    <label class="block text-gray-800 font-medium mb-2" for="role">Role</label>
                    <select name="role" id="role"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400" required>
                        <option value="">Select Role</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>

                <div id="teacher_fields" class="mb-6 hidden">
                    <label class="block text-gray-800 font-medium mb-2" for="subject">Subject</label>
                    <input type="text" name="subject" id="subject"
                        value="{{ old('subject', $user->subject) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
                </div>

                <div id="student_fields" class="mb-6 hidden">
                    <label class="block text-gray-800 font-medium mb-2" for="grade_level">Grade Level</label>
                    <input type="text" name="grade_level" id="grade_level"
                        value="{{ old('grade_level', $user->grade_level) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
                </div>

                <div class="flex justify-end pt-4">
                    <a href="#"
                        onclick="event.preventDefault();
                            const form = document.getElementById('update-user-form');
                            const formData = new FormData(form);
                            const userLink = [...document.querySelectorAll('.nav-link')].find(link => link.textContent.trim() === 'Users');

                            const loadingPopup = document.createElement('div');
                            loadingPopup.className = 'fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50';
                            loadingPopup.innerHTML =
                                `<div class='bg-white p-6 rounded shadow text-center'>
                                    <div class='custom-spinner h-10 w-10 mx-auto mb-2'></div>
                                    <p class='text-gray-700 font-medium'>Updating User...</p>
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
                                        successPopup.textContent = '✅ User updated successfully!';
                                        document.body.appendChild(successPopup);

                                        setTimeout(() => {
                                            successPopup.remove();
                                            loadContent('{{ route('users.index') }}', userLink || 'Users');
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
                        Update User
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const role = document.getElementById('role');
        const teacherFields = document.getElementById('teacher_fields');
        const studentFields = document.getElementById('student_fields');

        function toggleFields() {
            teacherFields.classList.add('hidden');
            studentFields.classList.add('hidden');

            if (role.value === 'teacher') {
                teacherFields.classList.remove('hidden');
            } else if (role.value === 'student') {
                studentFields.classList.remove('hidden');
            }
        }

        role.addEventListener('change', toggleFields);
        toggleFields(); // Run on load
    });
</script>
@endsection
