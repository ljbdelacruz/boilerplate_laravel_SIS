@extends('dashboard.admin')

@section('content')
<div class="container mx-auto px-4 py-0">
    <div class="max-w-6xl mx-auto">
        <div class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden p-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">User Activity Logs</h2>

                <!-- Role Filter on the right -->
                <form method="GET" action="{{ route('activity-logs.index') }}" class="ml-auto">
                    <label for="role_filter" class="mr-2 text-sm font-medium text-gray-700">Filter by Role:</label>
                    <select name="role" id="role_filter" onchange="this.form.submit()" class="custom-select border rounded p-2 text-sm">
                        <option value="">All Roles</option>
                        @foreach($allRoles as $role)
                            <option value="{{ $role }}" {{ $selectedRole == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div id="activityPagination">
                @forelse($users as $user)
                    <div class="mb-10 border border-yellow-200 rounded-lg p-6 bg-white fade-in user-entry" data-user-type="{{ strtolower($user->type ?? '') }}">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold user-name text-gray-800">
                                {{ $user->name }}
                                @if(isset($user->type))
                                    <span class="ml-2 text-sm text-gray-500 user-type">{{ $user->type }}</span>
                                @endif
                            </h3>
                            <a href="{{ route('activity-logs.user', $user->id) }}" 
                              onclick="event.preventDefault(); loadContent('{{ route('activity-logs.user', $user->id) }}', 'View User Logs', 'activity-logs');"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                View All Logs
                            </a>
                        </div>

                        <div class="overflow-x-auto shadow rounded-lgv">
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
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $log->created_at->format('Y-m-d h:i A') }}</td>
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
                    {{ $users->links() }} 
                </div>
            </div>
        </div>
    </div>
    <style>
        .fade-in {
        opacity: 0;
        animation: fadeIn ease 0.8s forwards;
    }
    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
      select.custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.55rem center;
            background-size: 0.875rem;
            padding-right: 2.25rem;

            background-color: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            transition: box-shadow 0.2s ease-in-out;
        }
    </style>
</div>
@endsection
