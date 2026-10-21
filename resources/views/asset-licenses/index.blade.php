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
                        <h1>License & Permit Management</h1>
                        <p>Track and monitor asset licenses and permits</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @if (!$canCreate) disabled @endif data-modal-target="alert-modal"
                            data-modal-toggle="{{ $canCreate ? 'alert-modal' : '' }}"
                            class="ft-btn ft-btn-ghost {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                            </svg>
                            Setup Alerts
                        </button>
                        <button @if (!$canCreate) disabled @endif data-modal-target="add-modal"
                            data-modal-toggle="{{ $canCreate ? 'add-modal' : '' }}"
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add License
                        </button>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Total Licenses',
                            'value' => $totalLicenses,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent);" fill="currentColor" viewBox="0 0 16 16"><path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/><path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/></svg>',
                        ],
                        [
                            'title' => 'Active',
                            'value' => $activeLicenses,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>',
                        ],
                        [
                            'title' => 'Expiring Soon',
                            'value' => $expiringSoonLicenses,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-warning-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/><path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>',
                        ],
                        [
                            'title' => 'Expired',
                            'value' => $expiredLicenses,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-danger-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                        <input type="text" name="search" placeholder="Search by name or description..." class="ft-field"
                            value="{{ request()->query('search') }}" oninput="this.form.submit()">
                    </div>
                    <div class="md:w-1/3 w-full flex gap-3">
                        <select name="searchType" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">Select license type</option>
                            @foreach ($allLicenseTypes as $licenseType)
                                <option value="{{ $licenseType->id }}"
                                    {{ request('searchType') == $licenseType->id ? 'selected' : '' }}>
                                    {{ $licenseType->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Expired</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Expiring Soon</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" style="min-width: 110px;">Asset ID</th>
                                <th scope="col" style="min-width: 180px;">Name</th>
                                <th scope="col" style="min-width: 130px;">Type</th>
                                <th scope="col" style="min-width: 120px;">Number/ Serial</th>
                                <th scope="col" style="min-width: 150px;">Issuing Authority</th>
                                <th scope="col" style="min-width: 110px;">Expiration Date</th>
                                <th scope="col" style="min-width: 120px;">Status</th>
                                <th scope="col" style="min-width: 200px;">Alert</th>
                                @if ($canUpdate || $canDelete)
                                    <th scope="col" class="text-center" style="width: 60px;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assetLicenses as $assetLicense)
                                <tr>
                                    <td class="whitespace-nowrap align-middle font-semibold" style="color: var(--ft-ink);">
                                        {{ $assetLicense->asset->asset_code }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $assetLicense->asset->name }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $assetLicense->license_type->name }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $assetLicense->license_number }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $assetLicense->issuing_authority }}
                                    </td>
                                    <td class="whitespace-nowrap align-middle" style="color: var(--ft-text);">
                                        {{ $assetLicense->expiration_date ? Carbon::parse($assetLicense->expiration_date)->format('Y-m-d') : 'N/A' }}
                                    </td>

                                    {{-- Fixed Status Column --}}
                                    <td class="align-middle">
                                        @php
                                            $statuses = [
                                                0 => ['class' => 'ft-badge-no', 'label' => 'Expired'],
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                                2 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Expiring Soon',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                            ];
                                            $status = $statuses[$assetLicense->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }}"
                                            style="{{ $status['style'] ?? '' }} whitespace-nowrap;">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>

                                    {{-- Fixed Alert Column --}}
                                    <td class="align-middle">
                                        @if ($assetLicense->status !== '1')
                                            <div class="flex items-center gap-1.5 text-xs font-medium"
                                                style="color: var(--ft-{{ $assetLicense->status_label['color'] }}-text, var(--ft-text));">
                                                <div class="shrink-0">{!! $assetLicense->status_label['icon'] !!}</div>
                                                <span>{{ $assetLicense->status_label['label'] }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    @if ($canUpdate || $canDelete)
                                        <td class="align-middle text-center">
                                            <button type="button" title="Edit license {{ $assetLicense->asset->name }}"
                                                data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                data-id="{{ $assetLicense->id }}"
                                                data-license_type_id="{{ $assetLicense->license_type_id }}"
                                                data-asset_id="{{ $assetLicense->asset_id }}"
                                                data-license_number="{{ $assetLicense->license_number }}"
                                                data-issuing_authority="{{ $assetLicense->issuing_authority }}"
                                                data-issue_date="{{ $assetLicense->issue_date ? Carbon::parse($assetLicense->issue_date)->format('Y-m-d') : '' }}"
                                                data-expiration_date="{{ $assetLicense->expiration_date ? Carbon::parse($assetLicense->expiration_date)->format('Y-m-d') : '' }}"
                                                data-status="{{ $assetLicense->status }}" onclick="openEditModal(this)"
                                                class="inline-flex p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="ft-empty">No asset licenses found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $assetLicenses->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create alert modal -->
    <div id="alert-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Modify Alert Settings
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="alert-modal">
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
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                            <div>
                                <label for="alert_days_before" class="ft-label">Days Before Expiry <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="alert_days_before" id="alert_days_before"
                                    value="{{ $licenseExpiringDays }}" class="ft-field" required>
                            </div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create alert modal -->

    <!-- Create license modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Add New License
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
                <div class="p-4 overflow-y-auto max-h-[75vh]">
                    <form action="{{ route('asset-licenses.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <label for="asset_id" class="ft-label">Asset Name <span
                                        class="text-red-600">*</span></label>
                                <select name="asset_id" id="asset_id" class="ft-field select2" required>
                                    <option value="" selected>Select asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->name }} -
                                            {{ $asset->asset_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="license_type_id" class="ft-label">License Type <span
                                        class="text-red-600">*</span></label>
                                <select id="license_type_id" name="license_type_id" class="ft-field" required>
                                    <option value="" selected>Select type</option>
                                    @foreach ($licenseTypes as $licenseType)
                                        <option value="{{ $licenseType->id }}">{{ $licenseType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="license_number" class="ft-label">License/ Serial Number <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="license_number" id="license_number" class="ft-field"
                                    placeholder="License/ permit number" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="issuing_authority" class="ft-label">Issuing Authority <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="issuing_authority" id="issuing_authority" class="ft-field"
                                    placeholder="Authority or department name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="issue_date" class="ft-label">Date Issued</label>
                                <input type="date" name="issue_date" id="issue_date" class="ft-field">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="expiration_date" class="ft-label">Date Expired <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="expiration_date" id="expiration_date" class="ft-field"
                                    required>
                            </div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Add License
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create license modal -->

    <!-- Modal Edit -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Update License Type
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
                <div class="p-4 overflow-y-auto max-h-[75vh]">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="edit_id" id="edit_id">

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <label for="edit_asset_id" class="ft-label">Asset Name <span
                                        class="text-red-600">*</span></label>
                                <select name="edit_asset_id" id="edit_asset_id" class="ft-field select2" required>
                                    <option value="">Select asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->name }} -
                                            {{ $asset->asset_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="edit_license_type_id" class="ft-label">License Type <span
                                        class="text-red-600">*</span></label>
                                <select id="edit_license_type_id" name="edit_license_type_id" class="ft-field" required>
                                    <option value="">Select type</option>
                                    @foreach ($licenseTypes as $licenseType)
                                        <option value="{{ $licenseType->id }}">{{ $licenseType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_license_number" class="ft-label">License/ Serial Number <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="edit_license_number" id="edit_license_number"
                                    class="ft-field" placeholder="License/ permit number" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_issuing_authority" class="ft-label">Issuing Authority <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="edit_issuing_authority" id="edit_issuing_authority"
                                    class="ft-field" placeholder="Authority or department name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_issue_date" class="ft-label">Date Issued</label>
                                <input type="date" name="edit_issue_date" id="edit_issue_date" class="ft-field">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_expiration_date" class="ft-label">Date Expired <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="edit_expiration_date" id="edit_expiration_date"
                                    class="ft-field" required>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            Update License
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End edit modal -->

    <script>
        $(document).ready(function() {
            $('#asset_id').select2({
                placeholder: "Select asset",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#add-modal')
            });
            $('#edit_asset_id').select2({
                placeholder: "Select asset",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#edit-modal')
            });
        });

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

            $('#edit_id').val(id);
            $('#edit_asset_id').val(button.getAttribute('data-asset_id')).trigger('change');
            $('#edit_license_type_id').val(button.getAttribute('data-license_type_id')).trigger('change');

            $('#edit_license_number').val(button.getAttribute('data-license_number'));
            $('#edit_issuing_authority').val(button.getAttribute('data-issuing_authority'));
            $('#edit_issue_date').val(button.getAttribute('data-issue_date'));
            $('#edit_expiration_date').val(button.getAttribute('data-expiration_date'));

            document.getElementById('editForm').action = `/asset-licenses/${id}`;
        }
    </script>
@endsection
