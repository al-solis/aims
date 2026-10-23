@extends('dashboard')
@section('content')
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>DDO Location Setup</h1>
                        <p>Manage employees and reliever for DDO locations.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('setup.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        <button data-modal-target="copy-modal" data-modal-toggle="copy-modal" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                            </svg>
                            Copy Setup
                        </button>

                        <a href="{{ route('ddo.create') }}" class="ft-btn ft-btn-primary"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Setup for Location
                        </a>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Total Setups',
                            'value' => $totalSetups,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent);" fill="currentColor" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>',
                        ],
                        [
                            'title' => 'Active Setups',
                            'value' => $activeSetups,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>',
                        ],
                        [
                            'title' => 'Total Inactive',
                            'value' => $totalInactiveSetups,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-orange-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" /></svg>',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach ($cards as $card)
                        <div class="ft-info flex items-center gap-4" style="padding: 1rem;">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                style="background: var(--ft-surface); border: 1px solid var(--ft-line);">
                                {!! $card['icon'] !!}
                            </div>
                            <div>
                                <p class="ft-eyebrow">{{ $card['title'] }}</p>
                                <p class="text-xl font-semibold mt-0.5" style="color: var(--ft-ink);">{{ $card['value'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

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
                        <input type="text" name="search" placeholder="Search by name, code, or description..."
                            class="ft-field" value="{{ request()->query('search') }}" oninput="this.form.submit()">
                    </div>
                    <div class="md:w-1/3 w-full">
                        <select name="status" class="ft-field" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[100px]">Code</th>
                                <th scope="col" class="w-[200px]">Location</th>
                                <th scope="col" class="w-[250px]">Remarks</th>
                                <th scope="col" class="w-[100px]">Count</th>
                                <th scope="col" class="w-[100px]">Status</th>
                                <th scope="col" class="text-center w-[100px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($setups as $setup)
                                <tr>
                                    <td class="whitespace-nowrap align-middle font-semibold" style="color: var(--ft-ink);">
                                        {{ $setup->id }}
                                    </td>
                                    <td class="whitespace-normal break-all align-middle" style="color: var(--ft-text);">
                                        {{ $setup->location->name }}
                                    </td>
                                    <td class="whitespace-normal break-all align-middle" style="color: var(--ft-muted);">
                                        {{ $setup->remarks ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap align-middle tabular-nums" style="color: var(--ft-text);">
                                        {{ $setup->ddoDetails->count() }}
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $statuses = [
                                                0 => ['class' => 'ft-badge-no', 'label' => 'Inactive'],
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                            ];
                                            $status = $statuses[$setup->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }} whitespace-nowrap">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center">
                                            <a href="{{ route('ddo.edit', $setup->id) }}"
                                                title="Edit location {{ $setup->location->name }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="ft-empty">No DDO setup found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $setups->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Copy location modal -->
    <div id="copy-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Copy Setup
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="copy-modal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 overflow-y-auto max-h-[75vh]">
                    <form id="copyForm">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-1">
                                    <label for="source_id" class="ft-label">Source Location <span
                                            class="text-red-600">*</span></label>
                                    <select name="source_id" id="source_id" class="ft-field" required>
                                        <option value="" selected disabled>Select source location</option>
                                        @foreach ($setups as $setup)
                                            <option value="{{ $setup->location_id }}">{{ $setup->location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <label for="destination_id" class="ft-label">Destination Location <span
                                            class="text-red-600">*</span></label>
                                    <select name="destination_id" id="destination_id" class="ft-field" required>
                                        <option value="" selected disabled>Select destination location</option>
                                        @foreach ($destination as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="copySetup(this)"
                            class="ft-btn ft-btn-primary w-full justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="mr-2 bi bi-copy" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                            </svg>
                            Copy Setup
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End copy location modal -->

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

        function copySetup(button) {
            const sourceSelect = document.getElementById('source_id');
            const destinationSelect = document.getElementById('destination_id');

            const sourceId = sourceSelect.value;
            const sourceName = sourceSelect.options[sourceSelect.selectedIndex].text;
            const destinationId = destinationSelect.value;
            const destinationName = destinationSelect.options[destinationSelect.selectedIndex].text;

            if (!sourceId || !destinationId) {
                alert('Please select both source and destination locations.');
                return;
            }

            if (sourceId === destinationId) {
                alert('Source and destination locations must be different.');
                return;
            }

            if (!confirm('Are you sure you want to copy the setup from ' + sourceName + ' to ' + destinationName + '?')) {
                return;
            }

            // Disable the button to prevent double submission
            const copyButton = button;
            copyButton.disabled = true;
            copyButton.textContent = 'Copying...';

            $.ajax({
                url: '{{ route('ddo.location.copy') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    source_id: sourceId,
                    destination_id: destinationId
                },
                success: function(response) {
                    alert(response.success || 'Setup copied successfully.');

                    // Reset the form/selects
                    if (typeof clearModalFields === 'function') {
                        clearModalFields();
                    }

                    location.reload();
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Error copying setup.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    copyButton.disabled = false;
                    copyButton.textContent = 'Copy Setup';
                }
            });
        }
    </script>
@endsection
