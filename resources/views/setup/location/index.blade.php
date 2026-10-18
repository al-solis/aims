@extends('dashboard')
@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    {{-- Colors come from <html data-theme="..."> defined in the layout; do not set data-theme here --}}
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>Location Management</h1>
                        <p>Manage locations and sub-locations for asset tracking</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('setup.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>
                        <button data-modal-target="add-modal" data-modal-toggle="add-modal" class="ft-btn ft-btn-primary"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Location
                        </button>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Total Locations',
                            'value' => $totalLocations,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent);" fill="currentColor" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>',
                        ],
                        [
                            'title' => 'Active Locations',
                            'value' => $activeLocations,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>',
                        ],
                        [
                            'title' => 'Total Sub-Locations',
                            'value' => $totalSubLocations,
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
                    <div class="md:w-1/3 w-full flex gap-3">
                        <select name="cluster" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Clusters</option>
                            @foreach ($clusters as $cluster)
                                <option value="{{ $cluster->id }}"
                                    {{ request('cluster') == $cluster->id ? 'selected' : '' }}>
                                    {{ $cluster->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Under Maintenance
                            </option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[100px]">Code</th>
                                <th scope="col" class="w-[200px]">Name</th>
                                <th scope="col" class="w-[250px]">Description</th>
                                <th scope="col" class="w-[350px]">Address</th>
                                <th scope="col" class="w-[80px]">Contact</th>
                                <th scope="col" class="w-[150px]">Sub-Locations</th>
                                <th scope="col" class="w-[150px]">Cluster</th>
                                <th scope="col" class="w-[150px]">Status</th>
                                <th scope="col" class="text-center w-[50px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $location)
                                <tr>
                                    <td class="font-semibold whitespace-normal break-all align-top"
                                        style="color: var(--ft-ink);">
                                        {{ $location->code }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $location->name }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $location->description ?? '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $location->address ?? '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $location->contact_number ?? '—' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('location.sublocation.index', $location->id) }}"
                                            class="font-medium hover:underline" style="color: var(--ft-accent);">
                                            ({{ $location->sublocations_count }})
                                            sub-locations
                                        </a>
                                    </td>
                                    <td>
                                        @if ($location->cluster)
                                            <span class="ft-badge"
                                                style="background: var(--ft-neutral-bg); color: var(--ft-neutral-text);">
                                                {{ $location->cluster->name }}
                                            </span>
                                        @else
                                            <span class="ft-badge"
                                                style="background: var(--ft-mist); color: var(--ft-muted); border: 1px solid var(--ft-line);">
                                                No Cluster
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statuses = [
                                                0 => ['class' => 'ft-badge-no', 'label' => 'Inactive'],
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                                2 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Under Maintenance',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                            ];
                                            $status = $statuses[$location->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }}"
                                            style="{{ $status['style'] ?? '' }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center">
                                            <button type="button" title="Edit location {{ $location->name }}"
                                                data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                data-id="{{ $location->id }}" data-name="{{ $location->name }}"
                                                data-code="{{ $location->code }}"
                                                data-cluster="{{ $location->cluster_id }}"
                                                data-description="{{ $location->description }}"
                                                data-address="{{ $location->address }}"
                                                data-contact="{{ $location->contact_number }}"
                                                data-status="{{ $location->status }}" onclick="openEditModal(this)"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="ft-empty">No locations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $locations->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create location modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Add New Location
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="add-modal">
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
                <div class="p-4 overflow-y-auto max-h-[70vh]">
                    <form action="{{ route('location.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                            <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                                <div class="w-full md:col-span-1">
                                    <label for="code" class="ft-label">Code <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" name="code" id="code" minlength="1" maxlength="15"
                                        pattern="[A-Za-z0-9\-]+"
                                        title="Code can only contain letters, numbers, and hyphens"
                                        oninput="this.value = this.value.replace(/[^A-Za-z0-9\-]/g, '')" class="ft-field"
                                        placeholder="e.g. HQ-MAIN" required>
                                </div>
                                <div class="w-full md:col-span-2">
                                    <label for="cluster" class="ft-label">Cluster <span
                                            class="text-red-600">*</span></label>
                                    <select name="cluster" id="cluster" class="ft-field" required>
                                        <option value="">Select a cluster</option>
                                        @foreach ($clusters as $cluster)
                                            <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="w-full">
                                <label for="name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" maxlength="60" class="ft-field"
                                    placeholder="e.g. Headquarters Main Office" required>
                            </div>
                            <div>
                                <label for="description" class="ft-label">Description</label>
                                <textarea name="description" id="description" rows="2" maxlength="150" class="ft-field"
                                    placeholder="Location long description"></textarea>
                            </div>
                            <div>
                                <label for="address" class="ft-label">Address</label>
                                <textarea name="address" id="address" rows="3" maxlength="255" class="ft-field"
                                    placeholder="Location address"></textarea>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                                <div class="md:col-span-2">
                                    <label for="contact_number" class="ft-label">Contact Number</label>
                                    <input type="text" name="contact_number" id="contact_number" maxlength="20"
                                        class="ft-field" placeholder="e.g. 123-456-7890" required>
                                </div>
                                <div class="md:col-span-1">
                                    <label for="status" class="ft-label">Status <span
                                            class="text-red-600">*</span></label>
                                    <select id="status" name="status" class="ft-field" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                        <option value="2">Under Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Add Location
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create location modal -->

    <!-- Modal Edit -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Update Location
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="edit-modal">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 overflow-y-auto max-h-[70vh]">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="edit_id" id="edit_id">

                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                            <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                                <div class="w-full md:col-span-1">
                                    <label for="edit_code" class="ft-label">Code <span
                                            class="text-red-600">*</span></label>
                                    <input type="text" name="edit_code" id="edit_code" minlength="1" maxlength="15"
                                        pattern="[A-Za-z0-9\-]+"
                                        title="Code can only contain letters, numbers, and hyphens"
                                        oninput="this.value = this.value.replace(/[^A-Za-z0-9\-]/g, '')" class="ft-field"
                                        placeholder="e.g. HQ-MAIN" required>
                                </div>
                                <div class="w-full md:col-span-2">
                                    <label for="edit_cluster" class="ft-label">Cluster <span
                                            class="text-red-600">*</span></label>
                                    <select name="edit_cluster" id="edit_cluster" class="ft-field" required>
                                        <option value="">Select a cluster</option>
                                        @foreach ($clusters as $cluster)
                                            <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="w-full">
                                <label for="edit_name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="edit_name" id="edit_name" maxlength="60" class="ft-field"
                                    placeholder="e.g. Headquarters Main Office" required>
                            </div>
                            <div>
                                <label for="edit_description" class="ft-label">Description</label>
                                <textarea name="edit_description" id="edit_description" rows="2" maxlength="150" class="ft-field"
                                    placeholder="Location description"></textarea>
                            </div>
                            <div>
                                <label for="edit_address" class="ft-label">Address</label>
                                <textarea name="edit_address" id="edit_address" rows="2" maxlength="255" class="ft-field"
                                    placeholder="Location address"></textarea>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                                <div class="md:col-span-2">
                                    <label for="edit_contact_number" class="ft-label">Contact Number</label>
                                    <input type="text" name="edit_contact_number" id="edit_contact_number"
                                        maxlength="20" class="ft-field" placeholder="e.g. 123-456-7890" required>
                                </div>
                                <div class="md:col-span-1">
                                    <label for="edit_status" class="ft-label">Status <span
                                            class="text-red-600">*</span></label>
                                    <select id="edit_status" name="edit_status" class="ft-field" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                        <option value="2">Under Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            Update Location
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End edit modal -->

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

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_cluster').value = button.getAttribute('data-cluster');
            document.getElementById('edit_code').value = button.getAttribute('data-code');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_description').value = button.getAttribute('data-description');
            document.getElementById('edit_address').value = button.getAttribute('data-address');
            document.getElementById('edit_contact_number').value = button.getAttribute('data-contact');
            document.getElementById('edit_status').value = button.getAttribute('data-status');

            const form = document.getElementById('editForm');
            form.action = `location/${id}`;
        }
    </script>
@endsection
