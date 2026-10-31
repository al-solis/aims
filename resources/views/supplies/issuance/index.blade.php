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
                        <h1>Supplies Issuance</h1>
                        <p>Issue supplies to employees and update inventory.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('supplies.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        <a href="{{ route('issuance.create') }}" @if (!$canCreate) disabled @endif
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Issue Supplies
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
                    <div class="md:w-2/3 w-full">
                        <input type="text" name="search" placeholder="Search by name or description..." class="ft-field"
                            value="{{ request()->query('search') }}" oninput="this.form.submit()">
                    </div>
                    <div class="md:w-1/3 w-full flex gap-3">
                        <select name="searchemployee" class="ft-field flex-1" onchange="this.form.submit()"
                            title="Filter by employee">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ request('searchemployee') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="searchlocation" class="ft-field flex-1" onchange="this.form.submit()"
                            title="Filter by location">
                            <option value="">All Locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ request('searchlocation') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[120px]">Code</th>
                                <th scope="col" class="w-[180px]">Purpose</th>
                                <th scope="col" class="w-[120px]">Date Created</th>
                                <th scope="col" class="w-[120px]">Date Issued</th>
                                <th scope="col" class="w-[150px]">Issued To</th>
                                <th scope="col" class="w-[150px]">Location</th>
                                <th scope="col" class="w-[150px]">Received By</th>
                                <th scope="col" class="text-center w-[50px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($issuances as $issuance)
                                <tr>
                                    <td class="whitespace-nowrap align-middle font-semibold" style="color: var(--ft-ink);">
                                        {{ $issuance->issuance_number }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $issuance->purpose }}
                                    </td>
                                    <td class="whitespace-nowrap align-middle" style="color: var(--ft-muted);">
                                        {{ Carbon::parse($issuance->created_at)->format('Y-m-d') }}
                                    </td>
                                    <td class="whitespace-nowrap align-middle" style="color: var(--ft-muted);">
                                        {{ Carbon::parse($issuance->issuance_date)->format('Y-m-d') }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $issuance->issued_to ? $issuance->issuedTo->last_name . ', ' . $issuance->issuedTo->first_name . ' ' . $issuance->issuedTo->middle_name : '' }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-muted);">
                                        {{ $issuance->location ? $issuance->location->name : '' }}
                                    </td>
                                    <td class="align-middle" style="color: var(--ft-text);">
                                        {{ $issuance->received_by ? $issuance->receiver->last_name . ', ' . $issuance->receiver->first_name . ' ' . $issuance->receiver->middle_name : '' }}
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" title="View Issuance : {{ $issuance->issuance_number }}"
                                                data-modal-target="view-modal" data-modal-toggle="view-modal"
                                                onclick="viewIssuance({{ $issuance->id }})"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                                    <path
                                                        d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                                </svg>
                                            </button>

                                            <a href="{{ route('issuance.print', $issuance->id) }}" target="_blank"
                                                title="Print issuance : {{ $issuance->issuance_number }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-yellow-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                                    <path
                                                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('issuance.print-transmittal', $issuance->id) }}"
                                                target="_blank"
                                                title="Print transmittal : {{ $issuance->issuance_number }}"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                                    <path
                                                        d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z" />
                                                    <path
                                                        d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z" />
                                                </svg>
                                            </a>

                                            @if ($issuance->status == 0 || !$canUpdate)
                                                <button type="button"
                                                    title="{{ !$canUpdate ? 'Unable to void' : 'Voided already' }} : {{ $issuance->issuance_number }}"
                                                    disabled class="p-1.5 rounded text-gray-300 cursor-not-allowed">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button"
                                                    title="Void Issuance : {{ $issuance->issuance_number }}"
                                                    data-id="{{ $issuance->id }}"
                                                    data-code="{{ $issuance->issuance_number }}"
                                                    data-description="{{ $issuance->purpose }}"
                                                    onclick="voidIssuance(this)"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="ft-empty">No issued items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!$canCreate && !$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create or update supplies issuance information.
                    </p>
                @elseif (!$canCreate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create supplies issuance information.
                    </p>
                @elseif (!$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to update supplies issuance information.
                    </p>
                @endif

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $issuances->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View -->
    <div id="view-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-5xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 name="formLabel" id="formLabel" class="text-base font-bold text-[var(--ft-ink)]">
                        View Issuance Details
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="view-modal">
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
                    <form id="viewForm" method="POST">
                        @csrf
                        <input type="hidden" name="view_id" id="view_id">

                        <!-- MAIN 2 COLUMN LAYOUT -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            <!-- ================= LEFT SIDE (HEADER INFO) ================= -->
                            <div>
                                <h3 class="ft-section-title" style="margin-top: 0;">Issuance Information</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="ft-label">Issuance No</label>
                                        <input type="text" id="view_code" class="ft-field"
                                            style="background: var(--ft-mist);" readonly>
                                    </div>
                                    <div>
                                        <label class="ft-label">Issuance Date</label>
                                        <input type="date" id="view_date" class="ft-field"
                                            style="background: var(--ft-mist);" readonly>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="ft-label">Purpose</label>
                                        <input type="text" id="view_purpose" class="ft-field"
                                            style="background: var(--ft-mist);" readonly>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label class="ft-label">Issued To</label>
                                        <input type="text" id="view_issued_to" class="ft-field"
                                            style="background: var(--ft-mist);" readonly>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label class="ft-label">Location</label>
                                        <input type="text" id="view_location" class="ft-field"
                                            style="background: var(--ft-mist);" readonly>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="ft-label">Remarks</label>
                                        <textarea id="view_remarks" rows="3" class="ft-field" style="background: var(--ft-mist);" readonly></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= RIGHT SIDE (DETAILS TABLE) ================= -->
                            <div>
                                <h3 class="ft-section-title" style="margin-top: 0;">Issuance Details</h3>
                                <div class="ft-table-wrap" style="max-height: 400px; overflow-y: auto;">
                                    <table class="ft-table">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-right">Qty</th>
                                                <th>UOM</th>
                                                <th class="text-right">Unit Cost</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="viewDetailsTable">
                                            {{-- JS populated --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" data-modal-toggle="view-modal" class="ft-btn ft-btn-ghost">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End view modal -->

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

        function formatDateForInput(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date)) return '';

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function viewIssuance(id) {
            $.ajax({
                url: '/issuance/' + id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        $(document.getElementById('formLabel')).text(
                            `View Issuance Details ${data.status == 0 ? '(Voided)' : ''}`
                        );
                        // ================= HEADER =================
                        $('#view_id').val(data.id);
                        $('#view_code').val(data.issuance_number);
                        $('#view_date').val(formatDateForInput(data.issuance_date));
                        $('#view_purpose').val(data.purpose);
                        $('#view_reference').val(data.reference);
                        $('#view_issued_to').val(data.issued_to ? data.issued_to.last_name + ', ' + data
                            .issued_to.first_name + ' ' + data.issued_to.middle_name : '');
                        $('#view_location').val(data.location ? data.location.name : '');
                        $('#view_remarks').val(data.remarks);

                        // ================= DETAILS TABLE =================
                        let rows = '';
                        let grandTotal = 0;

                        data.details.forEach(function(item) {
                            grandTotal += parseFloat(item.total_cost);

                            rows += `
                            <tr>
                                <td class="align-middle font-semibold" style="color: var(--ft-ink);">${item.supply.name}</td>
                                <td class="text-right tabular-nums align-middle" style="color: var(--ft-text);">${parseFloat(item.quantity).toFixed(2)}</td>
                                <td class="align-middle" style="color: var(--ft-muted);">${item.uom.name}</td>
                                <td class="text-right tabular-nums align-middle" style="color: var(--ft-text);">${parseFloat(item.unit_cost).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                <td class="text-right tabular-nums align-middle" style="color: var(--ft-text);">${parseFloat(item.total_cost).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            </tr>
                            `;
                        });

                        rows += `
                        <tr style="background: var(--ft-mist);">
                            <td colspan="4" class="text-right font-semibold align-middle" style="color: var(--ft-ink);">Grand Total</td>
                            <td class="text-right tabular-nums font-semibold align-middle" style="color: var(--ft-ink);">${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        </tr>
                        `;

                        $('#viewDetailsTable').html(rows);

                        // Open modal
                        $('#view-modal').removeClass('hidden');
                    }
                }
            });
        }

        function voidIssuance(button) {
            const issuanceId = button.getAttribute('data-id');
            const description = button.getAttribute('data-description');
            const code = button.getAttribute('data-code');

            if (confirm(
                    `Are you sure you want to void the issuance: "${code} - ${description}"? This action cannot be undone.`
                )) {
                $.ajax({
                    url: `/issuance/${issuanceId}/void`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            alert(xhr.responseJSON.message);
                        } else {
                            alert('An unexpected error occurred.');
                        }
                    }
                });
            }
        }
    </script>
@endsection
