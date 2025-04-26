@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Schedule Management</h1>
            <a href="{{ route('schedules.create') }}"
                onclick="event.preventDefault(); loadContent('{{ route('schedules.create') }}', 'Add Schedule');"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Add Schedule
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full text-center text-sm divide-y divide-gray-300">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Day</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($schedules as $schedule)
                        <tr class="hover:bg-yellow-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $schedule->teacher->name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $schedule->course->name }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $schedule->section->name }} (Grade {{ $schedule->section->grade_level }})
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                @switch($schedule->day_of_week)
                                    @case('Monday')
                                        Monday
                                    @break

                                    @case('Tuesday')
                                        Tuesday
                                    @break

                                    @case('Wednesday')
                                        Wednesday
                                    @break

                                    @case('Thursday')
                                        Thursday
                                    @break

                                    @case('Friday')
                                        Friday
                                    @break

                                    @default
                                        {{ $schedule->day_of_week }}
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('schedules.edit', $schedule) }}"
                                    onclick="event.preventDefault(); loadContent('{{ route('schedules.edit', $schedule) }}', 'Edit Schedule');"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                                    Edit
                                </a>
                                <!-- Delete Button -->
                                <button type="button"
                                    onclick="showCenteredDeletePopup('{{ route('schedules.destroy', $schedule) }}')"
                                    class="inline-block bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition duration-200">
                                    Delete
                                </button>

                                <!-- Hidden Form Template -->
                                <form id="deleteFormTemplate" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

<script>
    function showCenteredDeletePopup(actionUrl) {
        // Remove existing popups
        document.querySelectorAll('.delete-popup').forEach(p => p.remove());

        // Create the popup
        const popup = document.createElement('div');
        popup.className =
            'delete-popup fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white shadow-lg p-6 rounded-lg border border-gray-300 z-50 text-center w-80';
        popup.innerHTML = `
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Delete Schedule</h2>
            <p class="text-sm text-gray-700 mb-4">Are you sure you want to delete this schedule?</p>
            <div class="flex justify-center gap-4">
                <button class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm rounded" onclick="this.closest('.delete-popup').remove()">Cancel</button>
                <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded" onclick="submitDelete('${actionUrl}')">Delete</button>
            </div>
        `;
        document.body.appendChild(popup);
    }

    function submitDelete(actionUrl) {
        const form = document.getElementById('deleteFormTemplate').cloneNode(true);
        form.action = actionUrl;

        // Close the confirmation popup
        document.querySelectorAll('.delete-popup').forEach(p => p.remove());

        // Show loading
        const loadingPopup = document.createElement('div');
        loadingPopup.className = 'fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50';
        loadingPopup.innerHTML =
            `<div class='bg-white p-6 rounded shadow text-center'>
            <div class='custom-spinner h-10 w-10 mx-auto mb-2'></div>
            <p class='text-gray-700 font-medium'>Deleting Schedule...</p>
        </div>`;
        document.body.appendChild(loadingPopup);

        // Send request using fetch
        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        }).then(response => {
            if (response.ok) {
                setTimeout(() => {
                    loadingPopup.remove();

                    const successPopup = document.createElement('div');
                    successPopup.className =
                        'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-500 text-white px-6 py-4 rounded shadow-lg text-lg font-semibold z-50';
                    successPopup.textContent = '✅ Schedule deleted successfully!';
                    document.body.appendChild(successPopup);

                    setTimeout(() => {
                        successPopup.remove();

                        const scheduleLink = [...document.querySelectorAll('.nav-link')]
                            .find(link => link.textContent.replace(/\s+/g, ' ').trim() ===
                                'Schedules');

                        loadContent('{{ route('schedules.index') }}', scheduleLink ||
                            'Schedules');
                    }, 700);
                }, 500);
            } else {
                loadingPopup.remove();
                alert('Something went wrong while deleting.');
            }
        }).catch(error => {
            loadingPopup.remove();
            alert('An error occurred while deleting.');
        });
    }
</script>

<style>
    .custom-spinner {
        border: 4px solid #ef4444;
        /* red-500 */
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin-slow 1.5s linear infinite;
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
