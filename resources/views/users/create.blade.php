@extends('dashboard.admin')

@section('title', 'Add User')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button --}}
        <div class="flex justify-start mt-6 mb-4">
            <a href="{{ route('users.index') }}"
                    onclick="event.preventDefault(); 
                         const subjectLink = [...document.querySelectorAll('.nav-link')]
                            .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Users'); 
                         loadContent('{{ route('users.index') }}', subjectLink || 'Users');"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                ← Back to Users
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

        {{-- Form Card --}}
        <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="mb-6 text-left">
            <label for="name" class="block text-gray-800 font-medium mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
        </div>

        <div class="mb-6 text-left">
            <label for="email" class="block text-gray-800 font-medium mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
        </div>

        <div class="mb-6 text-left">
            <label for="password" class="block text-gray-800 font-medium mb-2">Password</label>
            <input type="password" name="password" id="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
            <p class="text-sm text-gray-500 mt-1">*Password must be at least 8 characters.</p>
        </div>

        <div class="mb-6 text-left">
            <label for="password_confirmation" class="block text-gray-800 font-medium mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                required>
            <p class="text-sm text-gray-500 mt-1">*Re-enter your password to confirm.</p>
        </div>

        <div class="mb-6 text-left">
            <label for="role" class="block text-gray-800 font-medium mb-2">Role</label>
            <select name="role" id="role"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"required>
                <option value="">Select Role</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
            </select>
        </div>

        <div id="teacherFields" class="hidden">
            <div class="mb-6 text-left">
                <label for="specialization" class="block text-gray-800 font-medium mb-2">Specialization</label>
                <input type="text" name="specialization" id="specialization" value="{{ old('specialization') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="mb-6 text-left">
                <label for="bio" class="block text-gray-800 font-medium mb-2">Bio</label>
                <textarea name="bio" id="bio" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">{{ old('bio') }}</textarea>
            </div>

            <div class="mb-6 text-left">
                <label for="contact_number" class="block text-gray-800 font-medium mb-2">Contact Number</label>
                <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}"
                    pattern="[0-9]{11}" title="Please enter a valid 11-digit phone number"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                Create New User
            </button>
        </div>
    </form>
</div>

    </div>
</div>

{{-- Role Toggle Script --}}
<script>
    document.getElementById('role').addEventListener('change', function () {
        const teacherFields = document.getElementById('teacherFields');
        const showTeacher = this.value === 'teacher';

        teacherFields.classList.toggle('hidden', !showTeacher);
        [...teacherFields.querySelectorAll('input, textarea')].forEach(el => {
            el.disabled = !showTeacher;
        });
    });

    // Trigger on load if already selected
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('role').dispatchEvent(new Event('change'));
    });
</script>
@endsection

