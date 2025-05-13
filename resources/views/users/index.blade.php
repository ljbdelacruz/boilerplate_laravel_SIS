@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">User Management</h1>
        </div>

        @if(session('success'))
            <div id="successAlert"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 gap-4">

                {{-- Left: Search Bar --}}
                <div class="flex items-center space-x-2">
                    <div class="relative w-72">
                        <img src="icons/searchlogo.png" alt="Search Icon"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4">
                        <input type="text" id="searchUser" placeholder="Search user by name..."
                            class="bg-gray-100 text-sm text-gray-700 pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button onclick="filterUser()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                        Search
                    </button>
                    <button onclick="clearUserrSearch()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded-lg text-sm">
                        Clear
                    </button>
                </div>

                {{-- Right: Buttons --}}
                <div class="flex flex-wrap gap-2 justify-end">
                    <a href="{{ route('users.create') }}"
                        onclick="event.preventDefault(); loadContent('{{ route('users.create') }}', 'Add User', 'users');"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Add New User
                    </a>
                </div>
            </div>
            <table id="UserTable" class="min-w-full text-center text-sm divide-y divide-gray-300">
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
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                        ($user->role === 'teacher' ? 'bg-blue-100 text-blue-800' :
                            ($user->role === 'student' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                            onclick="event.preventDefault(); loadContent('{{ route('users.edit', $user) }}', 'Edit User','users');"
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
                function filterUser() {
                    const input = document.getElementById("searchUser").value.trim().toLowerCase();
                    const rows = document.querySelectorAll("#UserTable tbody tr:not(.no-match-row)");
                    let found = false;

                    rows.forEach(row => {
                        const name = row.children[0].textContent.toLowerCase();
                        const match = name.includes(input);
                        row.style.display = match ? "table-row" : "table-row";
                        row.style.visibility = match ? "visible" : "collapse";
                        if (match) found = true;
                    });

                    const tbody = document.querySelector("#UserTable tbody");
                    let noMatchRow = tbody.querySelector(".no-match-row");

                    if (!found) {
                        if (!noMatchRow) {
                            noMatchRow = document.createElement("tr");
                            noMatchRow.classList.add("no-match-row");
                            noMatchRow.innerHTML = `
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 bg-white text-sm">
                            No user name matches found
                        </td>
                    `;
                            tbody.appendChild(noMatchRow);
                        }
                    } else if (noMatchRow) {
                        noMatchRow.remove();
                    }
                }

                function clearUserrSearch() {
                    document.getElementById("searchUser").value = "";
                    const rows = document.querySelectorAll("#UserTable tbody tr:not(.no-match-row)");
                    rows.forEach(row => {
                        row.style.display = "table-row";
                        row.style.visibility = "visible";
                    });

                    const existingNoMatch = document.querySelector("#UserTable tbody .no-match-row");
                    if (existingNoMatch) {
                        existingNoMatch.remove();
                    }
                }

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

                    fadeWithDelay('successAlert');
                    fadeWithDelay('errorAlert');
                });
            </script>
        </div>
    </div>
@endsection