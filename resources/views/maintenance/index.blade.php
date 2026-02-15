@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Maintenance Management</h1>
                <p class="text-sm text-gray-500">
                    Schedule and track asset maintenance activities.
                </p>
            </div>
            <div class="flex items-center gap-2 mt-0">
                {{-- <a href="{{ route('setup.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a> --}}

                <button data-modal-target="add-modal" data-modal-toggle="add-modal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Schedule Maintenance
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Scheduled',
                    'value' => $scheduledMaintenances,
                    'color' => 'yellow',
                    'icon' => '
                        <svg class="w-5 h-5 text-yellow-600" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                        </svg>',
                ],
                [
                    'title' => 'In Progress',
                    'value' => $inprogressMaintenances,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"/>
                        </svg>',
                ],

                [
                    'title' => 'Overdue',
                    'value' => $overdueMaintenances,
                    'color' => 'red',
                    'icon' => '
                        <svg class="w-5 h-5 text-red-600" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>',
                ],
                [
                    'title' => 'Completed This Month',
                    'value' => $completedThisMonthMaintenances,
                    'color' => 'green',
                    'icon' => '
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
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
                    <input type="text" id="simple-search" name="search" placeholder="Search by asset or description..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        value = "{{ request()->query('search') }}" oninput="this.form.submit()">
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="searchType" name="searchType"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="1" {{ request('searchType') === '1' ? 'selected' : '' }}>Preventive</option>
                        <option value="2" {{ request('searchType') === '2' ? 'selected' : '' }}>Corrective</option>
                        <option value="3" {{ request('searchType') === '3' ? 'selected' : '' }}>Emergency</option>
                        <option value="4" {{ request('searchType') === '4' ? 'selected' : '' }}>Inspection</option>
                    </select>
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Scheduled</option>
                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>In-progress</option>
                        <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Completed</option>
                        <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Cancelled</option>
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
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">ID</th>
                        <th scope="col" class="px-4 py-3 text-left w-[180px]">Asset</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Type</th>
                        <th scope="col" class="px-4 py-3 text-left w-[200px]">Description</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Scheduled Date</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Priority</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Technician</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Cost</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($maintenances as $maintenance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 w-[100px]">{{ $maintenance->maintenance_code }}</td>
                            <td class="px-4 py-3 w-[180px]">
                                {{ $maintenance->asset ? $maintenance->asset->name : '' }}
                            </td>
                            <td class="px-4 py-3 w-[80px]">
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
                            <td class="px-4 py-3 w-[200px]">{{ $maintenance->description }}</td>
                            <td class="px-4 py-3 w-[80px]">
                                {{ Carbon::parse($maintenance->scheduled_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 w-[100px] text-xs font-semibold">
                                @php
                                    $statuses = [
                                        1 => ['color' => 'bg-yellow-100 text-yellow-700', 'label' => 'Scheduled'],
                                        2 => ['color' => 'bg-green-100 text-green-700', 'label' => 'In-Progress'],
                                        3 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Completed'],
                                        4 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Cancelled'],
                                        5 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Overdue'],
                                    ];
                                    $status = $statuses[$maintenance->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                @if ($maintenance->scheduled_date < now() && $maintenance->status != 3 && $maintenance->status != 4)
                                    @php
                                        $status = $statuses[5]; // Overdue
                                    @endphp
                                @endif

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 w-[80px] text-xs font-semibold">
                                @php
                                    $statuses = [
                                        1 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Low'],
                                        2 => ['color' => 'bg-yellow-100 text-yellow-700', 'label' => 'Medium'],
                                        3 => ['color' => 'bg-orange-100 text-orange-700', 'label' => 'High'],
                                        4 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Critical'],
                                    ];
                                    $status = $statuses[$maintenance->priority] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 w-[150px]">{{ $maintenance->technician }}</td>
                            <td class="px-4 py-3 text-right w-[80px]">{{ number_format($maintenance->cost, 2) }}</td>
                            <td class="px-4 py-3 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
                                    <button type="button" title="Edit maintenance : {{ $maintenance->maintenance_code }}"
                                        data-id="{{ $maintenance->id }}" data-asset_id="{{ $maintenance->asset_id }}"
                                        data-asset_name="{{ $maintenance->asset ? $maintenance->asset->name : '' }}"
                                        data-type="{{ $maintenance->type }}"
                                        data-description="{{ $maintenance->description }}"
                                        data-scheduled_date="{{ $maintenance->scheduled_date }}"
                                        data-priority="{{ $maintenance->priority }}"
                                        data-technician="{{ $maintenance->technician }}"
                                        data-cost="{{ $maintenance->cost }}" data-notes="{{ $maintenance->notes }}"
                                        data-status="{{ $maintenance->status }}" data-modal-target="edit-modal"
                                        data-modal-toggle="edit-modal" onclick="openEditModal(this)"
                                        class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path
                                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd"
                                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </button>

                                    {{-- <a href="" type="button" target="_blank"
                                        title="Print maintenance : {{ $maintenance->maintenance_code }}"
                                        class="group flex space-x-1 text-gray-500 hover:text-yellow-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                            <path
                                                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                        </svg>
                                    </a> --}}

                                    @if ($maintenance->status == 3 && !in_array($maintenance->status, [2, 4]))
                                        {{-- Completed --}}
                                        <button type="button"
                                            title="Maintenance : {{ $maintenance->maintenance_code }} is already completed, cannot be marked as in-progress"
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            title="Maintenance : {{ $maintenance->maintenance_code }} is already completed"
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                                <path
                                                    d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                <path
                                                    d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            title="Maintenance : {{ $maintenance->maintenance_code }} is already completed, cannot be voided"
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed"
                                            onclick="voidMaintenance({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
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
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed"
                                            onclick="markAsInProgress({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                            </svg>
                                        </button>

                                        {{-- Mark as complete --}}
                                        <button type="button"
                                            title="Mark as complete : {{ $maintenance->maintenance_code }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-green-600 transition-colors"
                                            onclick="markAsComplete({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                                <path
                                                    d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                <path
                                                    d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                            </svg>
                                        </button>

                                        {{-- Mark as void --}}
                                        <button type="button"
                                            title="Void maintenance : {{ $maintenance->maintenance_code }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-red-600 transition-colors"
                                            onclick="voidMaintenance({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
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
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            title="Request : {{ $maintenance->maintenance_code }} is already voided"
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                                <path
                                                    d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                <path
                                                    d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            title="Request : {{ $maintenance->maintenance_code }} is already voided, cannot be voided again"
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                <path
                                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8z" />
                                            </svg>
                                        </button>
                                    @else
                                        {{-- Mark as In-Progress --}}
                                        <button type="button"
                                            title="Mark as in-progress : {{ $maintenance->maintenance_code }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors"
                                            onclick="markAsInProgress({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                            </svg>
                                        </button>

                                        {{-- Mark as complete --}}
                                        <button type="button"
                                            title="Mark as complete : {{ $maintenance->maintenance_code }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-green-600 transition-colors"
                                            onclick="markAsComplete({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                                <path
                                                    d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                <path
                                                    d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                            </svg>
                                        </button>

                                        {{-- Mark as void --}}
                                        <button type="button"
                                            title="Void maintenance : {{ $maintenance->maintenance_code }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-red-600 transition-colors"
                                            onclick="voidMaintenance({{ $maintenance->id }}, '{{ $maintenance->maintenance_code }}' )">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
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
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No scheduled maintenance found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $maintenances->links() }}
        </div>
    </div>

    <!-- Create preventive maintenance modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Schedule Maintenance
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
                    <form action="{{ route('maintenance.store') }}" method="POST">
                        @csrf
                        <div class="grid ml-1 mr-1 gap-2 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <label for="asset_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset*</label>
                                <select name="asset_id" id="asset_id"
                                    class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" disabled selected>Select asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->asset_code }}
                                            ({{ $asset->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="type"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Maintenance
                                    Type*</label>
                                <select name="type" id="type"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" disabled selected>Select type</option>
                                    <option value="1">Preventive</option>
                                    <option value="2">Corrective</option>
                                    <option value="3">Emergency</option>
                                    <option value="4">Inspection</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="cost"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Cost</label>
                                <input type="number" name="cost" id="cost" min="0" step="0.01"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="0.00"></input>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="description"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Describe the maintenance work to be performed"></textarea>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="scheduled_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Scheduled Date*</label>
                                <input type="date" name="scheduled_date" id="scheduled_date"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required></input>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="priority"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Priority*</label>
                                <select name="priority" id="priority"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" disabled selected>Select priority</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                    <option value="4">Critical</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="technician"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Technician</label>
                                <input type="text" name="technician" id="technician"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Technician name"></input>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="notes"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Remarks</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Any additional information or special instructions"></textarea>
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
                            Schedule Maintenance
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create preventive maintenance modal -->

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
                        Update Maintenance Schedule
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
                        <input type="hidden" name="edit_status" id="edit_status">
                        <div class="grid ml-1 mr-1 gap-2 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <input type="hidden" name="edit_asset_id" id="edit_asset_id">
                                <label for="edit_asset_name"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset Name*</label>
                                <input type="text" name="edit_asset_name" id="edit_asset_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Asset Name" readonly></input>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_type"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Maintenance
                                    Type*</label>
                                <select name="edit_type" id="edit_type"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" disabled selected>Select type</option>
                                    <option value="1">Preventive</option>
                                    <option value="2">Corrective</option>
                                    <option value="3">Emergency</option>
                                    <option value="4">Inspection</option>
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_cost"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Cost</label>
                                <input type="number" name="edit_cost" id="edit_cost" min="0" step="0.01"
                                    format="0.00"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="0.00"></input>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_description"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea name="edit_description" id="edit_description" rows="3"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Describe the maintenance work to be performed"></textarea>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_scheduled_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Scheduled Date*</label>
                                <input type="date" name="edit_scheduled_date" id="edit_scheduled_date"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required></input>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_priority"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Priority*</label>
                                <select name="edit_priority" id="edit_priority"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" disabled selected>Select priority</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                    <option value="4">Critical</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_technician"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Technician</label>
                                <input type="text" name="edit_technician" id="edit_technician"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Technician name"></input>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_notes"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Remarks</label>
                                <textarea name="edit_notes" id="edit_notes" rows="3"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="Any additional information or special instructions"></textarea>
                            </div>

                        </div>

                        <button type="submit"
                            class="btn-Update mt-2 text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            {{-- <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg> --}}
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
                width: '100%'
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
            if (confirm(
                    `Are you sure you want to mark maintenance ${maintenanceCode} as in-progress?`
                )) {
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
