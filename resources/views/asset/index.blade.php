@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
    @endphp

    <style>
        .custom-checkbox {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 1px solid var(--ft-border);
            background-color: var(--ft-surface);
            border-radius: 3px;
            cursor: pointer;
            vertical-align: middle;
        }

        .custom-checkbox:checked {
            background-color: var(--ft-accent);
            border-color: var(--ft-accent);
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
            background-size: 100% 100%;
        }
    </style>

    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>Asset Management</h1>
                        <p>Manage and track agency assets and property</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">

                        {{-- Bulk Actions --}}
                        <div id="bulk-actions" class="flex items-center gap-2 bg-black/20 p-1 rounded-lg"
                            style="{{ count(session('selected_assets', [])) > 0 ? '' : 'display:none;' }}">
                            <div class="relative">
                                <button type="button" id="print-selected-btn" onclick="togglePrintDropdown()"
                                    class="ft-btn ft-btn-ghost text-white border-transparent hover:bg-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                                    </svg>
                                    Print (<span id="selected-count">0</span>)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div id="print-dropdown"
                                    class="hidden absolute left-0 z-20 mt-1 w-44 bg-white border border-[var(--ft-line)] rounded-lg shadow-lg overflow-hidden text-gray-800">
                                    <a id="print-barcode-link" href="#" target="_blank" onclick="closePrintDropdown()"
                                        class="flex items-center gap-2 px-4 py-2 text-xs hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                                        </svg>
                                        Barcode
                                    </a>
                                    <a id="print-qr-link" href="#" target="_blank" onclick="closePrintDropdown()"
                                        class="flex items-center gap-2 px-4 py-2 text-xs hover:bg-gray-100 border-t border-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M2 2h2v2H2zM2 3h1v1H2z" />
                                            <path d="M6 0v6H0V0zM5 1H1v4h4zM4 12H2v2h2z" />
                                            <path d="M6 10v6H0v-6zm-5 1v4h4v-4zm11-9h2v2h-2z" />
                                            <path
                                                d="M10 0v6h6V0zm5 1v4h-4V1zM8 1V0h1v2H8zm0 2h1v1H8zm0 2h1v1H8zM8 8h1v1H8zm0 2h1v1H8zm0 2h1v2H8zm0 3h1v1H8zM10 8h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm2-8h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm2-8h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1z" />
                                        </svg>
                                        QR Code
                                    </a>
                                </div>
                            </div>
                            <button onclick="clearSelection()"
                                class="ft-btn ft-btn-ghost text-white border-transparent hover:bg-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z" />
                                </svg>
                                Clear
                            </button>
                        </div>

                        {{-- Main Actions --}}
                        <button @if (!$canCreate) disabled @endif data-modal-target="add-modal"
                            data-modal-toggle="{{ $canCreate ? 'add-modal' : '' }}"
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Asset
                        </button>

                        <a href="{{ route('transmittal.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
                            </svg>
                            Transmittal
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
                    <input type="hidden" name="selected_assets" id="selected_assets"
                        value="{{ implode(',', session('selected_assets', [])) }}">

                    <div class="md:w-2/3 w-full">
                        <input type="text" name="search" placeholder="Search by name, code, or serial..."
                            class="ft-field" value="{{ request()->query('search') }}" oninput="this.form.submit()">
                    </div>

                    <div class="md:w-1/3 w-full flex flex-col md:flex-row gap-3">
                        <select name="searchcat" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('searchcat') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="searchloc" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ request('searchloc') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="searchstat" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('searchstat') == 1 ? 'selected' : '' }}>Available</option>
                            <option value="2" {{ request('searchstat') == 2 ? 'selected' : '' }}>Active</option>
                            <option value="3" {{ request('searchstat') == 3 ? 'selected' : '' }}>Assigned</option>
                            <option value="4" {{ request('searchstat') == 4 ? 'selected' : '' }}>Maintenance</option>
                            <option value="5" {{ request('searchstat') == 5 ? 'selected' : '' }}>Retired</option>
                            <option value="6" {{ request('searchstat') == 6 ? 'selected' : '' }}>Lost</option>
                            <option value="7" {{ request('searchstat') == 7 ? 'selected' : '' }}>Damaged</option>
                            <option value="8" {{ request('searchstat') == 8 ? 'selected' : '' }}>Vault</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center w-[40px]">
                                    <input type="checkbox" id="select-all" class="custom-checkbox">
                                </th>
                                <th scope="col" class="w-[100px]">Code</th>
                                <th scope="col" class="w-[180px]">Name</th>
                                <th scope="col" class="w-[100px]">Category</th>
                                <th scope="col" class="w-[100px]">Serial</th>
                                <th scope="col" class="w-[100px]">Status</th>
                                <th scope="col" class="w-[140px]">Assigned To</th>
                                <th scope="col" class="w-[120px]">Location</th>
                                <th scope="col" class="w-[150px]">License Status</th>
                                <th scope="col" class="text-center w-[150px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="asset-checkbox custom-checkbox"
                                            value="{{ $asset->id }}" @if (in_array($asset->id, session('selected_assets', []))) checked @endif>
                                    </td>
                                    <td class="whitespace-nowrap align-middle font-semibold"
                                        style="color: var(--ft-ink);">
                                        {{ $asset->asset_code }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $asset->name }} <br>
                                        <span class="text-xs"
                                            style="color: var(--ft-muted);">{{ $asset->subcategory }}</span>
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $asset->category_id ? $asset->category->name : '' }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $asset->serial }}
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $statuses = [
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Available'],
                                                2 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                                3 => ['class' => 'ft-badge-ok', 'label' => 'Assigned'],
                                                4 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Maintenance',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                                5 => ['class' => 'ft-badge-na', 'label' => 'Retired'],
                                                6 => ['class' => 'ft-badge-no', 'label' => 'Lost'],
                                                7 => ['class' => 'ft-badge-no', 'label' => 'Damaged'],
                                                8 => ['class' => 'ft-badge-na', 'label' => 'Vault'],
                                            ];
                                            $status = $statuses[$asset->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }} whitespace-nowrap"
                                            style="{{ $status['style'] ?? '' }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $asset->assigned_to ? $asset->assigned_user->last_name . ', ' . $asset->assigned_user->first_name . ' ' . $asset->assigned_user->middle_name : '' }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $asset->location_id ? $asset->location->name : '' }}
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $license = $asset->licenses->sortByDesc('expiration_date')->first();
                                        @endphp
                                        @if ($license)
                                            <div class="flex items-start gap-1.5 text-xs">
                                                <span class="mt-0.5"
                                                    style="color: var(--ft-{{ $license->status_label['color'] }}-text, var(--ft-text));">
                                                    {!! $license->status_label['icon'] !!}
                                                </span>
                                                <div class="flex flex-col items-start leading-tight">
                                                    <span class="font-medium"
                                                        style="color: var(--ft-{{ $license->status_label['color'] }}-text, var(--ft-text));">
                                                        {{ $license->status_label['label'] }}
                                                    </span>
                                                    <span style="color: var(--ft-muted);">
                                                        {{ $license->license_number }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            @if ($canUpdate)
                                                <button type="button" title="Edit asset: {{ $asset->asset_code }}"
                                                    data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                    data-id="{{ $asset->id }}" data-code="{{ $asset->asset_code }}"
                                                    data-name="{{ $asset->name }}"
                                                    data-description="{{ $asset->description }}"
                                                    data-category="{{ $asset->category_id }}"
                                                    data-subcategory="{{ $asset->subcategory }}"
                                                    data-serial="{{ $asset->serial }}" data-cost="{{ $asset->cost }}"
                                                    data-status="{{ $asset->status }}"
                                                    data-purchase_date="{{ $asset->purchase_date }}"
                                                    data-caliber="{{ $asset->caliber }}"
                                                    data-manufacturer="{{ $asset->manufacturer }}"
                                                    data-model="{{ $asset->model }}" data-serial="{{ $asset->serial }}"
                                                    data-assigned_to="{{ $asset->assigned_to }}"
                                                    data-location="{{ $asset->location_id }}"
                                                    data-sublocation="{{ $asset->subloc_id }}"
                                                    data-warranty="{{ $asset->warranty }}"
                                                    data-remarks="{{ $asset->remarks }}" onclick="openEditModal(this)"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd"
                                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <a href="{{ route('transfer.show', $asset->id) }}"
                                                title="Transfer asset: {{ $asset->asset_code }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="m11.798 8.271-3.182 1.97c-.27.166-.616-.036-.616-.372V9.1s-2.571-.3-4 2.4c.571-4.8 3.143-4.8 4-4.8v-.769c0-.336.346-.538.616-.371l3.182 1.969c.27.166.27.576 0 .742" />
                                                    <path
                                                        d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m.694 2.09A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09l-.636 7a1 1 0 0 1-.996.91H2.826a1 1 0 0 1-.995-.91zM6.172 2a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('asset.labels', ['asset_ids' => $asset->id]) }}"
                                                title="Generate barcode: {{ $asset->asset_code }}" target="_blank"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('asset.qr-code', ['asset_ids' => $asset->id]) }}"
                                                title="Generate QR: {{ $asset->asset_code }}" target="_blank"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M2 2h2v2H2zM2 3h1v1H2z" />
                                                    <path d="M6 0v6H0V0zM5 1H1v4h4zM4 12H2v2h2z" />
                                                    <path d="M6 10v6H0v-6zm-5 1v4h4v-4zm11-9h2v2h-2z" />
                                                    <path
                                                        d="M10 0v6h6V0zm5 1v4h-4V1zM8 1V0h1v2H8zm0 2h1v1H8zm0 2h1v1H8zM8 8h1v1H8zm0 2h1v1H8zm0 2h1v2H8zm0 3h1v1H8zM10 8h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm2-8h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm2-8h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1z" />
                                                </svg>
                                            </a>

                                            @if ($asset->category_id == 2 && $canCreate)
                                                <a href="{{ route('asset.odometer.show', $asset->id) }}"
                                                    title="Add odometer reading : {{ $asset->asset_code }}"
                                                    data-id="{{ $asset->id }}" data-code="{{ $asset->asset_code }}"
                                                    data-name="{{ $asset->name }}"
                                                    data-warranty="{{ $asset->warranty }}" onclick="openOdoModal(this)"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 2a.5.5 0 0 1 .5.5V4a.5.5 0 0 1-1 0V2.5A.5.5 0 0 1 8 2M3.732 3.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 8a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 7.31A.91.91 0 1 0 8.85 8.569l3.434-4.297a.39.39 0 0 0-.029-.518z" />
                                                        <path fill-rule="evenodd"
                                                            d="M6.664 15.889A8 8 0 1 1 9.336.11a8 8 0 0 1-2.672 15.78zm-4.665-4.283A11.95 11.95 0 0 1 8 10c2.186 0 4.236.585 6.001 1.606a7 7 0 1 0-12.002 0" />
                                                    </svg>
                                                </a>
                                            @else
                                                <button type="button"
                                                    title="Cannot add odometer reading : {{ $asset->asset_code }}"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed" disabled>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 2a.5.5 0 0 1 .5.5V4a.5.5 0 0 1-1 0V2.5A.5.5 0 0 1 8 2M3.732 3.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 8a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 7.31A.91.91 0 1 0 8.85 8.569l3.434-4.297a.39.39 0 0 0-.029-.518z" />
                                                        <path fill-rule="evenodd"
                                                            d="M6.664 15.889A8 8 0 1 1 9.336.11a8 8 0 0 1-2.672 15.78zm-4.665-4.283A11.95 11.95 0 0 1 8 10c2.186 0 4.236.585 6.001 1.606a7 7 0 1 0-12.002 0" />
                                                    </svg>
                                                </button>
                                            @endif

                                            @if ($canDelete)
                                                <button type="button" title="Retire asset: {{ $asset->asset_code }}"
                                                    data-id="{{ $asset->id }}" data-code="{{ $asset->asset_code }}"
                                                    onclick="retireAsset(this)"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z" />
                                                        <path
                                                            d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
                                                        <path
                                                            d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="ft-empty">No assets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create asset modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Add New Asset
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
                    <form action="{{ route('asset.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <label for="name" class="ft-label">Asset Name <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" class="ft-field"
                                    placeholder="Enter asset name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="cost" class="ft-label">Purchase Price <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="cost" id="cost" class="ft-field"
                                    placeholder="e.g. 0.00" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="description" class="ft-label">Description</label>
                                <textarea name="description" id="description" rows="2" class="ft-field"
                                    placeholder="Additional details about the asset"></textarea>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="category_id" class="ft-label">Category <span
                                        class="text-red-600">*</span></label>
                                <select id="category_id" name="category_id" class="ft-field" required>
                                    <option value="" selected disabled>Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="subcategory" class="ft-label">Sub-category</label>
                                <input type="text" name="subcategory" id="subcategory" class="ft-field"
                                    placeholder="e.g. Handgun, Patrol Vehicle">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="location_id" class="ft-label">Location</label>
                                <select id="location_id" name="location_id" data-target="#sublocation_id"
                                    class="ft-field select2">
                                    <option value="" selected disabled>Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="sublocation_id" class="ft-label">Sub-location</label>
                                <select id="sublocation_id" name="sublocation_id" class="ft-field">
                                    <option value="" selected disabled>Select sub-location</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="purchase_date" class="ft-label">Date Purchased <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="purchase_date" id="purchase_date" class="ft-field" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="caliber" class="ft-label">Caliber</label>
                                <input type="text" name="caliber" id="caliber" class="ft-field"
                                    placeholder="e.g. 9MM">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="manufacturer" class="ft-label">Manufacturer</label>
                                <input type="text" name="manufacturer" id="manufacturer" class="ft-field"
                                    placeholder="e.g. Brand/ Manufacturer">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="model" class="ft-label">Model</label>
                                <input type="text" name="model" id="model" class="ft-field"
                                    placeholder="e.g. Model number/ name">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="serial" class="ft-label">Serial</label>
                                <input type="text" name="serial" id="serial" class="ft-field"
                                    placeholder="e.g. Serial number">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="assigned_to" class="ft-label">Assigned To</label>
                                <select id="assigned_to" name="assigned_to" class="ft-field select2">
                                    <option value="0" selected disabled>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->last_name }},
                                            {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="warranty" class="ft-label">Warranty</label>
                                <textarea name="warranty" id="warranty" rows="1" class="ft-field"
                                    placeholder="Warranty details/ expiration"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="remarks" class="ft-label">Remarks</label>
                                <textarea name="remarks" id="remarks" rows="2" class="ft-field" placeholder="Remarks"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Add Asset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create asset modal -->

    <!-- Modal Edit -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Update Asset
                        @if (isset($asset) && $asset->asset_code)
                            ({{ $asset->asset_code }})
                        @endif
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
                            <div class="sm:col-span-1">
                                <label for="edit_name" class="ft-label">Asset Name <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="edit_name" id="edit_name" class="ft-field"
                                    placeholder="Enter asset name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_cost" class="ft-label">Purchase Price <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="edit_cost" id="edit_cost" class="ft-field"
                                    placeholder="e.g. 0.00" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_description" class="ft-label">Description</label>
                                <textarea name="edit_description" id="edit_description" rows="2" class="ft-field"
                                    placeholder="Additional details about the asset"></textarea>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_category_id" class="ft-label">Category <span
                                        class="text-red-600">*</span></label>
                                <select id="edit_category_id" name="edit_category_id" class="ft-field" required>
                                    <option value="" selected disabled>Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_subcategory" class="ft-label">Sub-category</label>
                                <input type="text" name="edit_subcategory" id="edit_subcategory" class="ft-field"
                                    placeholder="e.g. Handgun, Patrol Vehicle">
                            </div>

                            <div class="sm:col-span-1">
                                <input type="hidden" name="hidden_edit_location_id" id="hidden_edit_location_id">
                                <label for="edit_location_id" class="ft-label">Location</label>
                                <select id="edit_location_id" name="location_id" data-target="#edit_sublocation_id"
                                    class="ft-field select2">
                                    <option value="" selected>Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <input type="hidden" name="hidden_edit_sublocation_id" id="hidden_edit_sublocation_id">
                                <label for="edit_sublocation_id" class="ft-label">Sub-location</label>
                                <select id="edit_sublocation_id" name="sublocation_id" class="ft-field">
                                    <option value="" selected>Select sub-location</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <input type="hidden" name="asset_transfer_count" id="asset_transfer_count"
                                    value="">
                                <label for="edit_purchase_date" class="ft-label">Date Purchased <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="edit_purchase_date" id="edit_purchase_date" class="ft-field"
                                    required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_caliber" class="ft-label">Caliber</label>
                                <input type="text" name="edit_caliber" id="edit_caliber" class="ft-field"
                                    placeholder="e.g. 9MM">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_manufacturer" class="ft-label">Manufacturer</label>
                                <input type="text" name="edit_manufacturer" id="edit_manufacturer" class="ft-field"
                                    placeholder="e.g. Brand/ Manufacturer">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_model" class="ft-label">Model</label>
                                <input type="text" name="edit_model" id="edit_model" class="ft-field"
                                    placeholder="e.g. Model number/ name">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_serial" class="ft-label">Serial</label>
                                <input type="text" name="edit_serial" id="edit_serial" class="ft-field"
                                    placeholder="e.g. Serial number">
                            </div>

                            <div class="sm:col-span-2">
                                <input type="hidden" name="hidden_edit_assigned_to" id="hidden_edit_assigned_to">
                                <input type="hidden" name="hidden_edit_assigned_name" id="hidden_edit_assigned_name">
                                <label for="edit_assigned_to" class="ft-label">Assigned To</label>
                                <select id="edit_assigned_to" name="edit_assigned_to" class="ft-field select2">
                                    <option value="0" selected disabled>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->last_name }},
                                            {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_warranty" class="ft-label">Warranty</label>
                                <textarea name="edit_warranty" id="edit_warranty" rows="1" class="ft-field"
                                    placeholder="Warranty details/ expiration"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_remarks" class="ft-label">Remarks</label>
                                <textarea name="edit_remarks" id="edit_remarks" rows="2" class="ft-field" placeholder="Remarks"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            Update Asset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End edit modal -->

    <script>
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

        $(document).ready(function() {
            $('#assigned_to').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#add-modal')
            });

            $('#edit_assigned_to').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#edit-modal')
            });

            $('#location_id').select2({
                placeholder: "Select location",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#add-modal')
            });

            $('#edit_location_id').select2({
                placeholder: "Select location",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#edit-modal')
            });
        });

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            const editAssignedTo = document.getElementById('edit_assigned_to')
            const assignedTo = button.getAttribute('data-assigned_to');
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_description').value = button.getAttribute('data-description');
            document.getElementById('edit_cost').value = button.getAttribute('data-cost');
            document.getElementById('edit_category_id').value = button.getAttribute('data-category');
            document.getElementById('edit_subcategory').value = button.getAttribute('data-subcategory');
            document.getElementById('edit_serial').value = button.getAttribute('data-serial');
            document.getElementById('edit_location_id').value = button.getAttribute('data-location');
            document.getElementById('edit_purchase_date').value = button.getAttribute('data-purchase_date');
            document.getElementById('edit_manufacturer').value = button.getAttribute('data-manufacturer');
            document.getElementById('edit_model').value = button.getAttribute('data-model');
            document.getElementById('edit_caliber').value = button.getAttribute('data-caliber');
            document.getElementById('edit_sublocation_id').value = button.getAttribute('data-sublocation');
            document.getElementById('edit_warranty').value = button.getAttribute('data-warranty');
            document.getElementById('edit_remarks').value = button.getAttribute('data-remarks');

            $('#edit_assigned_to')
                .val(assignedTo && assignedTo !== '0' ? assignedTo : null)
                .trigger('change');

            const assignedValue = $('#edit_assigned_to').val();
            if (assignedValue && assignedValue !== '0') {
                $('#edit_assigned_to').prop('disabled', true);
                $('#edit_location_id').prop('disabled', true);
                $('#edit_sublocation_id').prop('disabled', true);

                $('#hidden_edit_assigned_to').val(assignedValue);
                $('#hidden_edit_location_id').val($('#edit_location_id').val());
                $('#hidden_edit_sublocation_id').val(button.getAttribute('data-sublocation'));
            } else {
                $('#edit_assigned_to').prop('disabled', false);
                $('#hidden_edit_assigned_to').val('');
            }

            const locationId = $(button).data('location');
            const sublocationId = $(button).data('sublocation');

            selectedEditSublocation = sublocationId;

            if (locationId) {
                $.ajax({
                    url: `/get-sublocations/${locationId}`,
                    type: 'GET',
                    success: function(data) {
                        $('#edit_sublocation_id').empty().append(
                            '<option value="" selected>Select sub-location</option>');

                        data.forEach(function(subloc) {
                            $('#edit_sublocation_id').append(
                                `<option value="${subloc.id}">${subloc.name}</option>`
                            );
                        });

                        if (sublocationId) {
                            $('#edit_sublocation_id').val(sublocationId);
                        }
                    },
                    error: function() {
                        $('#edit_sublocation_id').empty().append(
                            '<option value="" selected>Error loading sub-locations</option>');
                    }
                });
            }

            const form = document.getElementById('editForm');
            form.action = `/asset/${id}`;
        }

        document.getElementById('edit_assigned_to').addEventListener('change', function() {
            document.getElementById('hidden_edit_assigned_to').value = this.value;
        });

        document.getElementById('edit_location_id').addEventListener('change', function() {
            document.getElementById('hidden_edit_location_id').value = this.value;
        });

        document.getElementById('edit_sublocation_id').addEventListener('change', function() {
            document.getElementById('hidden_edit_sublocation_id').value = this.value;
        });

        document.getElementById('edit_purchase_date').addEventListener('focus', function() {
            const assetId = document.getElementById('edit_id').value;
            getAssetTransfers(assetId).then(() => {
                const transferCount = parseInt(document.getElementById('asset_transfer_count').value, 10);
                if (transferCount > 0) {
                    alert('Purchase date cannot be changed because this asset has been transferred.');
                    this.blur();
                    return;
                }
            });
        });

        function getAssetTransfers(assetId) {
            return fetch(`/asset/transfer/${assetId}/count`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('asset_transfer_count').value = data.count;
                })
                .catch(error => {
                    console.error('Error fetching asset transfers:', error);
                    return [];
                });
        }

        let selectedEditSublocation = null;

        $(document).on('change', '#location_id, #edit_location_id', function() {
            const locationId = $(this).val();
            const sublocationSelect = $($(this).data('target'));

            sublocationSelect.html('<option>Loading...</option>').prop('disabled', true);

            if (!locationId) {
                sublocationSelect.html('<option value="">Select sub-location</option>').prop('disabled', false);
                return;
            }

            $.ajax({
                url: `/get-sublocations/${locationId}`,
                type: 'GET',
                success: function(data) {
                    sublocationSelect.empty().append('<option value="">Select sub-location</option>');

                    $.each(data, function(_, sublocation) {
                        sublocationSelect.append(
                            `<option value="${sublocation.id}">${sublocation.name}</option>`
                        );
                    });

                    if (selectedEditSublocation && sublocationSelect.attr('id') ===
                        'edit_sublocation_id') {
                        sublocationSelect.val(selectedEditSublocation);
                        selectedEditSublocation = null;
                    }

                    sublocationSelect.prop('disabled', false);
                }
            });
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('asset-checkbox')) {
                updateBulkActions();
            }
        });

        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.asset-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkActions();
        });

        document.addEventListener('DOMContentLoaded', updateBulkActions);

        let selectedAssets = @json(session('selected_assets', []));

        function updateSelectedAssetsInput() {
            document.getElementById('selected_assets').value = selectedAssets.join(',');
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedAssetsInput();

            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.asset-checkbox');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    const id = cb.value;
                    if (this.checked) {
                        cb.checked = true;
                        if (!selectedAssets.includes(id)) {
                            selectedAssets.push(id);
                        }
                    } else {
                        cb.checked = false;
                        selectedAssets = selectedAssets.filter(x => x != id);
                    }
                });
                updateSelectedAssetsInput();
                saveSelectionToSession();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const id = this.value;
                    if (this.checked) {
                        if (!selectedAssets.includes(id)) {
                            selectedAssets.push(id);
                        }
                    } else {
                        selectedAssets = selectedAssets.filter(x => x != id);
                    }
                    updateSelectedAssetsInput();

                    const allChecked = [...checkboxes].every(c => c.checked);
                    selectAll.checked = allChecked;

                    saveSelectionToSession();
                });
            });

            if (checkboxes.length > 0) {
                const allChecked = [...checkboxes].every(c => c.checked);
                selectAll.checked = allChecked;
            }
        });

        function saveSelectionToSession() {
            const formData = new FormData();
            formData.append('selected_assets', selectedAssets.join(','));
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('asset.save-selection') }}', {
                method: 'POST',
                body: formData
            }).catch(error => console.error('Error saving selection:', error));
        }

        window.addEventListener('beforeunload', function() {
            saveSelectionToSession();
        });

        function clearSelection() {
            selectedAssets = [];
            updateSelectedAssetsInput();

            document.querySelectorAll('.asset-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('select-all').checked = false;

            fetch('{{ route('asset.clear-selection') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                location.reload();
            }).catch(error => console.error('Error clearing selection:', error));
        }

        const PRINT_BASE_URL = "{{ route('asset.labels') }}";
        const QR_BASE_URL = "{{ route('asset.qr-code') }}";
        const TRANSFER_BASE_URL = "{{ route('transfer.index') }}";

        function getSelectedAssetIds() {
            return Array.from(document.querySelectorAll('.asset-checkbox:checked')).map(cb => cb.value);
        }

        function updateBulkActions() {
            const ids = getSelectedAssetIds();
            const bulkActions = document.getElementById('bulk-actions');
            const countSpan = document.getElementById('selected-count');
            const barcodeLink = document.getElementById('print-barcode-link');
            const qrLink = document.getElementById('print-qr-link');
            const transferBtn = document.getElementById('transfer-selected-btn');

            if (ids.length > 0) {
                bulkActions.style.display = 'flex';
                countSpan.textContent = ids.length;

                barcodeLink.href = PRINT_BASE_URL + '?asset_ids=' + ids.join(',');
                qrLink.href = QR_BASE_URL + '?asset_ids=' + ids.join(',');
                if (transferBtn) transferBtn.href = TRANSFER_BASE_URL + '?asset_ids=' + ids.join(',');
            } else {
                bulkActions.style.display = 'none';
                barcodeLink.href = '#';
                qrLink.href = '#';
                if (transferBtn) transferBtn.href = '#';
            }
        }

        function togglePrintDropdown() {
            document.getElementById('print-dropdown').classList.toggle('hidden');
        }

        function closePrintDropdown() {
            document.getElementById('print-dropdown').classList.add('hidden');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('print-dropdown');
            const btn = document.getElementById('print-selected-btn');
            if (!dropdown || dropdown.classList.contains('hidden')) return;
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function retireAsset(button) {
            const assetId = button.getAttribute('data-id');
            const assetCode = button.getAttribute('data-code');

            if (confirm(`Are you sure you want to retire this asset ${assetCode}? This action cannot be undone.`)) {
                fetch(`/asset/${assetId}/retire`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message ?? 'Asset retired successfully.');
                            location.reload();
                        } else {
                            alert(data.message ?? 'Unable to retire asset.');
                        }
                    })
                    .catch(error => {
                        console.error('Error retiring asset:', error);
                        alert('An error occurred while retiring the asset.');
                    });
            }
        }
    </script>
@endsection
