@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
    @endphp
    <div class="p-6 space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Budget Management</h1>
                <p class="text-sm text-gray-500">
                    Manage and track budget allocations and expenditures.
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

                <a @if (!$canCreate) disabled @endif href="{{ route('budget.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white{{ $canCreate ? ' bg-gray-900 hover:bg-gray-800' : ' bg-gray-600 hover:bg-gray-600 cursor-not-allowed' }} rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Budget Request
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Total Requests',
                    'value' => $totalRequests,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" class = "w-5 h-5 text-blue-600" width="16" height="16" fill="currentColor" class="bi bi-clipboard2" viewBox="0 0 16 16">
                        <path d="M3.5 2a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-12a.5.5 0 0 0-.5-.5H12a.5.5 0 0 1 0-1h.5A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1H4a.5.5 0 0 1 0 1z"/>
                        <path d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5"/>
                        </svg>',
                ],
                [
                    'title' => 'Pending',
                    'value' => $pendingRequests,
                    'color' => 'yellow',
                    'icon' => '
                        <svg class="w-5 h-5 text-yellow-600" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                        </svg>',
                ],
                [
                    'title' => 'Overdue',
                    'value' => $overdueRequests,
                    'color' => 'red',
                    'icon' => '
                        <svg class="w-5 h-5 text-red-600" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>',
                ],
                [
                    'title' => 'Completed',
                    'value' => $completedRequests,
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
                    <input type="text" id="simple-search" name="search" placeholder="Search by name or description..."
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
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Submitted</option>
                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Completed</option>
                        <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Overdue</option>
                        <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Cancelled</option>
                        <option value="5" {{ request('status') === '5' ? 'selected' : '' }}>Approved</option>
                        <option value="6" {{ request('status') === '6' ? 'selected' : '' }}>Rejected</option>
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
                        <th scope="col" class="px-4 py-3 text-left w-[90px]">Request No</th>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Date</th>
                        <th scope="col" class="px-4 py-3 text-left w-[180px]">Description</th>
                        <th scope="col" class="px-4 py-3 text-left w-[200px]">Location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Requested By</th>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Amount</th>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-left w-[130px]">Approver</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Current Approver</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($budgets as $budget)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 w-[90px]">{{ $budget->apv_no }}</td>
                            <td class="px-4 py-3 w-[120px]">{{ Carbon::parse($budget->requested_at)->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3 w-[180px]">{{ $budget->purpose }}</td>
                            <td class="px-4 py-3 w-[200px]">{{ $budget->location ? $budget->location->name : '' }}
                            </td>
                            <td class="px-4 py-3 w-[120px]">
                                {{ $budget->requester ? $budget->requester->lname . ', ' . $budget->requester->fname . ' ' . $budget->requester->mname : '' }}
                            </td>
                            <td class="px-4 py-3 w-[120px]">
                                {{ number_format($budget->total_amount, 2) }}</td>
                            {{-- <td class="px-4 py-3 w-[100px]">
                                @php
                                    $statusLabels = [
                                        0 => 'Pending',
                                        1 => 'Submitted',
                                        2 => 'Approved',
                                        3 => 'Rejected',
                                        4 => 'Cancelled',
                                    ];
                                @endphp
                                {{ $statusLabels[$budget->status] ?? 'Unknown' }}
                            </td> --}}
                            <td class="px-4 py-3 w-[120px] text-xs font-semibold">
                                @php
                                    $statuses = [
                                        0 => ['color' => 'bg-yellow-100 text-yellow-600', 'label' => 'Pending'],
                                        1 => ['color' => 'bg-blue-100 text-blue-700', 'label' => 'In-progress'],
                                        2 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Completed'],
                                        3 => ['color' => 'bg-red-100 text-red-700', 'label' => 'Overdue'],
                                        4 => ['color' => 'bg-gray-100 text-gray-600', 'label' => 'Cancelled'],
                                    ];
                                    $status = $statuses[$budget->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                @if ($budget->submitted_at?->copy()->addDays(7) < now() && $budget->status == 1)
                                    @php
                                        $status = $statuses[3]; // Overdue
                                    @endphp
                                @endif

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-left w-[130px]">
                                @if ($budget->status == '0')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-600">Not
                                        Submitted</span>
                                @endif
                                @if ($budget->status == '2' && $budget->approvalHistory->last()->approved == '0')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600">Rejected</span>
                                @elseif ($budget->status == '2' && $budget->approvalHistory->last()->approved == '1')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">Approved</span>
                                @endif

                            </td>
                            <td class="px-4 py-3 w-[150px]">
                                {{ $budget->budgetApprover ? $budget->budgetApprover->name : '' }}
                            </td>

                            {{-- <td class="px-4 py-3 text-left w-[120px]">
                                @if ($budget->status == '0')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-600">Not
                                        Submitted</span>
                                @endif
                                @if ($budget->status == '2' && $budget->approvalHistory->last()->approved == '0')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600">Rejected</span>
                                @elseif ($budget->status == '2' && $budget->approvalHistory->last()->approved == '1')
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">Approved</span>
                                @endif

                            </td> --}}
                            <td class="px-4 py-3 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('budget.show', $budget->id) }}" type="button"
                                        title="Edit budget request : {{ $budget->apv_no }}"
                                        class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path
                                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd"
                                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('budget.print', $budget->id) }}" type="button" target="_blank"
                                        title="Print request : {{ $budget->id }}"
                                        class="group flex space-x-1 text-gray-500 hover:text-yellow-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                            <path
                                                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                        </svg>
                                    </a>

                                    <button type="button" title="View approval history : {{ $budget->apv_no }}"
                                        data-modal-target="view-approval-modal" data-modal-toggle="view-approval-modal"
                                        data-id="{{ $budget->id }}" data-apv_no="{{ $budget->apv_no }}"
                                        data-description="{{ $budget->purpose }}" onclick="openApprovalModal(this)"
                                        class="group flex space-x-1 text-gray-500 hover:text-indigo-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-diagram-3-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H14a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 2 7h5.5V6A1.5 1.5 0 0 1 6 4.5zm-6 8A1.5 1.5 0 0 1 1.5 10h1A1.5 1.5 0 0 1 4 11.5v1A1.5 1.5 0 0 1 2.5 14h-1A1.5 1.5 0 0 1 0 12.5zm6 0A1.5 1.5 0 0 1 7.5 10h1a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5z" />
                                        </svg>
                                    </button>

                                    <button type="button" @if (!$canCreate || $budget->status != 0 || Auth::user()->id != $budget->requested_by) disabled @endif
                                        title="Submit for approval : {{ $budget->apv_no }}"
                                        class="group flex space-x-1 text-gray-500 hover:text-green-600 transition-colors {{ !$canCreate && $budget->status == 0 && Auth::user()->id == $budget->requested_by ? ' cursor-not-allowed' : '' }}"
                                        onclick="submitForApproval({{ $budget->id }}, '{{ $budget->apv_no }}' )">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-send-check-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" />
                                            <path
                                                d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686" />
                                        </svg>
                                    </button>

                                    <button type="button" title="Approve request : {{ $budget->apv_no }}"
                                        @if ($budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver) disabled @endif
                                        class="group flex space-x-1 text-gray-500 hover:text-green-600 transition-colors {{ $budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver ? ' cursor-not-allowed' : '' }}"
                                        onclick="approveRequest({{ $budget->id }}, '{{ $budget->apv_no }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                            <path
                                                d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                        </svg>
                                    </button>

                                    <button type="button" title="Reject request : {{ $budget->apv_no }}"
                                        @if ($budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver) disabled @endif
                                        class="group flex space-x-1 text-gray-500 hover:text-red-600 transition-colors {{ $budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver ? ' cursor-not-allowed' : '' }}"
                                        onclick="rejectRequest({{ $budget->id }}, '{{ $budget->apv_no }}' )">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path
                                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 1 1 .708.708L8.707 8l2.647 2.646a.5.5 0 1 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 1 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                    </button>

                                    <button type="button" @if (Auth::user()->id != $budget->requested_by || !in_array($budget->status, [0, 1])) disabled @endif
                                        title="Void request : {{ $budget->apv_no }}"
                                        class="group flex space-x-1 text-gray-500 hover:text-red-600 transition-colors {{ Auth::user()->id != $budget->requested_by || !in_array($budget->status, [0, 1]) ? ' cursor-not-allowed' : '' }}"
                                        onclick="voidBudget({{ $budget->id }}, '{{ $budget->apv_no }}' )">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                        </svg>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No budget requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $budgets->links() }}
        </div>
    </div>

    <!-- View approval modal -->
    <div id="view-approval-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Approval History
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="view-approval-modal">
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
                        <input type="hidden" name="budget_id" id="budget_id">
                        <div name="apv_no" id="apv_no" class="mb-2 text-md font-semibold"></div>
                        <div name="description" id="description" class="mb-2 text-md font-semibold"></div>
                        <div class="overflow-y-auto max-h-[70vh]">
                            <table class="min-w-full text-xs border rounded-xl">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-2 py-2 text-left">Location</th>
                                        <th class="px-2 py-2 text-left">Approver</th>
                                        <th class="px-2 py-2 text-left">Action</th>
                                        <th class="px-2 py-2 text-left">Date</th>
                                        <th class="px-2 py-2 text-left">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="approvalTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-gray-500">
                                            Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End view approval modal -->

    <script>
        $(document).ready(function() {
            $('#employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });
        });

        function submitForApproval(id, requestNumber) {
            if (confirm(
                    `Are you sure you want to submit budget request ${requestNumber} for approval?`
                )) {
                $.ajax({
                    url: `/budget/${id}/submit`,
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

        function approveRequest(id, requestNumber) {
            if (confirm(
                    `Are you sure you want to approve budget request ${requestNumber}?`
                )) {
                let remarks = prompt("Enter remarks/comments (optional):");

                if (remarks === null) {
                    return;
                }

                $.ajax({
                    url: `/budget/${id}/approve`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remarks: remarks
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

        function rejectRequest(id, requestNumber) {
            if (confirm(
                    `Are you sure you want to reject budget request ${requestNumber}?`
                )) {
                let remarks = prompt("Enter remarks/comments for rejection (optional):");

                if (remarks === null) {
                    return;
                }

                $.ajax({
                    url: `/budget/${id}/reject`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remarks: remarks
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

        function voidBudget(id, requestNumber) {
            if (confirm(
                    `You will not be able to edit the data once voided. Are you sure you want to void budget request ${requestNumber}?`
                )) {
                $.ajax({
                    url: `/budget/${id}/void`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while voiding the budget request.');
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
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_description').value = button.getAttribute('data-description');
            document.getElementById('edit_status').value = button.getAttribute('data-status');

            const form = document.getElementById('editForm');
            // form.action = `license/${id}`;
        }

        function openApprovalModal(button) {
            const id = button.getAttribute('data-id');
            const apv_no = button.getAttribute('data-apv_no');
            const description = button.getAttribute('data-description');

            document.getElementById('budget_id').value = id;
            document.getElementById('apv_no').textContent = `Request No: ${apv_no}`;
            document.getElementById('description').textContent = `Description: ${description}`;

            // Fetch approval history via AJAX
            $.ajax({
                url: `/budget/${id}/approval-history`,
                method: 'GET',
                success: function(response) {
                    const tbody = document.getElementById('approvalTableBody');
                    tbody.innerHTML = '';

                    if (response.history.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    No approval history found.
                                </td>
                            </tr>
                        `;
                    } else {
                        response.history.forEach(record => {
                            const actionBadge =
                                record.action === 'Rejected' ?
                                `
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 1 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 1 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                        Rejected
                                    </span>
                                ` :
                                record.action === 'Approved' ?
                                `
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M13.78 4.22a.75.75 0 0 0-1.06 0L6.5 10.44l-2.72-2.72a.75.75 0 1 0-1.06 1.06l3.25 3.25a.75.75 0 0 0 1.06 0l7.25-7.25a.75.75 0 0 0 0-1.06z" />
                                        </svg>
                                        Approved
                                    </span>
                                ` :
                                'N/A';

                            const row = `
                                <tr>
                                    <td class="px-2 py-2">${record.location}</td>
                                    <td class="px-2 py-2">${record.approver}</td>
                                    <td class="px-2 py-2">${actionBadge}</td>
                                    <td class="px-2 py-2">${record.date}</td>
                                    <td class="px-2 py-2">${record.remarks || ''}</td>
                                </tr>
                            `;
                            tbody.insertAdjacentHTML('beforeend', row);
                        });
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while fetching approval history.');
                }
            });
        }
    </script>
@endsection
