@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Employee Management</h1>
                <p class="text-sm text-gray-500">
                    Manage employees and their details
                </p>
            </div>

            <a href="{{ route('employee.create') }}">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Employee
                </button>
            </a>

        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Total Employees',
                    'value' => $totalEmployees,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" class = "w-5 h-5 text-blue-600" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                        </svg>',
                ],
                [
                    'title' => 'Active',
                    'value' => $activeEmployees,
                    'color' => 'green',
                    'icon' => '
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
                        </svg>',
                ],
                [
                    'title' => 'On Leave',
                    'value' => $onleaveEmployees,
                    'color' => 'yellow',
                    'icon' => '
                        <svg class="w-5 h-5 text-yellow-600" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M11 12h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1m0-7a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                        </svg>',
                ],
                [
                    'title' => 'Inactive',
                    'value' => $inactiveEmployees,
                    'color' => 'red',
                    'icon' => '
                        <svg class="w-5 h-5 text-red-600" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m-.646-4.854.646.647.646-.647a.5.5 0 0 1 .708.708l-.647.646.647.646a.5.5 0 0 1-.708.708l-.646-.647-.646.647a.5.5 0 0 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 .708-.708" />
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
                    <input type="text" id="simple-search" name="search"
                        placeholder="Search by name, code, or position..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        value = "{{ request()->query('search') }}" oninput="this.form.submit()">
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

                <div class="md:w-1/3 w-full">
                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>On Leave</option>
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
                        <th scope="col" class="px-4 py-3 text-left w-[50px]">Code</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Name</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Position</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Email</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Phone</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium w-[50px]">{{ $employee->employee_code }}</td>
                            <td class="px-4 py-3 w-[150px]">{{ $employee->last_name }}, {{ $employee->first_name }}
                                {{ $employee->middle_name }}</td>
                            <td class="px-4 py-3 w-[150px]">{{ $employee->position }}</td>
                            <td class="px-4 py-3 w-[100px]">{{ $employee->location ? $employee->location->name : '' }}
                            </td>
                            <td class="px-4 py-3 w-[100px]">{{ $employee->email }}</td>
                            <td class="px-4 py-3 w-[100px]">{{ $employee->mobile }}</td>
                            <td class="px-4 py-3 w-[80px] text-xs font-semibold">
                                @php
                                    $statuses = [
                                        0 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Inactive'],
                                        1 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Active'],
                                        2 => ['color' => 'bg-yellow-100 text-yellow-700', 'label' => 'On Leave'],
                                    ];
                                    $status = $statuses[$employee->status] ?? [
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
                                    <a href="{{ route('employee.edit', $employee->id) }}"
                                        title="Edit employee {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                        class="group flex items-center space-x-1 text-gray-500 hover:text-blue-600 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path
                                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd"
                                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </a>

                                    <button type="button"
                                        title="View assigned assets to {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                        data-modal-target="view-asset-modal" data-modal-toggle="view-asset-modal"
                                        data-name="{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                        data-id="{{ $employee->id }}" onclick="openAssetModal(this)"
                                        class="group flex space-x-1 text-gray-500 hover:text-green-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
                                            <path
                                                d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                            <path
                                                d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                        </svg>
                                        {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                    </button>

                                    <button type="button"
                                        title="Upload file to {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                        data-modal-target="upload-file-modal" data-modal-toggle="upload-file-modal"
                                        data-name="{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                        data-id="{{ $employee->id }}" onclick="loadUploadModal(this)"
                                        class="group flex space-x-1 text-gray-500 hover:text-indigo-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-cloud-arrow-up" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708z" />
                                            <path
                                                d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z" />
                                        </svg>
                                        {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No employee found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- View asset modal -->
    <div id="view-asset-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Asset Accountability
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="view-asset-modal">
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
                    <form action="" method="put">
                        @csrf
                        <input type="hidden" name="empId" id="empId">
                        <div name="employee_name" id="employee_name" class="mb-2 text-md font-semibold"></div>
                        <div class="overflow-y-auto max-h-[70vh]">
                            <table class="min-w-full text-xs border rounded-xl">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-2 py-2 text-left">Asset Code</th>
                                        <th class="px-2 py-2 text-left">Name</th>
                                        <th class="px-2 py-2 text-left">Category</th>
                                        <th class="px-2 py-2 text-left">Location</th>
                                        {{-- <th class="px-2 py-2 text-left">Status</th> --}}
                                        <th class="px-2 py-2 text-left">License Validity</th>
                                    </tr>
                                </thead>
                                <tbody id="assetTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-gray-500">
                                            Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <br />
                        <a href="#" title="Print Acknowledgement Receipt for Equipement" onclick="printARE()"
                            class="text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="mr-2 bi bi-printer" viewBox="0 0 16 16">
                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                <path
                                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                            </svg>
                            ARE
                        </a>

                        <a href="#" title="Print Duty Detail" onclick="printDutyDetail()"
                            class="text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="mr-2 bi bi-printer" viewBox="0 0 16 16">
                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                <path
                                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                            </svg>
                            Duty Detail
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End view assest modal -->

    <!-- Upload file modal -->
    <div id="upload-file-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-4xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Upload File
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="upload-file-modal">
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
                    <form id="uploadFileForm" class="p-4 md:p-2" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="upload_emp_id" id="upload_emp_id">

                        <!-- MAIN 2 COLUMN LAYOUT -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            <!-- ================= LEFT SIDE (HEADER INFO) ================= -->
                            <div>
                                <h3 class="text-sm font-semibold mb-3">File Information</h3>

                                <!-- HEADER 2 COLUMN GRID -->
                                <div class="grid md:grid-cols-2 sm:grid-cols-2 gap-3">
                                    <div class="w-full sm:col-span-2">
                                        <input name="upload_employee_name" id="upload_employee_name"
                                            class="text-md font-semibold bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"></input>
                                    </div>
                                    <div class="w-full sm:col-span-2">
                                        <label class="block text-xs font-medium">Note</label>
                                        <textarea id="note" name="note" rows="3"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"></textarea>
                                    </div>

                                    <!-- File Upload (PDF, Excel, etc.) -->
                                    <div class="sm:col-span-2">
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload
                                            Files</label>
                                        <input type="file" name="files[]" multiple
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip,.rar"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= RIGHT SIDE (DETAILS TABLE) ================= -->
                            <div>
                                <div class="border rounded-lg p-3">

                                    <h3 class="text-sm font-semibold mb-2">Uploaded Files</h3>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs text-left border">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-3 py-2 border">Date</th>
                                                    <th class="px-3 py-2 border">File Name</th>
                                                    <th class="px-3 py-2 border">File</th>
                                                </tr>
                                            </thead>
                                            <tbody id="viewDetailsTable">

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="mt-6 flex full-width justify-end space-x-2">
                            <button type="button" id="saveButton" data-modal-toggle="upload-file-modal"
                                onclick="submitUploadFileForm()"
                                class="text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                Save
                            </button>

                            <button type="button" id="closeButton" data-modal-toggle="upload-file-modal"
                                class="px-4 py-2 text-xs border rounded-lg bg-gray-100 hover:bg-gray-200">
                                Close
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            <!-- End upload modal -->

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

                function openAssetModal(button) {
                    const id = button.getAttribute('data-id');
                    document.getElementById('empId').value = id
                    document.getElementById('employee_name').innerText = button.getAttribute('data-name');
                    const statusMap = {
                        1: 'Available',
                        2: 'Active',
                        3: 'Assigned',
                        4: 'Maintenance',
                        5: 'Retired'
                    };

                    fetch(`/employee/${id}/accountability`)
                        .then(response => response.json())
                        .then(data => {

                            const tbody = document.getElementById('assetTableBody');
                            tbody.innerHTML = '';

                            if (data.assets.length === 0) {
                                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            No assigned assets found.
                        </td>
                    </tr>
                `;
                                return;
                            }

                            data.assets.forEach(asset => {

                                const statusLabel = statusMap[asset.status] || 'Available'

                                let expirationDate = asset.licenses ?
                                    asset.licenses.map(l => new Date(l.expiration_date).toLocaleDateString())
                                    .join('<br>') :
                                    '';

                                tbody.innerHTML += `
                    <tr class="border-b">
                        <td class="px-2 py-2">${asset.asset_code ?? ''}</td>
                        <td class="px-2 py-2">${asset.name ?? ''}</td>
                        <td class="px-2 py-2">${asset.category ? asset.category.name : ''}</td>
                        <td class="px-2 py-2">${asset.location ? asset.location.name : ''}</td>                        
                        <td class="px-2 py-2">${expirationDate}</td>
                    </tr>
                `;
                            });
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }

                function printARE() {
                    const empId = document.getElementById('empId').value
                    if (!empId) {
                        alert('Employee must be selected.');
                        return;
                    }

                    let url = "{{ route('are.print') }}" + "?empId=" + empId;
                    window.open(url, '_blank');
                }

                function printDutyDetail() {
                    const empId = document.getElementById('empId').value
                    if (!empId) {
                        alert('Employee must be selected.');
                        return;
                    }

                    let url = "{{ route('duty.detail.print') }}" + "?empId=" + empId;
                    window.open(url, '_blank');
                }

                function loadUploadModal(button) {
                    const id = button.getAttribute('data-id');
                    document.getElementById('upload_emp_id').value = id;
                    document.getElementById('upload_employee_name').value = button.getAttribute('data-name');
                    const empName = button.getAttribute('data-name');

                    // make sure the elements exist
                    const empIdInput = document.getElementById('upload_emp_id');
                    const empNameInput = document.getElementById('upload_employee_name');

                    if (empIdInput && empNameInput) {
                        empIdInput.value = id; // hidden input
                        empNameInput.value = empName; // visible input
                    }

                    loadUploadedFiles(id);
                }


                function submitUploadFileForm() {
                    const empId = document.getElementById('upload_emp_id').value;
                    const form = document.getElementById('uploadFileForm');
                    const formData = new FormData(form);

                    fetch(`/employee/${empId}/upload`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Files uploaded successfully!');
                                form.reset();
                                // Optionally, refresh the uploaded files table here
                                loadUploadedFiles(empId);
                            } else {
                                alert('Error uploading files: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while uploading files.');
                        });
                }

                function loadUploadedFiles(empId) {
                    fetch(`/employee/${empId}/uploaded-files`)
                        .then(response => response.json())
                        .then(data => {
                            const tableBody = document.getElementById('viewDetailsTable');
                            tableBody.innerHTML = '';

                            if (data.files.length === 0) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">
                                            No files uploaded.
                                        </td>
                                    </tr>
                                `;
                                return;
                            }

                            data.files.forEach(file => {
                                const uploadDate = new Date(file.created_at).toLocaleDateString();
                                tableBody.innerHTML += `
                                    <tr class="border-b">
                                        <td class="px-3 py-2">${uploadDate}</td>
                                        <td class="px-3 py-2">${file.file_name}</td>
                                        <td class="px-3 py-2">
                                            <a href="/storage/${file.path}" target="_blank" class="text-blue-600 hover:underline">
                                                View File
                                            </a>
                                        </td>
                                    </tr>
                                `;
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while loading uploaded files.');
                        });
                }
            </script>
        @endsection
