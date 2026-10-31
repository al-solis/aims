@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Auth;
    @endphp
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>Employee Management</h1>
                        <p>Manage employees and their details</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($canCreate)
                            <a href="{{ route('employee.create') }}" class="ft-btn ft-btn-primary"
                                style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Employee
                            </a>
                        @else
                            <button disabled class="ft-btn ft-btn-primary cursor-not-allowed opacity-50"
                                style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Employee
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Total Employees',
                            'value' => $totalEmployees,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent);" fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>',
                        ],
                        [
                            'title' => 'Active',
                            'value' => $activeEmployees,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>',
                        ],
                        [
                            'title' => 'On Leave/ Floating',
                            'value' => $onleaveEmployees,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-warning-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M11 12h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1m0-7a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/></svg>',
                        ],
                        [
                            'title' => 'Inactive',
                            'value' => $inactiveEmployees,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-danger-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/><path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m-.646-4.854.646.647.646-.647a.5.5 0 0 1 .708.708l-.647.646.647.646a.5.5 0 0 1-.708.708l-.646-.647-.646.647a.5.5 0 0 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 .708-.708" /></svg>',
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
                    <div class="md:w-1/3 w-full">
                        <input type="text" id="simple-search" name="search"
                            placeholder="Search by name, code, or position..." class="ft-field"
                            value="{{ request()->query('search') }}">
                    </div>
                    <div class="md:w-2/3 w-full flex flex-col md:flex-row gap-3">
                        <select name="searchloc" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ request('searchloc') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="searchdoc" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Document Types</option>
                            @foreach ($docTypes as $docType)
                                <option value="{{ $docType->id }}"
                                    {{ request('searchdoc') == $docType->id ? 'selected' : '' }}>
                                    {{ $docType->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>On Leave</option>
                            <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Resigned</option>
                            <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Retired</option>
                            <option value="5" {{ request('status') === '5' ? 'selected' : '' }}>Terminated</option>
                            <option value="6" {{ request('status') === '6' ? 'selected' : '' }}>AWOL</option>
                            <option value="7" {{ request('status') === '7' ? 'selected' : '' }}>Deceased</option>
                            <option value="8" {{ request('status') === '8' ? 'selected' : '' }}>Dropped</option>
                            <option value="9" {{ request('status') === '9' ? 'selected' : '' }}>Labor</option>
                            <option value="10" {{ request('status') === '10' ? 'selected' : '' }}>Floating</option>
                        </select>
                        <select name="searchtype" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="1" {{ request('searchtype') === '1' ? 'selected' : '' }}>Regular</option>
                            <option value="2" {{ request('searchtype') === '2' ? 'selected' : '' }}>Contractual
                            </option>
                            <option value="3" {{ request('searchtype') === '3' ? 'selected' : '' }}>Probationary
                            </option>
                            <option value="4" {{ request('searchtype') === '4' ? 'selected' : '' }}>Project Based
                            </option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[50px]">Code</th>
                                <th scope="col" class="w-[150px]">Name</th>
                                <th scope="col" class="w-[150px]">Position</th>
                                <th scope="col" class="w-[100px]">Location</th>
                                <th scope="col" class="w-[100px]">Email</th>
                                <th scope="col" class="w-[100px]">Phone</th>
                                <th scope="col" class="w-[100px]">Date Hired</th>
                                <th scope="col" class="w-[80px]">Status</th>
                                <th scope="col" class="text-center w-[50px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td class="font-semibold whitespace-normal break-all align-top"
                                        style="color: var(--ft-ink);">
                                        {{ $employee->employee_code }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $employee->last_name }}{{ trim($employee->first_name) ? ', ' . $employee->first_name : '' }}{{ trim($employee->middle_name) ? ' ' . $employee->middle_name : '' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $employee->position }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $employee->location ? $employee->location->name : '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $employee->email }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $employee->mobile }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $employee->hire_date ? Carbon::parse($employee->hire_date)->format('m/d/Y') : '—' }}
                                    </td>
                                    <td>
                                        @php
                                            $statuses = [
                                                0 => ['class' => 'ft-badge-no', 'label' => 'Inactive'],
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                                2 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'On Leave',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                                3 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Resigned',
                                                    'style' =>
                                                        'background: var(--ft-neutral-bg); color: var(--ft-neutral-text);',
                                                ],
                                                4 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Retired',
                                                    'style' =>
                                                        'background: var(--ft-neutral-bg); color: var(--ft-neutral-text);',
                                                ],
                                                5 => ['class' => 'ft-badge-no', 'label' => 'Terminated'],
                                                6 => ['class' => 'ft-badge-na', 'label' => 'AWOL'],
                                                7 => ['class' => 'ft-badge-na', 'label' => 'Deceased'],
                                                8 => ['class' => 'ft-badge-na', 'label' => 'Dropped'],
                                                9 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Labor',
                                                    'style' =>
                                                        'background: var(--ft-info-bg, #e0e7ff); color: var(--ft-info-text, #3730a3);',
                                                ],
                                                10 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Floating',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                            ];
                                            $status = $statuses[$employee->status] ?? [
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
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('employee.edit', $employee->id) }}"
                                                title="Edit employee {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
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
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                                    <path
                                                        d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                                </svg>
                                            </button>

                                            <button type="button"
                                                title="Upload file to {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                                data-modal-target="upload-file-modal"
                                                data-modal-toggle="upload-file-modal"
                                                data-name="{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}"
                                                data-id="{{ $employee->id }}" onclick="loadUploadModal(this)"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708z" />
                                                    <path
                                                        d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="ft-empty">No employee found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!$canCreate && !$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create or update employee information.
                    </p>
                @elseif (!$canCreate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create employee information.
                    </p>
                @elseif (!$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to update employee information.
                    </p>
                @endif

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- View asset modal -->
    <div id="view-asset-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-4xl h-full md:h-auto">
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Asset Accountability
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
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
                <div class="p-4 overflow-y-auto max-h-[70vh]">
                    <form action="" method="put">
                        @csrf
                        <input type="hidden" name="empId" id="empId">
                        <div name="employee_name" id="employee_name" class="mb-3 text-sm font-semibold"
                            style="color: var(--ft-ink);"></div>

                        <div class="ft-table-wrap">
                            <table class="ft-table">
                                <thead>
                                    <tr>
                                        <th class="w-8 px-2 py-2">
                                            <input type="checkbox" id="selectAllAssetsHeader" checked>
                                        </th>
                                        <th>Asset Code</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Location</th>
                                        <th>License Validity</th>
                                    </tr>
                                </thead>
                                <tbody id="assetTableBody">
                                    <!-- Asset rows will be inserted here with checkboxes -->
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <a href="#" title="Print Accountability Form" onclick="printARE(event)"
                                class="ft-btn ft-btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="mr-2 bi bi-printer" viewBox="0 0 16 16">
                                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                    <path
                                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                </svg>
                                Accountability Form
                            </a>

                            <a href="#" title="Print Duty Detail" onclick="printDutyDetail(event)"
                                class="ft-btn ft-btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="mr-2 bi bi-printer" viewBox="0 0 16 16">
                                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                    <path
                                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                </svg>
                                Duty Detail Form
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload file modal -->
    <div id="upload-file-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-5xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Upload File
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
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
                <div class="p-4 overflow-y-auto max-h-[75vh]">
                    <form id="uploadFileForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="upload_emp_id" id="upload_emp_id">

                        <!-- MAIN 2 COLUMN LAYOUT -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            <!-- ================= LEFT SIDE (HEADER INFO) ================= -->
                            <div>
                                <h3 class="ft-section-title" style="margin-top: 0;">File Information</h3>

                                <!-- HEADER 2 COLUMN GRID -->
                                <div class="grid md:grid-cols-2 sm:grid-cols-2 gap-4">
                                    <div class="w-full sm:col-span-2">
                                        <input name="upload_employee_name" id="upload_employee_name" readonly
                                            class="ft-field font-semibold" style="background: var(--ft-mist);">
                                    </div>

                                    <div class="w-full sm:col-span-1">
                                        <label class="ft-label">Document Type</label>
                                        <select name="doctype" id="doctype" class="ft-field">
                                            <option value="">Select Document Type</option>
                                            @foreach ($docTypes as $docType)
                                                <option value="{{ $docType->id }}">{{ $docType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-full sm:col-span-1">
                                        <label class="ft-label">Document Date</label>
                                        <input type="date" name="document_date" id="document_date" class="ft-field">
                                    </div>

                                    <div class="w-full sm:col-span-2">
                                        <label class="ft-label">Note</label>
                                        <textarea id="note" name="note" rows="3" class="ft-field"></textarea>
                                    </div>

                                    <!-- File Upload (PDF, Excel, etc.) -->
                                    <div class="sm:col-span-2">
                                        <label class="ft-label">Upload Files</label>
                                        <input type="file" name="files[]" multiple
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip,.rar" class="ft-field p-2">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= RIGHT SIDE (DETAILS TABLE) ================= -->
                            <div>
                                <h3 class="ft-section-title" style="margin-top: 0;">Uploaded Files</h3>
                                <div class="ft-table-wrap" style="max-height: 320px; overflow-y: auto;">
                                    <table class="ft-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Uploaded By</th>
                                                <th>Document Type</th>
                                                <th>Document Date</th>
                                                <th>Note</th>
                                                <th>File Name</th>
                                                <th>File</th>
                                            </tr>
                                        </thead>
                                        <tbody id="viewDetailsTable">
                                            <!-- Uploaded files will be inserted here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button @if (Auth::user()->role == 2) disabled @endif type="button" id="saveButton"
                                data-modal-toggle="upload-file-modal" onclick="submitUploadFileForm()"
                                class="ft-btn ft-btn-primary {{ Auth::user()->role == 2 ? 'cursor-not-allowed opacity-50' : '' }}">
                                Save
                            </button>
                            <button type="button" id="closeButton" data-modal-toggle="upload-file-modal"
                                class="ft-btn ft-btn-ghost">
                                Close
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End view asset modal -->

    <script>
        let searchTimer;
        let selectedAssetIds = [];

        const searchInput = document.getElementById('simple-search');
        const form = searchInput.closest('form');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(() => {
                form.submit();
            }, 800);
        });

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

        // Main function to open asset modal with checkboxes
        function openAssetModal(button) {
            const id = button.getAttribute('data-id');
            document.getElementById('empId').value = id;
            document.getElementById('employee_name').innerText = button.getAttribute('data-name');

            fetch(`/employee/${id}/accountability`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('assetTableBody');
                    tbody.innerHTML = '';

                    if (data.assets.length === 0) {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color: var(--ft-muted);">
                                No assigned assets found.
                            </td>
                        </tr>
                    `;
                        return;
                    }

                    // Reset selected assets
                    selectedAssetIds = [];

                    data.assets.forEach(asset => {
                        // Add asset ID to selected list by default
                        selectedAssetIds.push(asset.id);

                        let expirationDate = asset.licenses ?
                            asset.licenses.map(l => new Date(l.expiration_date).toLocaleDateString()).join(
                                '<br>') :
                            '';

                        tbody.innerHTML += `
                        <tr data-asset-id="${asset.id}">
                            <td class="text-center px-2 py-2">
                                <input type="checkbox" class="asset-checkbox" value="${asset.id}" checked>
                            </td>
                            <td class="font-semibold" style="color: var(--ft-ink);">${asset.asset_code ?? ''}</td>
                            <td>${asset.name ?? ''}</td>
                            <td>${asset.category ? asset.category.name : ''}</td>
                            <td>${asset.location ? asset.location.name : ''}</td>                        
                            <td>${expirationDate}</td>
                        </tr>
                    `;
                    });

                    // Add event listeners to checkboxes
                    document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const assetId = parseInt(this.value);
                            if (this.checked) {
                                if (!selectedAssetIds.includes(assetId)) {
                                    selectedAssetIds.push(assetId);
                                }
                            } else {
                                selectedAssetIds = selectedAssetIds.filter(id => id !== assetId);
                            }
                            updateSelectAllState();
                        });
                    });

                    // Add event listeners for select all checkboxes
                    document.getElementById('selectAllAssetsHeader').addEventListener('change', function() {
                        toggleAllCheckboxes(this.checked);
                    });

                    // Initialize select all state
                    updateSelectAllState();
                })
                .catch(error => {
                    console.error('Error loading assets:', error);
                    alert('Error loading assets. Please try again.');
                });
        }

        function toggleAllCheckboxes(checked) {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checked;
                const assetId = parseInt(checkbox.value);
                if (checked) {
                    if (!selectedAssetIds.includes(assetId)) {
                        selectedAssetIds.push(assetId);
                    }
                } else {
                    selectedAssetIds = selectedAssetIds.filter(id => id !== assetId);
                }
            });

            document.getElementById('selectAllAssetsHeader').checked = checked;
        }

        function updateSelectAllState() {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
            const allChecked = checkboxes.length > 0 && checkedCount === checkboxes.length;

            document.getElementById('selectAllAssetsHeader').checked = allChecked;
        }

        function printARE(event) {
            event.preventDefault();
            const empId = document.getElementById('empId').value;

            if (!empId) {
                alert('Employee must be selected.');
                return;
            }

            if (selectedAssetIds.length === 0) {
                alert('Please select at least one asset to print.');
                return;
            }

            const assetIdsParam = selectedAssetIds.join(',');
            let url = "{{ route('are.print') }}" + "?empId=" + empId + "&assetIds=" + assetIdsParam;
            window.open(url, '_blank');
        }

        function printDutyDetail(event) {
            event.preventDefault();
            const empId = document.getElementById('empId').value;

            if (!empId) {
                alert('Employee must be selected.');
                return;
            }

            if (selectedAssetIds.length === 0) {
                alert('Please select at least one asset to print.');
                return;
            }

            const assetIdsParam = selectedAssetIds.join(',');
            let url = "{{ route('duty.detail.print') }}" + "?empId=" + empId + "&assetIds=" + assetIdsParam;
            window.open(url, '_blank');
        }

        function loadUploadModal(button) {
            const id = button.getAttribute('data-id');
            const empName = button.getAttribute('data-name');

            const empIdInput = document.getElementById('upload_emp_id');
            const empNameInput = document.getElementById('upload_employee_name');

            if (empIdInput && empNameInput) {
                empIdInput.value = id;
                empNameInput.value = empName;
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
                            <td colspan="7" class="text-center py-4" style="color: var(--ft-muted);">
                                No files uploaded.
                            </td>
                        </tr>
                    `;
                        return;
                    }

                    data.files.forEach(file => {
                        const uploadDate = new Date(file.created_at).toLocaleDateString();
                        tableBody.innerHTML += `
                        <tr>
                            <td>${uploadDate}</td>
                            <td>${file.uploaded_by ? file.uploader.lname + ', ' + file.uploader.fname : ''}</td>
                            <td>${file.document_type_id ? file.document_type.name : ''}</td>
                            <td>${file.document_date ? new Date(file.document_date).toLocaleDateString() : ''}</td>
                            <td>${file.note}</td>
                            <td>${file.file_name}</td>
                            <td>
                                <a href="/storage/${file.path}" target="_blank" class="hover:underline" style="color: var(--ft-accent);">
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
