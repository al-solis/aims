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
                        <h1>Maintenance Management</h1>
                        <p>Schedule and track asset maintenance activities.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @if (!$canCreate) disabled @endif data-modal-target="add-modal"
                            data-modal-toggle="{{ $canCreate ? 'add-modal' : '' }}"
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Schedule Maintenance
                        </button>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Scheduled',
                            'value' => $scheduledMaintenances,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-warning-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>',
                        ],
                        [
                            'title' => 'In Progress',
                            'value' => $inprogressMaintenances,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent-dark);" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"/></svg>',
                        ],
                        [
                            'title' => 'Overdue',
                            'value' => $overdueMaintenances,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-danger-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>',
                        ],
                        [
                            'title' => 'Completed This Month',
                            'value' => $completedThisMonthMaintenances,
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
                @if (session('success'))
                    <div id="success-alert" class="ft-alert ft-alert-success flex items-center justify-between"
                        data-success="true">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button type="button" onclick="this.closest('[id$=-alert]').style.display='none'"
                            class="hover:opacity-75">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- Filters --}}
                <form action="" method="GET" class="flex flex-col md:flex-row gap-3">
                    <div class="md:w-2/3 w-full">
                        <input type="text" name="search" placeholder="Search by asset or description..."
                            class="ft-field" value="{{ request()->query('search') }}" oninput="this.form.submit()">
                    </div>
                    <div class="md:w-1/3 w-full flex gap-3">
                        <select name="searchType" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="1" {{ request('searchType') === '1' ? 'selected' : '' }}>Preventive</option>
                            <option value="2" {{ request('searchType') === '2' ? 'selected' : '' }}>Corrective</option>
                            <option value="3" {{ request('searchType') === '3' ? 'selected' : '' }}>Emergency</option>
                            <option value="4" {{ request('searchType') === '4' ? 'selected' : '' }}>Inspection</option>
                        </select>
                        <select name="status" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Scheduled</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>In-progress</option>
                            <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Completed</option>
                            <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[100px]">ID</th>
                                <th scope="col" class="w-[180px]">Asset</th>
                                <th scope="col" class="w-[80px]">Type</th>
                                <th scope="col" class="w-[200px]">Description</th>
                                <th scope="col" class="w-[80px]">Scheduled Date</th>
                                <th scope="col" class="w-[100px]">Status</th>
                                <th scope="col" class="w-[80px]">Priority</th>
                                <th scope="col" class="w-[150px]">Technician</th>
                                <th scope="col" class="text-right w-[120px]">Cost</th>
                                <th scope="col" class="text-center w-[50px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($maintenances as $maintenance)
                                <tr>
                                    <td class="whitespace-normal break-all align-top font-semibold"
                                        style="color: var(--ft-ink);">
                                        {{ $maintenance->maintenance_code }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $maintenance->asset ? $maintenance->asset->name : '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        @php
                                            $typeLabels = [
                                                1 => 'Preventive',
                                                2 => 'Corrective',
                                                3 => 'Emergency',
                                                4 => 'Inspection',
                                            ];
                                        @endphp
                                        {{ $typeLabels[$maintenance->type] ?? 'Unknown' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $maintenance->description }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ Carbon::parse($maintenance->scheduled_date)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        @php
                                            $statuses = [
                                                1 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Scheduled',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                                2 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'In-Progress',
                                                    'style' =>
                                                        'background: var(--ft-info-bg, #e0e7ff); color: var(--ft-info-text, #3730a3);',
                                                ],
                                                3 => ['class' => 'ft-badge-ok', 'label' => 'Completed'],
                                                4 => ['class' => 'ft-badge-no', 'label' => 'Cancelled'],
                                                5 => ['class' => 'ft-badge-no', 'label' => 'Overdue'],
                                            ];
                                            $status = $statuses[$maintenance->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];

                                            if (
                                                $maintenance->scheduled_date < now() &&
                                                $maintenance->status != 3 &&
                                                $maintenance->status != 4
                                            ) {
                                                $status = $statuses[5]; // Overdue
                                            }
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }}"
                                            style="{{ $status['style'] ?? '' }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statuses = [
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Low'],
                                                2 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'Medium',
                                                    'style' =>
                                                        'background: var(--ft-warning-bg); color: var(--ft-warning-text);',
                                                ],
                                                3 => [
                                                    'class' => 'ft-badge',
                                                    'label' => 'High',
                                                    'style' =>
                                                        'background: var(--ft-orange-bg); color: var(--ft-orange-text);',
                                                ],
                                                4 => ['class' => 'ft-badge-no', 'label' => 'Critical'],
                                            ];
                                            $status = $statuses[$maintenance->priority] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }}"
                                            style="{{ $status['style'] ?? '' }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $maintenance->technician }}
                                    </td>
                                    <td class="text-right tabular-nums whitespace-normal break-all align-top"
                                        style="color: var(--ft-text);">
                                        {{ number_format($maintenance->cost, 2) }}
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" @if (!$canUpdate) disabled @endif
                                                title="Edit maintenance : {{ $maintenance->maintenance_code }}"
                                                data-id="{{ $maintenance->id }}"
                                                data-asset_id="{{ $maintenance->asset_id }}"
                                                data-asset_name="{{ $maintenance->asset ? $maintenance->asset->name : '' }}"
                                                data-type="{{ $maintenance->type }}"
                                                data-description="{{ $maintenance->description }}"
                                                data-scheduled_date="{{ $maintenance->scheduled_date }}"
                                                data-priority="{{ $maintenance->priority }}"
                                                data-technician="{{ $maintenance->technician }}"
                                                data-cost="{{ $maintenance->cost }}"
                                                data-notes="{{ $maintenance->notes }}"
                                                data-status="{{ $maintenance->status }}" data-modal-target="edit-modal"
                                                data-modal-toggle="edit-modal" onclick="openEditModal(this)"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canUpdate ? ' cursor-not-allowed' : '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </button>

                                            @if ($maintenance->status == 3 && !in_array($maintenance->status, [2, 4]))
                                                {{-- Completed --}}
                                                <button type="button"
                                                    title="Maintenance : {{ $maintenance->maintenance_code }} is already completed, cannot be marked as in-progress"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd"
                                                            d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    title="Maintenance : {{ $maintenance->maintenance_code }} is already completed"
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
                                                    title="Maintenance : {{ $maintenance->maintenance_code }} is already completed, cannot be voided"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed"
                                                    onclick="voidMaintenance({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8z" />
                                                    </svg>
                                                </button>
                                            @elseif ($maintenance->status == 2 && !in_array($maintenance->status, [3, 4]))
                                                {{-- In-progress --}}
                                                <button type="button"
                                                    title="Maintenance : {{ $maintenance->maintenance_code }} is in-progress, cannot be marked as in-progress again"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed"
                                                    onclick="markAsInProgress({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd"
                                                            d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                                    </svg>
                                                </button>
                                                <button type="button" @if (!$canUpdate) disabled @endif
                                                    title="Mark as complete : {{ $maintenance->maintenance_code }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canUpdate ? ' cursor-not-allowed' : '' }}"
                                                    onclick="markAsComplete({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                        <path
                                                            d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                                    </svg>
                                                </button>
                                                <button type="button" @if (!$canDelete) disabled @endif
                                                    title="Void maintenance : {{ $maintenance->maintenance_code }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canDelete ? ' cursor-not-allowed' : '' }}"
                                                    onclick="voidMaintenance({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8z" />
                                                    </svg>
                                                </button>
                                            @elseif ($maintenance->status == 4)
                                                {{-- Voided --}}
                                                <button type="button"
                                                    title="Maintenance : {{ $maintenance->maintenance_code }} is already voided"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd"
                                                            d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    title="Request : {{ $maintenance->maintenance_code }} is already voided"
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
                                                    title="Request : {{ $maintenance->maintenance_code }} is already voided, cannot be voided again"
                                                    class="p-1.5 rounded text-gray-300 cursor-not-allowed">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8z" />
                                                    </svg>
                                                </button>
                                            @else
                                                {{-- Mark as In-Progress --}}
                                                <button type="button" @if (!$canUpdate) disabled @endif
                                                    title="Mark as in-progress : {{ $maintenance->maintenance_code }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canUpdate ? ' cursor-not-allowed' : '' }}"
                                                    onclick="markAsInProgress({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd"
                                                            d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                                    </svg>
                                                </button>
                                                {{-- Mark as complete --}}
                                                <button type="button" @if (!$canUpdate) disabled @endif
                                                    title="Mark as complete : {{ $maintenance->maintenance_code }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canUpdate ? ' cursor-not-allowed' : '' }}"
                                                    onclick="markAsComplete({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                        <path
                                                            d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                                    </svg>
                                                </button>
                                                {{-- Mark as void --}}
                                                <button type="button" @if (!$canDelete) disabled @endif
                                                    title="Void maintenance : {{ $maintenance->maintenance_code }}"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canDelete ? ' cursor-not-allowed' : '' }}"
                                                    onclick="voidMaintenance({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="ft-empty">No scheduled maintenance found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $maintenances->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create preventive maintenance modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Schedule Maintenance
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
                    <form action="{{ route('maintenance.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <label for="asset_id" class="ft-label">Asset <span class="text-red-600">*</span></label>
                                <select name="asset_id" id="asset_id" class="ft-field select2" required>
                                    <option value="" disabled selected>Select asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->asset_code }}
                                            ({{ $asset->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="type" class="ft-label">Maintenance Type <span
                                        class="text-red-600">*</span></label>
                                <select name="type" id="type" class="ft-field" required>
                                    <option value="" disabled selected>Select type</option>
                                    <option value="1">Preventive</option>
                                    <option value="2">Corrective</option>
                                    <option value="3">Emergency</option>
                                    <option value="4">Inspection</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="cost" class="ft-label">Cost</label>
                                <input type="number" name="cost" id="cost" min="0" step="0.01"
                                    class="ft-field" placeholder="0.00">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="description" class="ft-label">Description</label>
                                <textarea name="description" id="description" rows="3" class="ft-field"
                                    placeholder="Describe the maintenance work to be performed"></textarea>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="scheduled_date" class="ft-label">Scheduled Date <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="scheduled_date" id="scheduled_date"
                                    min="{{ now()->format('Y-m-d') }}" class="ft-field" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="priority" class="ft-label">Priority <span
                                        class="text-red-600">*</span></label>
                                <select name="priority" id="priority" class="ft-field" required>
                                    <option value="" disabled selected>Select priority</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                    <option value="4">Critical</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="technician" class="ft-label">Technician</label>
                                <input type="text" name="technician" id="technician" class="ft-field"
                                    placeholder="Technician name">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="notes" class="ft-label">Remarks</label>
                                <textarea name="notes" id="notes" rows="3" class="ft-field"
                                    placeholder="Any additional information or special instructions"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Schedule Maintenance
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create preventive maintenance modal -->

    <!-- Modal Edit -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Update Maintenance Schedule
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
                        <input type="hidden" name="edit_status" id="edit_status">

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <input type="hidden" name="edit_asset_id" id="edit_asset_id">
                                <label for="edit_asset_name" class="ft-label">Asset Name <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="edit_asset_name" id="edit_asset_name" class="ft-field"
                                    style="background: var(--ft-mist);" readonly>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_type" class="ft-label">Maintenance Type <span
                                        class="text-red-600">*</span></label>
                                <select name="edit_type" id="edit_type" class="ft-field" required>
                                    <option value="" disabled selected>Select type</option>
                                    <option value="1">Preventive</option>
                                    <option value="2">Corrective</option>
                                    <option value="3">Emergency</option>
                                    <option value="4">Inspection</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_cost" class="ft-label">Cost</label>
                                <input type="number" name="edit_cost" id="edit_cost" min="0" step="0.01"
                                    class="ft-field" placeholder="0.00">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_description" class="ft-label">Description</label>
                                <textarea name="edit_description" id="edit_description" rows="3" class="ft-field"
                                    placeholder="Describe the maintenance work to be performed"></textarea>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_scheduled_date" class="ft-label">Scheduled Date <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="edit_scheduled_date" id="edit_scheduled_date"
                                    min="{{ now()->format('Y-m-d') }}" class="ft-field" required>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_priority" class="ft-label">Priority <span
                                        class="text-red-600">*</span></label>
                                <select name="edit_priority" id="edit_priority" class="ft-field" required>
                                    <option value="" disabled selected>Select priority</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                    <option value="4">Critical</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_technician" class="ft-label">Technician</label>
                                <input type="text" name="edit_technician" id="edit_technician" class="ft-field"
                                    placeholder="Technician name">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_notes" class="ft-label">Remarks</label>
                                <textarea name="edit_notes" id="edit_notes" rows="3" class="ft-field"
                                    placeholder="Any additional information or special instructions"></textarea>
                            </div>
                        </div>

                        <button type="submit" @if (!$canUpdate) disabled @endif
                            class="btn-Update ft-btn ft-btn-primary w-full justify-center {{ !$canUpdate ? ' cursor-not-allowed' : '' }}">
                            Update Maintenance Record
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
        });

        function formatDateForInput(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date)) return '';

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function markAsInProgress(id, maintenanceCode) {
            if (confirm(`Are you sure you want to mark maintenance ${maintenanceCode} as in-progress?`)) {
                $.ajax({
                    url: `/maintenance/${id}/mark-in-progress`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while marking as in-progress.');
                    }
                });
            }
        }

        function markAsComplete(id, maintenanceCode) {
            if (confirm(
                    `You will not be able to edit the data once marked as complete. Are you sure you want to mark maintenance ${maintenanceCode} as complete?`
                )) {
                $.ajax({
                    url: `/maintenance/${id}/mark-complete`,
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

        function voidMaintenance(id, maintenanceCode) {
            if (confirm(
                    `You will not be able to edit the data once voided. Are you sure you want to void maintenance ${maintenanceCode}?`
                )) {
                $.ajax({
                    url: `/maintenance/${id}/void`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while voiding the maintenance.');
                    }
                });
            }
        }

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
            const editStatus = button.getAttribute('data-status');
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_status').value = button.getAttribute('data-status');
            document.getElementById('edit_asset_id').value = button.getAttribute('data-asset_id');
            document.getElementById('edit_asset_name').value = button.getAttribute('data-asset_name');
            document.getElementById('edit_type').value = button.getAttribute('data-type');
            document.getElementById('edit_description').value = button.getAttribute('data-description');
            document.getElementById('edit_scheduled_date').value = formatDateForInput(button.getAttribute(
                'data-scheduled_date'));
            document.getElementById('edit_priority').value = button.getAttribute('data-priority');
            document.getElementById('edit_technician').value = button.getAttribute('data-technician');
            document.getElementById('edit_cost').value = parseFloat(button.getAttribute('data-cost')) || 0.00;
            document.getElementById('edit_notes').value = button.getAttribute('data-notes');

            if (editStatus == 3 || editStatus == 4) {
                // If status is complete or void, disable all form fields
                const formElements = document.querySelectorAll('#editForm input, #editForm select, #editForm textarea');
                formElements.forEach(element => {
                    element.disabled = true;
                });
                // Hide the update button
                document.querySelector('.btn-Update').style.display = 'none';
                document.querySelector('.btn-Update').setAttribute('title', editStatus == 3 ?
                    'This maintenance request is marked as complete and cannot be edited.' :
                    'This maintenance request is voided and cannot be edited.');
            } else {
                // Enable form fields and show update button for other statuses
                const formElements = document.querySelectorAll('#editForm input, #editForm select, #editForm textarea');
                formElements.forEach(element => {
                    element.disabled = false;
                });
                document.querySelector('.btn-Update').style.display = 'inline-flex';
                document.querySelector('.btn-Update').setAttribute('title', 'Update maintenance record');
            }

            const form = document.getElementById('editForm');
            form.action = `/maintenance/${id}`;
        }
    </script>
@endsection
