@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">License & Permit Management</h1>
                <p class="text-sm text-gray-500">
                    Track and monitor asset licenses and permits
                </p>
            </div>
            <div class="flex items-center gap-2 mt-0">
                <a href=""
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Setup Alerts
                </a>

                <button data-modal-target="add-modal" data-modal-toggle="add-modal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add License
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Total Licenses',
                    'value' => $totalLicenses,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" class = "w-5 h-5 text-blue-600" width="16" height="16" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                        <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
  <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                        </svg>',
                ],
                [
                    'title' => 'Active',
                    'value' => $activeLicenses,
                    'color' => 'green',
                    'icon' => '
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
                        </svg>',
                ],
                [
                    'title' => 'Expiring Soon',
                    'value' => $expiringSoonLicenses,
                    'color' => 'yellow',
                    'icon' => '
                        <svg class="w-5 h-5 text-yellow-600" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">                            
                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg>',
                ],
                [
                    'title' => 'Expired',
                    'value' => $expiredLicenses,
                    'color' => 'red',
                    'icon' => '
                        <svg class="w-5 h-5 text-red-600" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($cards as $card)
                <div class="bg-white border rounded-xl p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-lg bg-{{ $card['color'] }}-100 
                                flex items-center justify-center">
                        {!! $card['icon'] !!}
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ $card['title'] }}</p>
                        <p class="text-xl font-semibold text-gray-900">
                            {{ $card['value'] }}
                        </p>
                    </div>
                </div>
            @endforeach
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
                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Expired</option>
                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Expiring Soon</option>
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
                        <th scope="col" class="px-4 py-3 text-left w-[110px]">Asset ID</th>
                        <th scope="col" class="px-4 py-3 text-left w-[200px]">Name</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Type</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Number</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Issuing Authority</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Expiration Date</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Alert</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($assetLicenses as $assetLicense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 w-[110px]">{{ $assetLicense->asset->asset_code }}</td>
                            <td class="px-4 py-3 w-[200px]">{{ $assetLicense->asset->name }}</td>
                            <td class="px-4 py-3 w-[100px]">{{ $assetLicense->license_type->name }}</td>
                            <td class="px-4 py-3 w-[100px]">{{ $assetLicense->license_number }}</td>
                            <td class="px-4 py-3 w-[150px]">{{ $assetLicense->issuing_authority }}</td>
                            <td class="px-4 py-3 w-[100px]">
                                {{ $assetLicense->expiration_date ? Carbon::parse($assetLicense->expiration_date)->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 w-[100px] text-xs font-semibold">
                                @php
                                    $statuses = [
                                        0 => [
                                            'color' => 'bg-red-100 text-red-600',
                                            'label' => 'Expired',
                                        ],
                                        1 => [
                                            'color' => 'bg-green-100 text-green-700',
                                            'label' => 'Active',
                                        ],
                                        2 => [
                                            'color' => 'bg-yellow-100 text-yellow-600',
                                            'label' => 'Expiring Soon',
                                        ],
                                    ];
                                    $status = $statuses[$assetLicense->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-3 text-{{ $assetLicense->status_label['color'] }}-700 text-xs flex items-center gap-1 w-[200px]">
                                {!! $assetLicense->status_label['icon'] !!}
                                {{ $assetLicense->status_label['label'] }}</td>
                            <td class="px-4 py-3 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
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
                                        class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path
                                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd"
                                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                        {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No asset licenses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $assetLicenses->links() }}
        </div>
    </div>

    <!-- Create license modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Add New License
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
                    <form action="{{ route('licenses.store') }}" method="POST">
                        @csrf
                        <div class="grid ml-1 mr-1 gap-2 mb-4 sm:grid-cols-1">
                            <div class="w-full md:col-span-2">
                                <label for="asset_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset Name*</label>
                                <select name="asset_id" id="asset_id"
                                    class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" selected>Select asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->name }} -
                                            {{ $asset->asset_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="license_type_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">License Type*</label>
                                <select id="license_type_id" name="license_type_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" selected>Select type</option>
                                    @foreach ($licenseTypes as $licenseType)
                                        <option value="{{ $licenseType->id }}">{{ $licenseType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="license_number"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">License Number*</label>
                                <input type="text" name="license_number" id="license_number"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="License/ permit number" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="issuing_authority"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Issuing
                                    Authority*</label>
                                <input type="text" name="issuing_authority" id="issuing_authority"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Authority or department name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="issue_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date Issued</label>
                                <input type="date" name="issue_date" id="issue_date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="mm/dd/yyyy">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="expiration_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date Expired</label>
                                <input type="date" name="expiration_date" id="expiration_date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="mm/dd/yyyy" required>
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
                            Add License
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create license type modal -->

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
                        Update License Type
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

                        <div class="grid ml-1 mr-1 gap-2 mb-4 sm:grid-cols-1">
                            <div class="w-full md:col-span-2">
                                <label for="edit_asset_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset Name*</label>
                                <select name="edit_asset_id" id="edit_asset_id"
                                    class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="{{ old('edit_asset_id') }}" selected>Select asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}"
                                            {{ old('edit_asset_id', $assetLicense->asset_id ?? '') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->name }} -
                                            {{ $asset->asset_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="edit_license_type_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">License Type*</label>
                                <select id="edit_license_type_id" name="edit_license_type_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" selected>Select type</option>
                                    @foreach ($licenseTypes as $licenseType)
                                        <option value="{{ $licenseType->id }}"
                                            {{ old('edit_license_type_id', $assetLicense->license_type_id ?? '') == $licenseType->id ? 'selected' : '' }}>
                                            {{ $licenseType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_license_number"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">License Number*</label>
                                <input type="text" name="edit_license_number" id="edit_license_number"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="License/ permit number" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_issuing_authority"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Issuing
                                    Authority*</label>
                                <input type="text" name="edit_issuing_authority" id="edit_issuing_authority"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Authority or department name" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_issue_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date Issued</label>
                                <input type="date" name="edit_issue_date" id="edit_issue_date"
                                    value="{{ old('edit_issue_date') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_expiration_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date Expired</label>
                                <input type="date" name="edit_expiration_date" id="edit_expiration_date"
                                    value="{{ old('edit_expiration_date') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-2 text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            {{-- <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg> --}}
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
                width: '100%'
            });
            $('#edit_asset_id').select2({
                placeholder: "Select asset",
                allowClear: true,
                width: '100%'
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
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_asset_id').value = button.getAttribute('data-asset_id');
            document.getElementById('edit_license_type_id').value = button.getAttribute('data-license_type_id');
            document.getElementById('edit_license_number').value = button.getAttribute('data-license_number');
            document.getElementById('edit_issuing_authority').value = button.getAttribute('data-issuing_authority');
            document.getElementById('edit_issue_date').value = button.getAttribute('data-issue_date') ?? null;
            document.getElementById('edit_expiration_date').value = button.getAttribute('data-expiration_date') ?? null;


            const form = document.getElementById('editForm');
            form.action = `/licenses/${id}`;

        }
    </script>
@endsection
