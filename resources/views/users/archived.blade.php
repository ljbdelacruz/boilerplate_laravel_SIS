@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="View Archived Users" data-parent="Users">
        <div class="container mx-auto px-4">
            {{-- Existing header and content --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold self-center">Archived Users</h1>

                <div class="flex items-center">
                    <a href="{{ route('users.index') }}" onclick="event.preventDefault(); 
                                    const userLink = [...document.querySelectorAll('.nav-link')]
                                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Users'); 
                                    const title = userLink?.getAttribute('data-title') || 'Users'; 
                                    loadContent('{{ route('users.index') }}', title, 'users');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Users List
                    </a>
                </div>
            </div>

            {{-- Success Alert --}}
            @if(session('success'))
                <div id="successAlert" class="mb-4 rounded-lg bg-green-100 px-6 py-5 text-base text-green-700" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Alert --}}
            @if(session('error'))
                <div id="errorAlert" class="mb-4 rounded-lg bg-red-100 px-6 py-5 text-base text-red-700" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 gap-4">
                    {{-- Left: Search Bar --}}
                    <div class="flex items-center space-x-2">
                        <div class="relative w-72">
                            <img src="{{ asset('icons/searchlogo.png') }}" alt="Search Icon"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4">
                            <input type="text" id="searchUser" placeholder="Search user by name..."
                                class="bg-gray-100 text-sm text-gray-700 pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button onclick="filterUser()"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                            Search
                        </button>
                        <button onclick="clearArchivedSearch()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded-lg text-sm">
                            Clear
                        </button>
                    </div>
                </div>
                <table id="ArchivedTable" class="min-w-full text-center text-sm divide-y divide-gray-300">
                    <thead class="bg-yellow-100">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Date Archived
                            </th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ ucfirst($user->role) }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $user->deleted_at ? $user->deleted_at->format('Y-m-d h:i A') : 'N/A' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to restore this user?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                            Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No archived users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6">
                    {{ $users->links() }}
                </div>
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

                fadeWithDelay('successAlert');
                fadeWithDelay('errorAlert');
            });

             function filterUser() {
                const input = document.getElementById("searchUser").value.trim().toLowerCase();
                const rows = document.querySelectorAll("#ArchivedTable tbody tr:not(.no-match-row)");
                let found = false;

                rows.forEach(row => {
                    const name = row.children[0].textContent.toLowerCase();
                    const match = name.includes(input);
                    row.style.display = match ? "table-row" : "table-row";
                    row.style.visibility = match ? "visible" : "collapse";
                    if (match) found = true;
                });

                const tbody = document.querySelector("#ArchivedTable tbody");
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

            function clearArchivedSearch() {
                document.getElementById("searchUser").value = "";
                const rows = document.querySelectorAll("#ArchivedTable tbody tr:not(.no-match-row)");
                rows.forEach(row => {
                    row.style.display = "table-row";
                    row.style.visibility = "visible";
                });

                const existingNoMatch = document.querySelector("#ArchivedTable tbody .no-match-row");
                if (existingNoMatch) {
                    existingNoMatch.remove();
                }
            }
        </script>
    </div>
@endsection