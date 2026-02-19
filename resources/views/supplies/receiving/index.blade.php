@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Recieve Supplies</h1>
                <p class="text-sm text-gray-500">
                    Receive supplies and update inventory.
                </p>
            </div>
            <div class="flex items-center gap-2 mt-0">
                <a href="{{ route('supplies.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                <a href ="{{ route('receiving.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Receive Item
                </a>
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
            <div class="flex flex-col md:flex-row gap-2 text-xs md:text-sm">
                <div class="md:w-2/3 w-full">
                    <input type="text" id="simple-search" name="search" placeholder="Search by name or description..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        value = "{{ request()->query('search') }}" oninput="this.form.submit()">
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="searchsupplier" name="searchsupplier"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ request('searchsupplier') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="searchemployee" name="searchemployee" tooltip="Filter by employee who received the items"
                        data-tooltip-placement="top"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Employees</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ request('searchemployee') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- <div class="md:w-1/3 w-full">
                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>In-progress</option>
                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Completed</option>
                        <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Overdue</option>
                        <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div> --}}

            </div>
            <button type="submit"
                class="hidden mt-4 w-full shrink-0 rounded-lg bg-gray-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 sm:mt-0 sm:w-auto">Search</button>
        </form>

        {{-- Table --}}
        <div class="bg-white border rounded-xl overflow-x-auto md:overflow-visible scroll-smooth">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Code</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Description</th>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Date Created</th>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Date Received</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Reference</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Supplier</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Received By</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($receivings as $receiving)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 w-[120px]">{{ $receiving->transaction_number }}</td>
                            <td class="px-4 py-3 w-[150px]">{{ $receiving->description }}</td>
                            <td class="px-4 py-3 w-[120px]">
                                {{ Carbon::parse($receiving->created_at)->format(format: 'Y-m-d') }}</td>
                            <td class="px-4 py-3 w-[120px]">
                                {{ Carbon::parse($receiving->received_date)->format(format: 'Y-m-d') }}</td>
                            <td class="px-4 py-3 w-[150px]">{{ $receiving->reference }}</td>
                            <td class="px-4 py-3 w-[150px]">{{ $receiving->supplier->name }}</td>
                            <td class="px-4 py-3 w-[150px]">
                                {{ $receiving->received_by ? $receiving->receiver->last_name . ', ' . $receiving->receiver->first_name . ' ' . $receiving->receiver->middle_name : '' }}
                            </td>
                            <td class="px-4 py-3 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
                                    <button type="button" title="View Receiving : {{ $receiving->transaction_number }}"
                                        data-modal-target="view-modal" data-modal-toggle="view-modal"
                                        onclick="viewReceiving({{ $receiving->id }})"
                                        class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path
                                                d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                            <path
                                                d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                        </svg>
                                        {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                    </button>

                                    <a href="{{ route('receiving.print', $receiving->id) }}" type="button" target="_blank"
                                        title="Print receipt : {{ $receiving->transaction_number }}"
                                        class="group flex space-x-1 text-gray-500 hover:text-yellow-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                            <path
                                                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                        </svg>
                                    </a>

                                    @if ($receiving->status == 2)
                                        <button type="button"
                                            title="Voided already : {{ $receiving->transaction_number }}" disabled
                                            class="group flex space-x-1 text-gray-300 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                <path
                                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                            {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                        </button>
                                    @else
                                        <button type="button"
                                            title="Void Receiving : {{ $receiving->transaction_number }}"
                                            data-id="{{ $receiving->id }}"
                                            data-code="{{ $receiving->transaction_number }}"
                                            data-description="{{ $receiving->description }}"
                                            onclick="voidReceiving(this)"
                                            class="group flex space-x-1 text-gray-500 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                <path
                                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                            {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No received items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $receivings->links() }}
        </div>
    </div>

    <!-- Modal  View-->
    <div id="view-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 name="formLabel" id="formLabel" class="text-lg font-semibold text-gray-900 dark:text-white">

                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="view-modal">
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
                    <form id="viewForm" class="p-4 md:p-5" method="POST">
                        @csrf
                        <input type="hidden" name="view_id" id="view_id">

                        <!-- MAIN 2 COLUMN LAYOUT -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            <!-- ================= LEFT SIDE (HEADER INFO) ================= -->
                            <div>
                                <h3 class="text-sm font-semibold mb-3">Receipt Information</h3>

                                <!-- HEADER 2 COLUMN GRID -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                    <div>
                                        <label class="block text-xs font-medium">Receipt No</label>
                                        <input type="text" id="view_code"
                                            class="w-full text-xs rounded-lg border p-2.5">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium">Receipt Date</label>
                                        <input type="date" id="view_date"
                                            class="w-full text-xs rounded-lg border p-2.5">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium">Description</label>
                                        <input type="text" id="view_description"
                                            class="w-full text-xs rounded-lg border p-2.5">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium">Reference</label>
                                        <input type="text" id="view_reference"
                                            class="w-full text-xs rounded-lg border p-2.5">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium">Supplier</label>
                                        <input type="text" id="view_supplier"
                                            class="w-full text-xs rounded-lg border p-2.5">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium">Received By</label>
                                        <input type="text" id="view_received_by"
                                            class="w-full text-xs rounded-lg border p-2.5">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium">Remarks</label>
                                        <textarea id="view_remarks" rows="3" class="w-full text-xs rounded-lg border p-2.5"></textarea>
                                    </div>

                                </div>
                            </div>


                            <!-- ================= RIGHT SIDE (DETAILS TABLE) ================= -->
                            <div>
                                <div class="border rounded-lg p-3">

                                    <h3 class="text-sm font-semibold mb-2">Receipt Details</h3>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs text-left border">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-3 py-2 border">Item</th>
                                                    <th class="px-3 py-2 border">Qty</th>
                                                    <th class="px-3 py-2 border">UOM</th>
                                                    <th class="px-3 py-2 border">Unit Cost</th>
                                                    <th class="px-3 py-2 border">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="viewDetailsTable">

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="mt-6">
                            <button type="button" id="closeButton" data-modal-toggle="view-modal"
                                class="px-4 py-2 text-xs border rounded-lg bg-gray-100 hover:bg-gray-200">
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

        function viewReceiving(id) {

            $.ajax({
                url: '/receiving/' + id,
                type: 'GET',
                success: function(response) {

                    if (response.success) {

                        let data = response.data;
                        $(document.getElementById('formLabel')).text(
                            `View Receiving Details ${data.status == 2 ? '(Voided)' : ''}`
                        );
                        // ================= HEADER =================
                        $('#view_id').val(data.id);
                        $('#view_code').val(data.transaction_number);
                        $('#view_date').val(formatDateForInput(data.received_date));
                        $('#view_reference').val(data.reference);
                        $('#view_description').val(data.description);
                        $('#view_supplier').val(data.supplier ? data.supplier.name : '');
                        $('#view_received_by').val(data.received_by ? data.receiver.last_name + ', ' + data
                            .receiver.first_name + ' ' + data.receiver.middle_name : '');
                        $('#view_remarks').val(data.remarks);

                        // ================= DETAILS TABLE =================
                        let rows = '';
                        let grandTotal = 0;

                        data.details.forEach(function(item) {

                            grandTotal += parseFloat(item.total_price);

                            rows += `
                        <tr>
                            <td class="border px-3 py-2">${item.product.name}</td>
                            <td class="border px-3 py-2 text-right">${parseFloat(item.quantity).toFixed(2)}</td>
                            <td class="border px-3 py-2">${item.uom.name}</td>
                            <td class="border px-3 py-2 text-right">${parseFloat(item.unit_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td class="border px-3 py-2 text-right">${parseFloat(item.total_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        </tr>
                    `;
                        });
                        rows += `
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="4" class="border px-3 py-2 text-right">Grand Total</td>
                        <td class="border px-3 py-2 text-right">${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    </tr>
                `;

                        $('#viewDetailsTable').html(rows);

                        // Open modal
                        $('#view-modal').removeClass('hidden');
                    }
                }
            });

        }

        function voidReceiving(button) {
            const receivingId = button.getAttribute('data-id');
            const description = button.getAttribute('data-description');
            const code = button.getAttribute('data-code');

            if (confirm(
                    `Are you sure you want to void the receiving: "${code} - ${description}"? This action cannot be undone.`
                )) {
                $.ajax({
                    url: `/receiving/${receivingId}/void`,
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
