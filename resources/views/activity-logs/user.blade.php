@extends('dashboard.admin')

@section('content')
 <div id="page-meta" data-title="View User Logs" data-parent="Activity Logs">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">

            <div class="bg-yellow-100 shadow-lg rounded-lg overflow-hidden p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-600 mb-4 md:mb-0">Activity Logs: {{ $user->name }}</h2>
                    <a href="{{ route('activity-logs.index') }}"onclick="event.preventDefault(); 
                                                const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                   .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Activity Logs'); 
                                               const title = schoolYearLink?.getAttribute('data-title') || 'Activity Logs'; 
                                                loadContent('{{ route('activity-logs.index') }}', title, 'activity-logs');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to All Logs
                    </a>
                </div>

                <div class="overflow-x-auto shadow rounded-lg">
                    <table class="min-w-full table-auto divide-y divide-gray-200 text-center">
                        <thead class="bg-yellow-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-700 uppercase tracking-wider">Description
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $log->action }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $log->description }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $log->created_at->format('Y-m-d h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No activity logs found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            
            <div class="px-6 mt-6 mb-2">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
 </div>
@endsection