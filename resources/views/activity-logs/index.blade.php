@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4 py-0">
    <div class="max-w-6xl mx-auto">

        <div class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">User Activity Logs</h2>

            
            </div>

            <div id="activityPagination">
                @forelse($users as $user)
                    <div class="mb-10 border border-yellow-200 rounded-lg p-6 bg-white user-entry" data-user-type="{{ strtolower($user->type) }}">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold user-name text-gray-800">
                                {{ $user->name }}
                                <span class="ml-2 text-sm text-gray-500 user-type">{{ $user->type }}</span>
                            </h3>
                            <a href="{{ route('activity-logs.user', $user->id) }}"
   onclick="event.preventDefault(); loadContent('{{ route('activity-logs.user', $user->id) }}', 'View Activity Log');"
   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
    View All Logs
</a>
                        </div>

                        <div class="overflow-x-auto shadow rounded-lg">
                            <table class="min-w-full table-auto divide-y divide-gray-200 text-center">
                                <thead class="bg-yellow-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-700 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($user->activityLogs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $log->action }}</td>
                                            <td class="px-6 py-4 text-gray-700">{{ $log->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No recent activity</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">No users found.</div>
                @endforelse

                <div class="flex justify-end mt-6">
                {{ $users->appends(['search' => request('search')])->links('vendor.tailwind-custom') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
      
        document.addEventListener("click", function (e) {
            const link = e.target.closest(".pagination-link");
            if (!link) return;

            e.preventDefault();
            const url = link.href;

            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");
                const newContent = doc.getElementById("activityPagination");
                if (newContent) {
                    document.getElementById("activityPagination").innerHTML = newContent.innerHTML;
                    userEntries = document.querySelectorAll('.user-entry'); 
                    window.history.pushState({}, "", url);
                }
            });
        });
</script>

@push('styles')
<style>
    /* For all screen sizes - basic table responsiveness */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        word-wrap: break-word;
    }

    /* Tablet: 768px - 1024px */
    @media (min-width: 768px) and (max-width: 1024px) {
        .user-entry {
            padding: 1rem;
        }

        .user-name {
            font-size: 1.125rem;
        }

        table th,
        table td {
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        .pagination-link {
            padding: 0.4rem 0.7rem;
            font-size: 0.85rem;
        }
    }

    /* Small screens: 268px - 767px */
    @media (max-width: 767px) {
        .container {
            padding: 0.5rem;
        }

        h2 {
            font-size: 1.25rem;
            text-align: center;
        }

        .user-entry {
            padding: 0.75rem;
        }

        .user-name {
            font-size: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .user-type {
            font-size: 0.75rem;
        }

        .user-entry a {
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
        }

        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        table th,
        table td {
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .pagination-link,
        nav span {
            font-size: 0.75rem;
            text-align: center;
            display: block;
            width: 100%;
            margin-bottom: 0.25rem;
        }
    }

    /* Ultra-small screens: up to 400px */
    @media (max-width: 400px) {
        .user-name {
            font-size: 0.9rem;
        }

        table th,
        table td {
            font-size: 0.7rem;
            padding: 0.4rem;
        }

        .user-entry a {
            font-size: 0.7rem;
        }

        h2 {
            font-size: 1rem;
        }
    }
</style>
@endpush
