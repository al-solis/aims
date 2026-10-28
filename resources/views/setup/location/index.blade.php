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

                {{-- Locations Map --}}
                <div class="ft-info" style="padding: 0.75rem;">
                    <div class="flex items-center justify-between mb-2 px-1">
                        <p class="ft-eyebrow">Locations Map</p>
                        <p class="text-xs" style="color: var(--ft-muted);">
                            {{ $mapLocations->count() }} of {{ $locations->total() }} plotted
                        </p>
                    </div>
                    @if (config('services.google_maps.key'))
                        <div id="locations-map" class="w-full h-80 rounded-lg border border-[var(--ft-line)]"></div>

                        <div class="flex flex-wrap gap-4 mt-2 px-1 text-xs" style="color: var(--ft-muted);">
                            <span><span style="color:#dc2626;">●</span> Understaffed</span>
                            <span><span style="color:#16a34a;">●</span> Fully staffed</span>
                            <span><span style="color:#d97706;">●</span> Overstaffed</span>
                            <span><span style="color:#6b7280;">●</span> No requirement set</span>
                        </div>
                    @else
                        <p class="ft-empty">Set GOOGLE_MAPS_API_KEY to display the map.</p>
                    @endif
                </div>

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
                                <th scope="col" class="w-[130px]">Deployed / Required</th>
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
                                    {{-- <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $location->address ?? '—' }}
                                    </td> --}}
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $location->address ?? '—' }}
                                        @if ($location->latitude !== null && $location->longitude !== null)
                                            <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}"
                                                target="_blank" rel="noopener"
                                                class="block text-xs font-medium hover:underline"
                                                style="color: var(--ft-accent);">
                                                View on map
                                            </a>
                                        @endif
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
                                    @php
                                        $required = (int) $location->employee_count;
                                        $deployed = (int) $location->employees_count;
                                        $staffColor =
                                            $required === 0
                                                ? 'var(--ft-muted)'
                                                : ($deployed < $required
                                                    ? '#dc2626'
                                                    : ($deployed > $required
                                                        ? '#d97706'
                                                        : '#16a34a'));
                                    @endphp
                                    <td class="align-top font-medium" style="color: {{ $staffColor }};">
                                        {{ number_format($deployed) }} / {{ number_format($required) }}
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
                                                data-latitude="{{ $location->latitude }}"
                                                data-longitude="{{ $location->longitude }}"
                                                data-employees="{{ $location->employee_count }}"
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
                            <div>
                                <label class="ft-label">Map Location</label>
                                <div id="add_map" class="w-full h-56 rounded-lg border border-[var(--ft-line)]"></div>
                                <p class="text-xs mt-1" style="color: var(--ft-muted);">Click the map or drag the pin to
                                    set coordinates.</p>
                            </div>
                            <div class="grid gap-4 grid-cols-2">
                                <div>
                                    <label for="latitude" class="ft-label">Latitude</label>
                                    <input type="number" step="any" min="-90" max="90" name="latitude"
                                        id="latitude" class="ft-field" placeholder="e.g. 14.5995">
                                </div>
                                <div>
                                    <label for="longitude" class="ft-label">Longitude</label>
                                    <input type="number" step="any" min="-180" max="180" name="longitude"
                                        id="longitude" class="ft-field" placeholder="e.g. 120.9842">
                                </div>
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
                            <div>
                                <label for="employee_count" class="ft-label">Required Employees</label>
                                <input type="number" min="0" name="employee_count" id="employee_count"
                                    value="0" class="ft-field">
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
                            <div>
                                <label class="ft-label">Map Location</label>
                                <div id="edit_map" class="w-full h-56 rounded-lg border border-[var(--ft-line)]"></div>
                                <p class="text-xs mt-1" style="color: var(--ft-muted);">Click the map or drag the pin to
                                    set coordinates.</p>
                            </div>
                            <div class="grid gap-4 grid-cols-2">
                                <div>
                                    <label for="edit_latitude" class="ft-label">Latitude</label>
                                    <input type="number" step="any" min="-90" max="90"
                                        name="edit_latitude" id="edit_latitude" class="ft-field"
                                        placeholder="e.g. 14.5995">
                                </div>
                                <div>
                                    <label for="edit_longitude" class="ft-label">Longitude</label>
                                    <input type="number" step="any" min="-180" max="180"
                                        name="edit_longitude" id="edit_longitude" class="ft-field"
                                        placeholder="e.g. 120.9842">
                                </div>
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
                            <div>
                                <label for="edit_employee_count" class="ft-label">Required Employees</label>
                                <input type="number" min="0" name="edit_employee_count" id="edit_employee_count"
                                    value="0" class="ft-field">
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

    <style>
        .map-label {
            position: absolute;
            transform: translate(-50%, 14px);
            background: #fff;
            border: 2px solid #6b7280;
            /* overridden per location in JS */
            border-radius: 6px;
            padding: 2px 6px;
            font: 12px/1.3 system-ui, sans-serif;
            text-align: center;
            white-space: nowrap;
            pointer-events: none;
            /* clicks still reach the marker */
            box-shadow: 0 1px 3px rgba(0, 0, 0, .3);
        }

        .map-label-name {
            font-weight: 600;
            color: #111827;
        }

        .map-label-count {
            font-weight: 600;
        }
    </style>

    <script>
        const DEFAULT_CENTER = {
            lat: 14.5995,
            lng: 120.9842
        }; // Manila
        const mapLocations = @json($mapLocations);
        const pickers = {};

        function clearModalFields() {
            const form = document.querySelector('form');
            form.reset();

            setTimeout(() => {
                const successMessage = document.querySelector('[data-success]');
                if (successMessage) {
                    successMessage.remove();
                }
            }, 3000);
        }

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_cluster').value = button.getAttribute('data-cluster');
            document.getElementById('edit_code').value = button.getAttribute('data-code');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_description').value = button.getAttribute('data-description');
            document.getElementById('edit_address').value = button.getAttribute('data-address');
            document.getElementById('edit_contact_number').value = button.getAttribute('data-contact');
            document.getElementById('edit_latitude').value = button.getAttribute('data-latitude') || '';
            document.getElementById('edit_longitude').value = button.getAttribute('data-longitude') || '';
            document.getElementById('edit_employee_count').value = button.getAttribute('data-employees') || 0;
            document.getElementById('edit_status').value = button.getAttribute('data-status');

            const form = document.getElementById('editForm');
            form.action = `location/${id}`;

            // Map needs a resize once the modal is visible
            setTimeout(() => pickers.edit && pickers.edit.sync(), 200);
        }

        // Click-to-place / drag pin picker bound to lat/lng inputs
        function createPicker(mapElId, latId, lngId) {
            const latEl = document.getElementById(latId);
            const lngEl = document.getElementById(lngId);
            const map = new google.maps.Map(document.getElementById(mapElId), {
                center: DEFAULT_CENTER,
                zoom: 6,
                streetViewControl: false,
                mapTypeControl: false,
            });
            const marker = new google.maps.Marker({
                map,
                draggable: true,
                visible: false
            });

            const place = (pos) => {
                marker.setPosition(pos);
                marker.setVisible(true);
                latEl.value = pos.lat.toFixed(7);
                lngEl.value = pos.lng.toFixed(7);
            };

            const fromInputs = () => {
                const lat = parseFloat(latEl.value);
                const lng = parseFloat(lngEl.value);
                if (isFinite(lat) && isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180) {
                    const pos = {
                        lat,
                        lng
                    };
                    marker.setPosition(pos);
                    marker.setVisible(true);
                    map.setCenter(pos);
                    map.setZoom(16);
                } else {
                    marker.setVisible(false);
                }
            };

            map.addListener('click', (e) => place(e.latLng.toJSON()));
            marker.addListener('dragend', (e) => place(e.latLng.toJSON()));
            latEl.addEventListener('change', fromInputs);
            lngEl.addEventListener('change', fromInputs);

            return {
                sync() {
                    google.maps.event.trigger(map, 'resize');
                    fromInputs();
                },
            };
        }

        function staffingInfo(required, deployed) {
            if (required === 0) return {
                color: '#6b7280',
                label: 'No requirement set'
            };
            if (deployed < required) return {
                color: '#dc2626',
                label: `Understaffed (short by ${required - deployed})`
            };
            if (deployed > required) return {
                color: '#d97706',
                label: `Overstaffed (+${deployed - required})`
            };
            return {
                color: '#16a34a',
                label: 'Fully staffed'
            };
        }

        function initOverviewMap() {
            const el = document.getElementById('locations-map');
            if (!el) return;

            const map = new google.maps.Map(el, {
                center: DEFAULT_CENTER,
                zoom: 6,
                streetViewControl: false,
                mapTypeControl: false,
            });
            if (!mapLocations.length) return;
            defineLabelOverlay();

            const bounds = new google.maps.LatLngBounds();
            const info = new google.maps.InfoWindow();

            mapLocations.forEach((loc) => {
                const pos = {
                    lat: Number(loc.latitude),
                    lng: Number(loc.longitude)
                };
                const required = Number(loc.employee_count) || 0;
                const deployed = Number(loc.employees_count) || 0;
                const staff = staffingInfo(required, deployed);

                const marker = new google.maps.Marker({
                    map,
                    position: pos,
                    title: `${loc.name}: ${deployed}/${required}`,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 9,
                        fillColor: staff.color,
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                    },
                });

                new LabelOverlay(pos, loc.name, `${deployed} / ${required}`, staff.color).setMap(map);

                marker.addListener('click', () => {
                    const row = (text, bold = false) => {
                        const d = document.createElement('div');
                        d.textContent = text;
                        if (bold) d.style.fontWeight = '600';
                        return d;
                    };

                    const box = document.createElement('div');
                    box.style.minWidth = '180px';
                    box.append(
                        row(`${loc.name} (${loc.code})`, true),
                        row(loc.address || ''),
                        row(`Required employees: ${required}`),
                        row(`Deployed employees: ${deployed}`)
                    );
                    const status = row(staff.label, true);
                    status.style.color = staff.color;
                    box.append(status);

                    info.setContent(box);
                    info.open(map, marker);
                });

                bounds.extend(pos);
            });

            if (mapLocations.length === 1) {
                map.setCenter(bounds.getCenter());
                map.setZoom(15);
            } else {
                map.fitBounds(bounds);
            }
        }

        let LabelOverlay;

        // Must run after the Google Maps API has loaded
        function defineLabelOverlay() {
            LabelOverlay = class extends google.maps.OverlayView {
                constructor(position, nameText, countText, color) {
                    super();
                    this.position = position;

                    this.div = document.createElement('div');
                    this.div.className = 'map-label';
                    this.div.style.borderColor = color;

                    const name = document.createElement('div');
                    name.className = 'map-label-name';
                    name.textContent = nameText;

                    const count = document.createElement('div');
                    count.className = 'map-label-count';
                    count.style.color = color;
                    count.textContent = countText;

                    this.div.append(name, count);
                }

                onAdd() {
                    this.getPanes().floatPane.appendChild(this.div);
                }

                draw() {
                    const p = this.getProjection().fromLatLngToDivPixel(new google.maps.LatLng(this.position));
                    if (p) {
                        this.div.style.left = p.x + 'px';
                        this.div.style.top = p.y + 'px';
                    }
                }

                onRemove() {
                    this.div.remove();
                }
            };
        }

        // Google Maps callback
        async function initMaps() {
            try {
                await google.maps.importLibrary('maps');
                await google.maps.importLibrary('marker');

                initOverviewMap();

                pickers.add = createPicker(
                    'add_map',
                    'latitude',
                    'longitude'
                );

                pickers.edit = createPicker(
                    'edit_map',
                    'edit_latitude',
                    'edit_longitude'
                );
            } catch (error) {
                console.error('Google Maps initialization failed:', error);
            }
        }
    </script>

    @if (config('services.google_maps.key'))
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&loading=async&callback=initMaps">
        </script>
    @endif
@endsection
