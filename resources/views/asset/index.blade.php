@extends('dashboard')
@section('content')

    <style>
        .custom-checkbox {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 1px solid #9ca3af;
            /* gray-400 */
            background-color: #e5e7eb;
            /* gray-200 */
            border-radius: 3px;
            cursor: pointer;
        }

        .custom-checkbox:checked {
            background-color: #6b7280;
            /* gray-500 */
        }
    </style>

    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Asset Management</h1>
                <p class="text-sm text-gray-500">
                    Manage and track agency assets and property
                </p>
            </div>

            <div class="flex items-center gap-2 mt-0">
                <div id="bulk-actions" class="flex items-center gap-2"
                    style="{{ count(session('selected_assets', [])) > 0 ? '' : 'display:none;' }}">

                    <a id="print-selected-btn" href="#" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-upc-scan" viewBox="0 0 16 16">
                            <path
                                d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                        </svg>
                        Print Selected (<span id="selected-count">0</span>)
                    </a>

                    <button onclick="clearSelection()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-eraser" viewBox="0 0 16 16">
                            <path
                                d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z" />
                        </svg>
                        Clear Selection
                    </button>
                </div>


                {{-- <a href="{{ route('setup.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-upc-scan" viewBox="0 0 16 16">
                        <path
                            d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                    </svg>
                    Bulk UPC Generate
                </a> --}}

                <button data-modal-target="add-modal" data-modal-toggle="add-modal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Asset
                </button>
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
            <input type="hidden" name="selected_assets" id="selected_assets"
                value="{{ implode(',', session('selected_assets', [])) }}">

            <div class="flex flex-col md:flex-row gap-2 text-xs md:text-sm">
                <div class="md:w-2/3 w-full">
                    <input type="text" id="simple-search" name="search" placeholder="Search by name, code, or serial..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        value = "{{ request()->query('search') }}" oninput="this.form.submit()">
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="searchcat" name="searchcat"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('searchcat') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="searchloc" name="searchloc"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Locations</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ request('searchloc') == $location->id ? 'selected' : '' }}>
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
                        <th scope="col" class="px-4 py-3 text-center w-[40px]">
                            <input type="checkbox" id="select-all" class="form-checkbox custom-checkbox">
                        </th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Code</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Name</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Category</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Serial</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Assigned To</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">License Status</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-150">
                            <td class="px-4 py-2 text-center">
                                <input type="checkbox" class="asset-checkbox custom-checkbox"
                                    value="{{ $asset->id }}" @if (in_array($asset->id, session('selected_assets', []))) checked @endif>
                            </td>
                            <td class="px-4 py-2font-medium w-[80px]">{{ $asset->asset_code }}</td>
                            <td class="px-4 py-2 w-[150px]">{{ $asset->name }} <br>
                                <span class="text-gray-400 text-xs">{{ $asset->subcategory }}</span>
                            </td>
                            <td class="px-4 py-2 w-[80px]">{{ $asset->category_id ? $asset->category->name : '' }}</td>
                            <td class="px-4 py-2 w-[100px]">{{ $asset->serial }}</td>
                            <td class="px-4 py-2 w-[80px] text-xs">
                                @php
                                    $statuses = [
                                        0 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Inactive'],
                                        1 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Active'],
                                        2 => ['color' => 'bg-yellow-100 text-yellow-700', 'label' => 'On Leave'],
                                    ];
                                    $status = $statuses[$asset->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2 w-[100px]">
                                {{ $asset->assigned_to ? $asset->assigned_user->last_name . ', ' . $asset->assigned_user->first_name . ' ' . $asset->assigned_user->middle_name : '' }}
                            </td>
                            <td class="px-4 py-2 w-[100px]">
                                {{ $asset->location_id ? $asset->location->name : '' }}</td>
                            <td class="px-4 py-2 w-[80px]"></td>
                            <td class="px-4 py-2 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
                                    <button type="button" title="Edit asset: {{ $asset->asset_code }}"
                                        data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                        data-id="{{ $asset->id }}" data-code="{{ $asset->asset_code }}"
                                        data-name="{{ $asset->name }}" data-description="{{ $asset->description }}"
                                        data-category="{{ $asset->category_id }}"
                                        data-subcategory="{{ $asset->subcategory }}" data-serial="{{ $asset->serial }}"
                                        data-cost="{{ $asset->cost }}" data-status="{{ $asset->status }}"
                                        data-purchase_date="{{ $asset->purchase_date }}"
                                        data-manufacturer="{{ $asset->manufacturer }}" data-model="{{ $asset->model }}"
                                        data-serial="{{ $asset->serial }}" data-assigned_to="{{ $asset->assigned_to }}"
                                        data-location="{{ $asset->location_id }}"
                                        data-sublocation="{{ $asset->subloc_id }}"
                                        data-warranty="{{ $asset->warranty }}" onclick="openEditModal(this)"
                                        class="group flex items-center space-x-1 text-gray-500 hover:text-blue-600 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path
                                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd"
                                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </button>

                                    <a href="{{ route('transfer.show', $asset->id) }}"
                                        title="Transfer asset: {{ $asset->asset_code }}"
                                        class="group flex items-center space-x-1 text-gray-500 hover:text-green-600 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-folder-symlink" viewBox="0 0 16 16">
                                            <path
                                                d="m11.798 8.271-3.182 1.97c-.27.166-.616-.036-.616-.372V9.1s-2.571-.3-4 2.4c.571-4.8 3.143-4.8 4-4.8v-.769c0-.336.346-.538.616-.371l3.182 1.969c.27.166.27.576 0 .742" />
                                            <path
                                                d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m.694 2.09A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09l-.636 7a1 1 0 0 1-.996.91H2.826a1 1 0 0 1-.995-.91zM6.172 2a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('asset.labels', ['asset_ids' => $asset->id]) }}"
                                        title="Generate barcode: {{ $asset->asset_code }}" target="_blank"
                                        class="group flex items-center space-x-1 text-gray-500 hover:text-red-600 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-upc-scan" viewBox="0 0 16 16">
                                            <path
                                                d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                                        </svg>
                                    </a>

                                    <a href="" title="Generate QR: {{ $asset->asset_code }}"
                                        class="group flex items-center space-x-1 text-gray-500 hover:text-yellow-600 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16">
                                            <path
                                                d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z" />
                                            <path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z" />
                                            <path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z" />
                                            <path
                                                d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z" />
                                            <path d="M12 9h2V8h-2z" />
                                        </svg>
                                    </a>
                                    {{-- <button type="button"
                                        title="View assigned assets to {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                        data-modal-target="view-asset-modal" data-modal-toggle="view-asset-modal"
                                        data-id="{{ $employee->id }}" data-code="{{ $employee->employee_code }}"
                                        data-department="{{ $employee->location->description ?? '' }}"
                                        data-status="{{ $employee->status }}" onclick="openEditModal(this)"
                                        class="group flex space-x-1 text-gray-500 hover:text-green-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
                                            <path
                                                d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                            <path
                                                d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                        </svg>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No assets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $assets->links() }}
        </div>
    </div>


    <!-- Create asset modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Add New Asset
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
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
                <div class="overflow-y-auto max-h-[70vh]">
                    <form action="{{ route('asset.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-2 mb-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <label for="name"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset
                                    Name*</label>
                                <input type="text" name="name" id="name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Enter asset name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="cost"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset
                                    Cost*</label>
                                <input type="number" name="cost" id="cost"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. 0.00" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="category_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Category*</label>
                                <select id="category_id" name="category_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value = "" selected disabled>Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="subcategory"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Sub-category</label>
                                <input type="text" name="subcategory" id="subcategory"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Handgun, Patrol Vehicle">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="location_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Location</label>
                                <select id="location_id" name="location_id" data-target="#sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value = "" selected disabled>Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="sublocation_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Sub-location</label>
                                <select id="sublocation_id" name="sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value = "" selected disabled>Select sub-location</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="manufacturer"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Manufacturer</label>
                                <input type="text" name="manufacturer" id="manufacturer"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Brand/ Manufacturer">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="purchase_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date
                                    Purchase*</label>
                                <input type="date" name="purchase_date" id="purchase_date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. mm/dd/yyyy" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="model"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Model</label>
                                <input type="text" name="model" id="model"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Model number/ name">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="serial"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Serial</label>
                                <input type="text" name="serial" id="serial"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Serial number">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="assigned_to"
                                    class="select2 block text-xs font-medium text-gray-900 dark:text-white">Assigned
                                    To</label>
                                <select id="assigned_to" name="assigned_to"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value = "0" selected disabled>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->last_name }},
                                            {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="description"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea type="text" name="description" id="description" rows="2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Additional details about the asset"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="warranty"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Warranty</label>
                                <textarea type="text" name="warranty" id="warranty" rows="1"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Warranty details/ expiration"></textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <svg class="mr-1 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
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

    <!-- Modal  Edit-->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Update Asset
                        @if (isset($asset) && $asset->asset_code)
                            {{ '(' . $asset->asset_code . ')' }}
                        @endif
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="edit-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="overflow-y-auto max-h-[70vh]">
                    <form id="editForm" class="p-4 md:p-5" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="edit_id" id="edit_id">

                        <div class="grid gap-2 mb-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <label for="edit_name"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset
                                    Name*</label>
                                <input type="text" name="edit_name" id="edit_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Enter asset name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_cost"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset
                                    Cost*</label>
                                <input type="number" name="edit_cost" id="edit_cost"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. 0.00" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_category_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Category*</label>
                                <select id="edit_category_id" name="edit_category_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value = "" selected disabled>Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_subcategory"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Sub-category</label>
                                <input type="text" name="edit_subcategory" id="edit_subcategory"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Handgun, Patrol Vehicle">
                            </div>

                            <div class="sm:col-span-1">
                                <input type="hidden" name="hidden_edit_location_id" id="hidden_edit_location_id">
                                <label for="edit_location_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Location</label>
                                <select id="edit_location_id" name="location_id" data-target="#edit_sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value = "" selected>Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <input type="hidden" name="hidden_edit_sublocation_id" id="hidden_edit_sublocation_id">
                                <label for="edit_sublocation_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Sub-location</label>
                                <select id="edit_sublocation_id" name="sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value = "" selected>Select sub-location</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_manufacturer"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Manufacturer</label>
                                <input type="text" name="edit_manufacturer" id="edit_manufacturer"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Brand/ Manufacturer">
                            </div>

                            <div class="sm:col-span-1">
                                <input type="hidden" name="asset_transfer_count" id="asset_transfer_count"
                                    value =''>
                                <label for="edit_purchase_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date
                                    Purchase*</label>
                                <input type="date" name="edit_purchase_date" id="edit_purchase_date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. mm/dd/yyyy" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_model"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Model</label>
                                <input type="text" name="edit_model" id="edit_model"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Model number/ name">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_serial"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Serial</label>
                                <input type="text" name="edit_serial" id="edit_serial"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Serial number">
                            </div>

                            <div class="sm:col-span-2">
                                <input type="hidden" name="hidden_edit_assigned_to" id="hidden_edit_assigned_to">
                                <label for="edit_assigned_to"
                                    class="select2 block text-xs font-medium text-gray-900 dark:text-white">Assigned
                                    To</label>
                                <select id="edit_assigned_to" name="edit_assigned_to"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value = "0" selected disabled>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->last_name }},
                                            {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_description"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea type="text" name="edit_description" id="edit_description" rows="2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Additional details about the asset"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_warranty"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Warranty</label>
                                <textarea type="text" name="edit_warranty" id="edit_warranty" rows="1"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Warranty details/ expiration"></textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-2 text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            {{-- <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg> --}}
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
            document.getElementById('edit_assigned_to').value = button.getAttribute('data-assigned_to');
            document.getElementById('edit_sublocation_id').value = button.getAttribute('data-sublocation');
            document.getElementById('edit_warranty').value = button.getAttribute('data-warranty');

            if (document.getElementById('edit_assigned_to').value !== '0' &&
                document.getElementById('edit_assigned_to').value !== '') {
                document.getElementById('edit_assigned_to').disabled = true;
                document.getElementById('edit_location_id').disabled = true;
                document.getElementById('edit_sublocation_id').disabled = true;
                document.getElementById('hidden_edit_assigned_to').value = document.getElementById('edit_assigned_to')
                    .value;
                document.getElementById('hidden_edit_location_id').value = document.getElementById('edit_location_id')
                    .value;
                document.getElementById('hidden_edit_sublocation_id').value = button.getAttribute('data-sublocation');
            } else {
                document.getElementById('edit_assigned_to').disabled = false;
                document.getElementById('hidden_edit_assigned_to').value = '';
            }

            const form = document.getElementById('editForm');
            form.action = `/asset/${id}`;

            const locationId = $(button).data('location');
            const sublocationId = $(button).data('sublocation');

            selectedEditSublocation = sublocationId;

            if (locationId) {
                $.ajax({
                    url: `/get-sublocations/${locationId}`,
                    type: 'GET',
                    success: function(data) {
                        // clear existing options
                        $('#edit_sublocation_id').empty().append(
                            '<option value="" selected>Select sub-location</option>');

                        data.forEach(function(subloc) {
                            $('#edit_sublocation_id').append(
                                `<option value="${subloc.id}">${subloc.name}</option>`
                            );
                        });

                        // select the correct sub-location
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

        //disable purchase date if transferred has been made
        document.getElementById('edit_purchase_date').addEventListener('focus', function() {
            const assetId = document.getElementById('edit_id').value;
            getAssetTransfers(assetId).then(() => {
                const transferCount = parseInt(document.getElementById('asset_transfer_count').value, 10);
                if (transferCount > 0) {
                    alert('Purchase date cannot be changed because this asset has been transferred.');
                    this.blur();
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

        // ADD + EDIT location change handler
        $(document).on('change', '#location_id, #edit_location_id', function() {

            const locationId = $(this).val();
            const sublocationSelect = $($(this).data('target'));

            // alert('Location Id: ' + locationId);
            // alert('Sublocation Select Id: ' + sublocationSelect.attr('id'));

            sublocationSelect
                .html('<option>Loading...</option>')
                .prop('disabled', true);

            if (!locationId) {
                sublocationSelect.html('<option value="">Select sub-location</option>');
                return;
            }

            $.ajax({
                url: `/get-sublocations/${locationId}`,
                type: 'GET',
                success: function(data) {
                    sublocationSelect.empty()
                        .append('<option value="">Select sub-location</option>');

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

        //load sub-location when edit modal is shown
        $('#editModal').on('shown.bs.modal', function() {
            $('#edit_location_id').trigger('change');
        });

        // Individual checkbox
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('asset-checkbox')) {
                updateBulkActions();
            }
        });

        // Select all
        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.asset-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkActions();
        });

        // Run on page load (important for search / pagination)
        document.addEventListener('DOMContentLoaded', updateBulkActions);

        // Load selected assets from session or hidden input
        let selectedAssets = @json(session('selected_assets', []));

        // Update hidden input with current selections
        function updateSelectedAssetsInput() {
            document.getElementById('selected_assets').value = selectedAssets.join(',');
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial hidden input value
            updateSelectedAssetsInput();

            // Check all functionality
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

                // Submit form to save selection to session
                saveSelectionToSession();
            });

            // Individual checkbox change
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

                    // Update "Select All" checkbox
                    const allChecked = [...checkboxes].every(c => c.checked);
                    selectAll.checked = allChecked;

                    // Save selection to session
                    saveSelectionToSession();
                });
            });

            // Initialize "Select All" checkbox state
            if (checkboxes.length > 0) {
                const allChecked = [...checkboxes].every(c => c.checked);
                selectAll.checked = allChecked;
            }
        });

        // Function to save selection to session via AJAX
        function saveSelectionToSession() {
            const formData = new FormData();
            formData.append('selected_assets', selectedAssets.join(','));
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('asset.save-selection') }}', {
                method: 'POST',
                body: formData
            }).catch(error => console.error('Error saving selection:', error));
        }

        // Save selection when page unloads (optional)
        window.addEventListener('beforeunload', function() {
            saveSelectionToSession();
        });

        // Function to clear selection
        function clearSelection() {
            selectedAssets = [];
            updateSelectedAssetsInput();

            // Uncheck all checkboxes
            document.querySelectorAll('.asset-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('select-all').checked = false;

            // Clear from session via AJAX
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

        //update print selected button link
        const PRINT_BASE_URL = "{{ route('asset.labels') }}";

        function getSelectedAssetIds() {
            return Array.from(document.querySelectorAll('.asset-checkbox:checked'))
                .map(cb => cb.value);
        }

        function updateBulkActions() {
            const ids = getSelectedAssetIds();
            const bulkActions = document.getElementById('bulk-actions');
            const printBtn = document.getElementById('print-selected-btn');
            const countSpan = document.getElementById('selected-count');

            if (ids.length > 0) {
                bulkActions.style.display = 'flex';
                countSpan.textContent = ids.length;

                // Live update URL
                printBtn.href = PRINT_BASE_URL + '?asset_ids=' + ids.join(',');
            } else {
                bulkActions.style.display = 'none';
                printBtn.href = '#';
            }
        }

        // Checkbox change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('asset-checkbox')) {
                updateBulkActions();
            }
        });

        // Select all
        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.asset-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkActions();
        });

        // Initial load (important for search / pagination)
        document.addEventListener('DOMContentLoaded', updateBulkActions);
    </script>
@endsection
