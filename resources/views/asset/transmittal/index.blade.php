@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
    @endphp
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>Asset Transmittal</h1>
                        <p>Create, edit and print asset transmittal forms.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('asset.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        <a href="{{ route('transmittal.create') }}" @if (Auth::user()->role == 2) disabled @endif
                            class="ft-btn ft-btn-primary {{ Auth::user()->role == 2 ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Transmittal
                        </a>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Alerts --}}
                @if ($errors->any())
                    <div class="ft-alert ft-alert-error">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @elseif (session('success'))
                    <div class="ft-alert ft-alert-success flex items-center justify-between" data-success="true">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            clearModalFields();
                        });
                    </script>
                @endif

                {{-- Filters --}}
                <form action="" method="GET" class="flex flex-col md:flex-row gap-3">
                    <div class="md:w-2/3 w-full">
                        <input type="text" name="search" placeholder="Search by name or description..." class="ft-field"
                            value="{{ request()->query('search') }}" oninput="this.form.submit()">
                    </div>
                    <div class="md:w-1/3 w-full flex gap-3">
                        <select name="searchemployee" class="ft-field flex-1" onchange="this.form.submit()"
                            title="Filter by employee">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ request('searchemployee') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="searchlocation" class="ft-field flex-1" onchange="this.form.submit()"
                            title="Filter by location">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ request('searchlocation') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[150px]">No</th>
                                <th scope="col" class="w-[120px]">Date</th>
                                <th scope="col" class="w-[180px]">To</th>
                                <th scope="col" class="w-[150px]">Location</th>
                                <th scope="col" class="w-[250px]">Remarks</th>
                                <th scope="col" class="w-[100px]">Status</th>
                                <th scope="col" class="text-center w-[100px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transmittals as $transmittal)
                                <tr>
                                    <td class="whitespace-nowrap align-middle font-semibold" style="color: var(--ft-ink);">
                                        {{ $transmittal->transmittal_number }}
                                    </td>
                                    <td class="whitespace-nowrap align-middle" style="color: var(--ft-text);">
                                        {{ Carbon::parse($transmittal->transmittal_date)->format('Y-m-d') }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $transmittal->issued_to ? $transmittal->issuedTo->last_name . ', ' . $transmittal->issuedTo->first_name . ' ' . $transmittal->issuedTo->middle_name : '' }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $transmittal->location ? $transmittal->location->name : '' }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $transmittal->remarks }}
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $statuses = [
                                                0 => ['class' => 'ft-badge-no', 'label' => 'Voided'],
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                            ];
                                            $status = $statuses[$transmittal->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }} whitespace-nowrap">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('transmittal.show', $transmittal->id) }}"
                                                title="Edit transmittal : {{ $transmittal->transmittal_number }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('asset.print-transmittal', $transmittal->id) }}"
                                                target="_blank"
                                                title="Print transmittal : {{ $transmittal->transmittal_number }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-yellow-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                                    <path
                                                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                                </svg>
                                            </a>

                                            @if ($transmittal->status == 0 || Auth::user()->role == 2)
                                                <button type="button"
                                                    title="{{ Auth::user()->role == 2 ? 'Unable to void' : 'Voided already' }} : {{ $transmittal->transmittal_number }}"
                                                    disabled class="p-1.5 rounded text-gray-300 cursor-not-allowed">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button"
                                                    title="Void transmittal : {{ $transmittal->transmittal_number }}"
                                                    data-id="{{ $transmittal->id }}"
                                                    data-code="{{ $transmittal->transmittal_number }}"
                                                    data-description="{{ $transmittal->purpose }}"
                                                    onclick="voidTransmittal(this)"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="ft-empty">No transmittals found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $transmittals->links() }}
                </div>
            </div>
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
