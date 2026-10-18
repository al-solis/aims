@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
    @endphp
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Asset Transmittal</h1>
                <p class="text-sm text-gray-500">
                    Create, edit and print asset transmittal forms.
                </p>
            </div>
            <div class="flex items-center gap-2 mt-0">
                <a href="{{ route('asset.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                <a href ="{{ route('transmittal.create') }}" @if (Auth::user()->role == 2) disabled @endif
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white {{ Auth::user()->role == 2 ? 'bg-gray-600 hover:bg-gray-600 cursor-not-allowed' : 'bg-gray-900 hover:bg-gray-800' }} rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Transmittal
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 pt-1">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 pt-1"
                data-success="true">
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Clear form fields after successful submission
                    clearModalFields();
                });
            </script>
        @endif

        {{-- Filters --}}
        <form action="" method="GET">
            <div class="flex flex-col md:flex-row gap-2 text-xs md:text-sm">
                <div class="md:w-2/3 w-full">
                    <input type="text" id="simple-search" name="search" placeholder="Search by name or description..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        value = "{{ request()->query('search') }}" oninput="this.form.submit()">
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="searchemployee" name="searchemployee" tooltip="Filter by employee who received the items"
                        data-tooltip-placement="top"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Employees</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ request('searchemployee') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="searchlocation" name="searchlocation" tooltip="Filter by location"
                        data-tooltip-placement="top"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Locations</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ request('searchlocation') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
            <button type="submit"
                class="hidden mt-4 w-full shrink-0 rounded-lg bg-gray-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 sm:mt-0 sm:w-auto">Search</button>
        </form>

        {{-- Table --}}
        <div class="bg-white border rounded-xl overflow-x-auto md:overflow-visible scroll-smooth">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">No</th>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Date</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">To</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[250px]">Remarks</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($transmittals as $transmittal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 w-[150px]">{{ $transmittal->transmittal_number }}</td>
                            <td class="px-4 py-3 w-[120px]">
                                {{ Carbon::parse($transmittal->transmittal_date)->format(format: 'Y-m-d') }}</td>
                            <td class="px-4 py-3 w-[150px]">
                                {{ $transmittal->issued_to ? $transmittal->issuedTo->last_name . ', ' . $transmittal->issuedTo->first_name . ' ' . $transmittal->issuedTo->middle_name : '' }}
                            </td>
                            <td class="px-4 py-3 w-[150px]">
                                {{ $transmittal->location ? $transmittal->location->name : '' }}</td>
                            <td class="px-4 py-3 w-[250px]">
                                {{ $transmittal->remarks }}
                            </td>
                            <td class="px-4 py-3 w-[150px] text-xs font-semibold">
                                @php
                                    $statuses = [
                                        0 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Voided'],
                                        1 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Active'],
                                    ];
                                    $status = $statuses[$transmittal->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('transmittal.show', $transmittal->id) }}" type="button"
                                            title="Edit transmittal : {{ $transmittal->transmittal_number }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path
                                                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd"
                                                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('asset.print-transmittal', $transmittal->id) }}" type="button"
                                            target="_blank"
                                            title="Print transmittal : {{ $transmittal->transmittal_number }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-yellow-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                                <path
                                                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                            </svg>
                                        </a>

                                        @if ($transmittal->status == 0 || Auth::user()->role == 2)
                                            <button type="button"
                                                title="{{ Auth::user()->role == 2 ? 'Unable to void' : 'Voided already' }} : {{ $transmittal->transmittal_number }}"
                                                disabled class="group flex space-x-1 text-gray-300 cursor-not-allowed">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path
                                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                </svg>
                                                {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                            </button>
                                        @else
                                            <button type="button"
                                                title="Void transmittal : {{ $transmittal->transmittal_number }}"
                                                data-id="{{ $transmittal->id }}"
                                                data-code="{{ $transmittal->transmittal_number }}"
                                                data-description="{{ $transmittal->purpose }}"
                                                onclick="voidTransmittal(this)"
                                                class="group flex space-x-1 text-gray-500 hover:text-red-600 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path
                                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                </svg>
                                                {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                            </button>
                                        @endif
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No transmittals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $transmittals->links() }}
        </div>
    </div>

    <script>
        function clearModalFields() {
            // Clear all form fields
            const form = document.querySelector('form');
            form.reset();

            // Remove any success messages after a delay
            setTimeout(() => {
                const successMessage = document.querySelector('[data-success]');
                if (successMessage) {
                    successMessage.remove();
                }
            }, 3000);
        }

        function formatDateForInput(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date)) return '';

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function voidTransmittal(button) {
            const transmittalId = button.getAttribute('data-id');
            const description = button.getAttribute('data-description');
            const code = button.getAttribute('data-code');

            if (confirm(
                    `Are you sure you want to void the transmittal: "${code} - ${description}"? This action cannot be undone.`
                )) {
                $.ajax({
                    url: `/transmittal/${transmittalId}/void`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            alert(xhr.responseJSON.message);
                        } else {
                            alert('An unexpected error occurred.');
                        }
                    }
                });
            }
        }
    </script>
@endsection
