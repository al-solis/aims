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
                        <h1>Budget Management</h1>
                        <p>Manage and track budget allocations and expenditures.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a @if (!$canCreate) disabled @endif href="{{ route('budget.create') }}"
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Budget Request
                        </a>
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
                    <div class="md:w-2/3 w-full">
                        <input type="text" name="search" placeholder="Search by name or description..." class="ft-field"
                            value="{{ request()->query('search') }}" oninput="this.form.submit()">
                    </div>
                    <div class="md:w-1/3 w-full flex flex-col md:flex-row gap-3">
                        <select name="searchloc" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ request('searchloc') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="ft-field flex-1" onchange="this.form.submit()">
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
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" style="min-width: 90px;">Request No</th>
                                <th scope="col" style="min-width: 110px;">Date</th>
                                <th scope="col" style="min-width: 180px;">Description</th>
                                <th scope="col" style="min-width: 180px;">Location</th>
                                <th scope="col" style="min-width: 140px;">Requested By</th>
                                <th scope="col" style="min-width: 100px;">Amount</th>
                                <th scope="col" style="min-width: 110px;">Status</th>
                                <th scope="col" style="min-width: 120px;">Approver</th>
                                <th scope="col" style="min-width: 150px;">Current Approver</th>
                                <th scope="col" class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($budgets as $budget)
                                <tr>
                                    <td class="whitespace-nowrap align-middle font-semibold" style="color: var(--ft-ink);">
                                        {{ $budget->apv_no }}
                                    </td>
                                    <td class="whitespace-nowrap align-middle" style="color: var(--ft-text);">
                                        {{ Carbon::parse($budget->requested_at)->format('Y-m-d') }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $budget->purpose }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $budget->location ? $budget->location->name : '' }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $budget->requester ? $budget->requester->lname . ', ' . $budget->requester->fname . ' ' . $budget->requester->mname : '' }}
                                    </td>
                                    <td class="whitespace-nowrap align-middle tabular-nums" style="color: var(--ft-text);">
                                        {{ number_format($budget->total_amount, 2) }}
                                    </td>
                                    <td class="align-middle">
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
                                            $status = $statuses[$budget->status] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];

                                            if (
                                                $budget->submitted_at?->copy()->addDays(7) < now() &&
                                                $budget->status == 1
                                            ) {
                                                $status = $statuses[3]; // Overdue
                                            }
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }}"
                                            style="{{ $status['style'] ?? '' }} whitespace-nowrap;">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if ($budget->status == '0')
                                            <span class="ft-badge"
                                                style="background: var(--ft-warning-bg); color: var(--ft-warning-text);">Not
                                                Submitted</span>
                                        @endif
                                        @if ($budget->status == '2' && $budget->approvalHistory->last()->approved == '0')
                                            <span class="ft-badge-no">Rejected</span>
                                        @elseif ($budget->status == '2' && $budget->approvalHistory->last()->approved == '1')
                                            <span class="ft-badge-ok">Approved</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $budget->budgetApprover ? $budget->budgetApprover->name : '—' }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('budget.show', $budget->id) }}"
                                                title="Edit budget request : {{ $budget->apv_no }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('budget.print', $budget->id) }}" target="_blank"
                                                title="Print request : {{ $budget->id }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                                    <path
                                                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                                </svg>
                                            </a>

                                            <button type="button" title="View approval history : {{ $budget->apv_no }}"
                                                data-modal-target="view-approval-modal"
                                                data-modal-toggle="view-approval-modal" data-id="{{ $budget->id }}"
                                                data-apv_no="{{ $budget->apv_no }}"
                                                data-description="{{ $budget->purpose }}"
                                                onclick="openApprovalModal(this)"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H14a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 2 7h5.5V6A1.5 1.5 0 0 1 6 4.5zm-6 8A1.5 1.5 0 0 1 1.5 10h1A1.5 1.5 0 0 1 4 11.5v1A1.5 1.5 0 0 1 2.5 14h-1A1.5 1.5 0 0 1 0 12.5zm6 0A1.5 1.5 0 0 1 7.5 10h1a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5z" />
                                                </svg>
                                            </button>

                                            <button type="button" @if (!$canCreate || $budget->status != 0 || Auth::user()->id != $budget->requested_by) disabled @endif
                                                title="Submit for approval : {{ $budget->apv_no }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ !$canCreate && $budget->status == 0 && Auth::user()->id == $budget->requested_by ? ' cursor-not-allowed' : '' }}"
                                                onclick="submitForApproval({{ $budget->id }}, '{{ $budget->apv_no }}' )">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" />
                                                    <path
                                                        d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686" />
                                                </svg>
                                            </button>

                                            <button type="button" title="Approve request : {{ $budget->apv_no }}"
                                                @if ($budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver) disabled @endif
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ $budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver ? ' cursor-not-allowed' : '' }}"
                                                onclick="approveRequest({{ $budget->id }}, '{{ $budget->apv_no }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                                                    <path
                                                        d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
                                                </svg>
                                            </button>

                                            <button type="button" title="Reject request : {{ $budget->apv_no }}"
                                                @if ($budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver) disabled @endif
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ $budget->status != 1 || Auth::user()->getDepartmentAttribute() != $budget->current_approver ? ' cursor-not-allowed' : '' }}"
                                                onclick="rejectRequest({{ $budget->id }}, '{{ $budget->apv_no }}' )">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path
                                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 1 1 .708.708L8.707 8l2.647 2.646a.5.5 0 1 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 1 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                </svg>
                                            </button>

                                            <button type="button" @if (Auth::user()->id != $budget->requested_by || !in_array($budget->status, [0, 1])) disabled @endif
                                                title="Void request : {{ $budget->apv_no }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors {{ Auth::user()->id != $budget->requested_by || !in_array($budget->status, [0, 1]) ? ' cursor-not-allowed' : '' }}"
                                                onclick="voidBudget({{ $budget->id }}, '{{ $budget->apv_no }}' )">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="ft-empty">No budget requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $budgets->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- View approval modal -->
    <div id="view-approval-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-3xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Approval History
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
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
                <div class="p-4 overflow-y-auto max-h-[75vh]">
                    <form action="" method="put">
                        @csrf
                        <input type="hidden" name="budget_id" id="budget_id">
                        <div class="mb-4">
                            <div id="apv_no" class="text-sm font-semibold mb-1" style="color: var(--ft-ink);"></div>
                            <div id="description" class="text-sm" style="color: var(--ft-muted);"></div>
                        </div>

                        <div class="ft-table-wrap">
                            <table class="ft-table">
                                <thead>
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
                                        <td colspan="5" class="text-center py-4" style="color: var(--ft-muted);">
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
            if (confirm(`Are you sure you want to submit budget request ${requestNumber} for approval?`)) {
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
            if (confirm(`Are you sure you want to approve budget request ${requestNumber}?`)) {
                let remarks = prompt("Enter remarks/comments (optional):");
                if (remarks === null) return;

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
            if (confirm(`Are you sure you want to reject budget request ${requestNumber}?`)) {
                let remarks = prompt("Enter remarks/comments for rejection (optional):");
                if (remarks === null) return;

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
            const form = document.querySelector('form');
            form.reset();

            setTimeout(() => {
                const successMessage = document.querySelector('[data-success]');
                if (successMessage) {
                    successMessage.remove();
                }
            }, 3000);
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
                                <td colspan="5" class="text-center py-4" style="color: var(--ft-muted);">
                                    No approval history found.
                                </td>
                            </tr>
                        `;
                    } else {
                        response.history.forEach(record => {
                            const actionBadge =
                                record.action === 'Rejected' ?
                                `<span class="ft-badge-no"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" /><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 1 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 1 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" /></svg> Rejected</span>` :
                                record.action === 'Approved' ?
                                `<span class="ft-badge-ok"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16"><path d="M13.78 4.22a.75.75 0 0 0-1.06 0L6.5 10.44l-2.72-2.72a.75.75 0 1 0-1.06 1.06l3.25 3.25a.75.75 0 0 0 1.06 0l7.25-7.25a.75.75 0 0 0 0-1.06z" /></svg> Approved</span>` :
                                'N/A';

                            const row = `
                                <tr>
                                    <td>${record.location}</td>
                                    <td>${record.approver}</td>
                                    <td>${actionBadge}</td>
                                    <td class="whitespace-nowrap">${record.date}</td>
                                    <td>${record.remarks || '—'}</td>
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
