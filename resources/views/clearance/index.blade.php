@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Str;
    @endphp
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>Personnel Clearance Management</h1>
                        <p>Manage asset returns for transfers, resignation, and contract ending.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @if (!$canCreate) disabled @endif data-modal-target="add-modal"
                            data-modal-toggle="{{ $canCreate ? 'add-modal' : '' }}"
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Clearance Request
                        </button>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Total Requests',
                            'value' => $totalRequests,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent);" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 2a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-12a.5.5 0 0 0-.5-.5H12a.5.5 0 0 1 0-1h.5A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1H4a.5.5 0 0 1 0 1z"/><path d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5"/></svg>',
                        ],
                        [
                            'title' => 'Pending',
                            'value' => $pendingRequests,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-warning-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>',
                        ],
                        [
                            'title' => 'Overdue',
                            'value' => $overdueRequests,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-danger-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>',
                        ],
                        [
                            'title' => 'Completed',
                            'value' => $completedRequests,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>',
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
                        <input type="text" name="search" placeholder="Search by name or description..." class="ft-field"
                            value="{{ request()->query('search') }}" oninput="this.form.submit()">
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
                        <select name="searchtype" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="1" {{ request('searchtype') === '1' ? 'selected' : '' }}>Transfer</option>
                            <option value="2" {{ request('searchtype') === '2' ? 'selected' : '' }}>Resignation
                            </option>
                            <option value="3" {{ request('searchtype') === '3' ? 'selected' : '' }}>Contract Ending
                            </option>
                            <option value="4" {{ request('searchtype') === '4' ? 'selected' : '' }}>Termination
                            </option>
                        </select>
                        <select name="status" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>In-progress</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Completed</option>
                            <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Overdue</option>
                            <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[90px]">Request ID</th>
                                <th scope="col" class="w-[180px]">Employee</th>
                                <th scope="col" class="w-[150px]">Department</th>
                                <th scope="col" class="w-[200px]">Type</th>
                                <th scope="col" class="w-[80px]">Expected Date</th>
                                <th scope="col" class="w-[100px]">Status</th>
                                <th scope="col" class="w-[120px]">Approver</th>
                                <th scope="col" class="w-[220px]">Current Approver</th>
                                <th scope="col" class="text-right w-[60px]">Assets</th>
                                <th scope="col" class="text-right w-[180px]">Value</th>
                                <th scope="col" class="text-center w-[50px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clearanceHeaders as $clearanceHeader)
                                <tr>
                                    <td class="whitespace-normal break-all align-top font-semibold"
                                        style="color: var(--ft-ink);">
                                        {{ $clearanceHeader->request_number }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $clearanceHeader->employee ? $clearanceHeader->employee->last_name . ', ' . $clearanceHeader->employee->first_name . ' ' . $clearanceHeader->employee->middle_name : '' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $clearanceHeader->employee && $clearanceHeader->employee->location ? $clearanceHeader->employee->location->description : '' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        @php
                                            $typeLabels = [
                                                1 => 'Transfer',
                                                2 => 'Resignation',
                                                3 => 'Contract Ending',
                                                4 => 'Termination',
                                            ];
                                        @endphp
                                        {{ $typeLabels[$clearanceHeader->type] ?? 'Unknown' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ Carbon::parse($clearanceHeader->expected_date)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        @php
                                            $statuses = [
                                                0 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Pending',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                                1 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'In-progress',
                                                    'style' =>
                                                        'background: var(--ft-info-bg, #e0e7ff); color: var(--ft-info-text, #3730a3);',
                                                ],
                                                2 => ['class' => 'ft-badge-ok', 'label' => 'Completed'],
                                                3 => ['class' => 'ft-badge-no', 'label' => 'Overdue'],
                                                4 => ['class' => 'ft-badge-na', 'label' => 'Cancelled'],
                                            ];
                                            $status = $statuses[$clearanceHeader->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];

                                            if (
                                                $clearanceHeader->expected_date < now() &&
                                                $clearanceHeader->status != 2
                                            ) {
                                                $status = $statuses[3]; // Overdue
                                            }
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }}"
                                            style="{{ $status['style'] ?? '' }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        @if ($clearanceHeader->status == '0')
                                            <span class="ft-badge"
                                                style="background: var(--ft-warning-bg); color: var(--ft-warning-text);">Not
                                                Submitted</span>
                                        @endif
                                        @if ($clearanceHeader->status == '2' && $clearanceHeader->approvalHistory->last()->approved == '0')
                                            <span class="ft-badge-no">Rejected</span>
                                        @elseif ($clearanceHeader->status == '2' && $clearanceHeader->approvalHistory->last()->approved == '1')
                                            <span class="ft-badge-ok">Approved</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $clearanceHeader->clearance_approver ? $clearanceHeader->clearance_approver->name : '—' }}
                                    </td>
                                    <td class="text-right tabular-nums whitespace-normal break-all align-top"
                                        style="color: var(--ft-text);">
                                        {{ $clearanceHeader->clearance_details->count() }}
                                    </td>
                                    <td class="text-right tabular-nums whitespace-normal break-all align-top"
                                        style="color: var(--ft-text);">
                                        {{ number_format($clearanceHeader->clearance_details->sum('total'), 2) }}
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-0">
                                            @if ($canUpdate || Auth::user()->id == $clearanceHeader->created_by)
                                                <a href="{{ route('clearance.show', $clearanceHeader->id) }}"
                                                    title="Edit clearance : {{ $clearanceHeader->request_number }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd"
                                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>
                                                </a>
                                            @endif

                                            <a href="{{ route('clearance.print', $clearanceHeader->id) }}"
                                                target="_blank"
                                                title="Print clearance : {{ $clearanceHeader->request_number }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                                    <path
                                                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                                </svg>
                                            </a>

                                            @if ($clearanceHeader->status == 2 && $clearanceHeader->status != 4)
                                                <button type="button"
                                                    title="Request : {{ $clearanceHeader->request_number }} is already completed"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                        <path
                                                            d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    title="Request : {{ $clearanceHeader->request_number }} is already completed, cannot be voided"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed"
                                                    onclick="voidClearance({{ $clearanceHeader->id }}, '{{ $clearanceHeader->request_number }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>
                                                </button>
                                            @elseif ($clearanceHeader->status == 4)
                                                <button type="button"
                                                    title="Request : {{ $clearanceHeader->request_number }} is already voided"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    title="Request : {{ $clearanceHeader->request_number }} is already voided, cannot be voided again"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed"
                                                    onclick="voidClearance({{ $clearanceHeader->id }}, '{{ $clearanceHeader->request_number }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>
                                                </button>
                                            @elseif ($clearanceHeader->status == 0)
                                                <button type="button" @if (!$canUpdate || Auth::user()->id != $clearanceHeader->created_by) disabled @endif
                                                    title="Submit for approval : {{ $clearanceHeader->request_number }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canUpdate || Auth::user()->id != $clearanceHeader->created_by ? ' cursor-not-allowed' : '' }}"
                                                    onclick="submitForApproval({{ $clearanceHeader->id }}, '{{ $clearanceHeader->request_number }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" />
                                                        <path
                                                            d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686" />
                                                    </svg>
                                                </button>
                                                <button type="button" @if (!$canUpdate || Auth::user()->id != $clearanceHeader->created_by) disabled @endif
                                                    title="Void clearance : {{ $clearanceHeader->request_number }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canUpdate || Auth::user()->id != $clearanceHeader->created_by ? ' cursor-not-allowed' : '' }}"
                                                    onclick="voidClearance({{ $clearanceHeader->id }}, '{{ $clearanceHeader->request_number }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>
                                                </button>
                                            @endif

                                            @if ($clearanceHeader->current_approver == $userLocation->location_id)
                                                <button type="button"
                                                    title="Approve request : {{ $clearanceHeader->request_number }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors"
                                                    onclick="approveRequest({{ $clearanceHeader->id }}, '{{ $clearanceHeader->request_number }}', '{{ $clearanceHeader->approval_level }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                        <path
                                                            d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    title="Reject request : {{ $clearanceHeader->request_number }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors"
                                                    onclick="rejectRequest({{ $clearanceHeader->id }}, '{{ $clearanceHeader->request_number }}', '{{ $clearanceHeader->approval_level }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 1 1 .708.708L8.707 8l2.647 2.646a.5.5 0 1 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 1 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="ft-empty">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!$canCreate && !$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create or update clearance request information.
                    </p>
                @elseif (!$canCreate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create clearance request information.
                    </p>
                @elseif (!$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to update clearance request information.
                    </p>
                @endif

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $clearanceHeaders->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create clearance modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Add New Clearance
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
                    <form action="{{ route('clearance.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <label for="employee_id" class="ft-label">Employee Name <span
                                        class="text-red-600">*</span></label>
                                <select name="employee_id" id="employee_id" class="ft-field select2" required>
                                    <option value="" disabled selected>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ Str::ucwords($employee->last_name) }},
                                            {{ Str::ucwords($employee->first_name) }}
                                            {{ Str::ucwords($employee->middle_name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="type" class="ft-label">Request Type <span
                                        class="text-red-600">*</span></label>
                                <select name="type" id="type" class="ft-field" required>
                                    <option value="" disabled selected>Select request type</option>
                                    <option value="1">Transfer</option>
                                    <option value="2">Resignation</option>
                                    <option value="3">Contract Ending</option>
                                    <option value="4">Termination</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="expected_date" class="ft-label">Expected Date <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="expected_date" id="expected_date" class="ft-field" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="remarks" class="ft-label">Remarks</label>
                                <textarea name="remarks" id="remarks" rows="3" class="ft-field" placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Submit Clearance Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create request modal -->

    <script>
        $(document).ready(function() {
            $('#employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#add-modal')
            });
        });

        function submitForApproval(id, requestNumber) {
            if (confirm(`Are you sure you want to submit clearance ${requestNumber} for approval?`)) {
                $.ajax({
                    url: `/clearance/${id}/submit`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while submitting for approval.');
                    }
                });
            }
        }

        function approveRequest(id, requestNumber, approvalLevel) {
            if (confirm(`Are you sure you want to approve clearance ${requestNumber}?`)) {
                let remarks = prompt("Enter remarks/comments (optional):");
                if (remarks === null) return;

                $.ajax({
                    url: `/clearance/${id}/approve`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remarks: remarks,
                        approval_level: approvalLevel
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while approving the request.');
                    }
                });
            }
        }

        function rejectRequest(id, requestNumber, approvalLevel) {
            if (confirm(`Are you sure you want to reject clearance ${requestNumber}?`)) {
                let remarks = prompt("Enter remarks/comments for rejection (optional):");
                if (remarks === null) return;

                $.ajax({
                    url: `/clearance/${id}/reject`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remarks: remarks,
                        approval_level: approvalLevel
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while rejecting the request.');
                    }
                });
            }
        }

        function markAsComplete(id, requestNumber) {
            if (confirm(
                    `You will not be able to edit the data once marked as complete. Are you sure you want to mark clearance ${requestNumber} as complete?`
                )) {
                $.ajax({
                    url: `/clearance/${id}/mark-complete`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while marking as complete.');
                    }
                });
            }
        }

        function voidClearance(id, requestNumber) {
            if (confirm(
                    `You will not be able to edit the data once voided. Are you sure you want to void clearance ${requestNumber}?`
                )) {
                $.ajax({
                    url: `/clearance/${id}/void`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while voiding the clearance.');
                    }
                });
            }
        }

        function clearModalFields() {
            const form = document.querySelector('form');
            form.reset();

            setTimeout(() => {
                const successMessage = document.querySelector('[data-success]');
                if (successMessage) successMessage.remove();
            }, 3000);
        }
    </script>
@endsection
