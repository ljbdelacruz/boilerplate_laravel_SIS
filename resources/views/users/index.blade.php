@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">User Management</h1>
        <a href="{{ route('users.create') }}"
           onclick="event.preventDefault(); loadContent('{{ route('users.create') }}', 'Add User');"
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
           Add New User
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full text-center text-sm divide-y divide-gray-300">
            <thead class="bg-yellow-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-yellow-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                   ($user->role === 'teacher' ? 'bg-blue-100 text-blue-800' : 
                                   ($user->role === 'student' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('users.edit', $user) }}"
                               onclick="event.preventDefault(); loadContent('{{ route('users.edit', $user) }}', 'Edit User');"
                               class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                                Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
