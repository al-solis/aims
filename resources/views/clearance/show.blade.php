@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">
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

        <div class="bg-white rounded-xl shadow-xs p-3 sm:p-8">
            <div class="text-center mb-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Display Clearance Request
                </h2>
                <p class="text-sm text-gray-600">
                    Display employee clearance details.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">
            <div class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Employee Name</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $clearanceHeader->employee ? $clearanceHeader->employee->last_name . ', ' . $clearanceHeader->employee->first_name . ' ' . $clearanceHeader->employee->middle_name : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Department</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $clearanceHeader->employee && $clearanceHeader->employee->location ? $clearanceHeader->employee->location->description : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>


            {{-- <div class="mt-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Clearance Details</h3>
                @if ($clearanceHeader->clearance_details->isEmpty())
                    <p class="text-gray-600">No clearance details found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-200 text-gray-600">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left w-[120px]">Asset</th>
                                    <th scope="col" class="px-4 py-3 text-left w-[90px]">Qty</th>
                                    <th scope="col" class="px-4 py-3 text-left w-[180px]">Purchase</th>
                                    <th scope="col" class="px-4 py-3 text-left w-[150px]">Actual</th>
                                    <th scope="col" class="px-4 py-3 text-left w-[80px]">Status</th>
                                    <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($clearanceHeader->clearance_details as $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 w-[120px]">{{ $detail->asset ? $detail->asset->name : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 w-[90px]">{{ $detail->quantity }}</td>
                                        <td class="px-4 py-3 w-[180px]">{{ number_format($detail->purchase_cost, 2) }}</td>
                                        <td class="px-4 py-3 w-[150px]">
                                            {{ number_format($detail->total, 2) }}</td>
                                        <td class="px-4 py-3 w-[80px]">
                                            @if ($detail->status == 'Cleared')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M13.78 4.22a.75.75 0 0 0-1.06 0L6.5 10.44l-2.72-2.72a.75.75 0 1 0-1.06 1.06l3.25 3.25a.75.75 0 0 0 1.06 0l7.25-7.25a.75.75 0 0 0 0-1.06z" />
                                                    </svg>
                                                    Cleared
                                                </span>
                                            @elseif ($detail->status == 'Pending')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.75-11.75a.75.75 0 0 1-1.5 0V4a.75.75 0 0 1 1.5 0v.25zm-.75 2.25a.75.75 0 0 1-1.5 0V7a.75.75 0 0 1 1.5 0v.25z" />
                                                    </svg>
                                                    Pending
                                                </span>
                                            @elseif ($detail->status == 'Overdue')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.75-11.75a.75.75 0 0 1-1.5 0V4a.75.75 0 0 1 1.5 0v.25zm-.75 2.25a.75.75 0 0 1-1.5 0V7a.75.75 0 0 1 1.5 0v.25z" />
                                                    </svg>
                                                    Overdue
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div> --}}

            <form action="{{ route('clearance.update-details', $clearanceHeader->id) }}" method="POST" id="clearanceForm">
                @csrf
                @method('PUT')
                <div class="grid ml-1 mr-1 mt-2 gap-2 mb-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="type" class="block text-xs font-medium text-gray-900 dark:text-white">Request
                            Type*</label>
                        <select name="type" id="type"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option value="" disabled selected>Select request type</option>
                            <option value="1" {{ old('type', $clearanceHeader->type) == 1 ? 'selected' : '' }}>Transfer
                            </option>
                            <option value="2" {{ old('type', $clearanceHeader->type) == 2 ? 'selected' : '' }}>
                                Resignation</option>
                            <option value="3" {{ old('type', $clearanceHeader->type) == 3 ? 'selected' : '' }}>Contract
                                Ending</option>
                            <option value="4" {{ old('type', $clearanceHeader->type) == 4 ? 'selected' : '' }}>
                                Termination</option>
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="expected_date" class="block text-xs font-medium text-gray-900 dark:text-white">Expected
                            Date*</label>
                        <input type="date" name="expected_date" id="expected_date"
                            value="{{ old('expected_date', $clearanceHeader->expected_date ? Carbon::parse($clearanceHeader->expected_date)->format('Y-m-d') : '') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required></input>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="remarks"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Enter remarks">{{ old('remarks', $clearanceHeader->remarks) }}</textarea>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-4 mb-2">Clearance Details</h3>

                <div class="bg-white border rounded-xl overflow-x-auto overflow-y-auto md:overflow-visible scroll-smooth">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-200 text-gray-600">
                            <tr class="rounded-xl">
                                <th class="px-4 py-3 text-left w-[120px]">Asset</th>
                                <th class="px-4 py-3 text-left w-[120px]">Qty</th>
                                <th class="px-4 py-3 text-left w-[140px]">Purchase</th>
                                <th class="px-4 py-3 text-left w-[150px]">Actual</th>
                                <th class="px-4 py-3 text-left w-[140px]">Total</th>
                                <th class="px-4 py-3 text-left w-[140px]">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @foreach ($clearanceHeader->clearance_details as $detail)
                                <tr class="hover:bg-gray-50">
                                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">

                                    <td class="px-4 py-3 w-[120px]">
                                        {{ $detail->asset->name ?? 'N/A' }}
                                    </td>

                                    <td class="px-4 py-3 w-[120px]">
                                        <input type="number" min="1" step="1" name="qty[]" id="qty"
                                            class="qty w-full text-xs border rounded px-2 py-1"
                                            value="{{ $detail->quantity }}" readonly>
                                    </td>
                                    <td class="px-4 py-3 w-[140px]">{{ number_format($detail->purchase_cost, 2) }}</td>

                                    <td class="px-4 py-3 w-[150px]">
                                        <input type="number" step="0.01" name="actual[]"
                                            class="actual w-full text-xs border rounded px-2 py-1"
                                            value="{{ $detail->actual_cost ?? $detail->purchase_cost }}">
                                    </td>

                                    <!-- TOTAL -->
                                    <input type="hidden" name="total[]" class="total" value="{{ $detail->total }}">
                                    <td class="px-4 py-3 w-[140px]">
                                        <input type="text" class="total-display w-full text-xs border-none px-2 py-1"
                                            value="{{ number_format($detail->total, 2) }}" readonly>
                                    </td>

                                    <!-- ACTION / STATUS -->
                                    @php
                                        $statuses = [
                                            0 => 'Pending',
                                            1 => 'Returned',
                                            2 => 'Damaged',
                                            3 => 'Lost',
                                        ];
                                    @endphp

                                    <td class="px-4 py-3 w-[140px]">
                                        <select name="status[]" class="w-full text-xs border rounded px-2 py-1">
                                            @foreach ($statuses as $value => $label)
                                                <option value="{{ $value }}" @selected($detail->status == $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($clearanceHeader->clearance_details->isEmpty())
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-center text-gray-600">
                                        No asset accountability found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('clearance.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>
                    @if ($clearanceHeader->status != 2)
                        <button type="submit" id="saveBtn"
                            class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                            Save Changes
                        </button>
                    @endif
                </div>
        </div>

        <script>
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('actual')) {
                    const row = e.target.closest('tr');
                    const qty = parseFloat(row.querySelector('.qty').value) || 0;
                    const actual = parseFloat(e.target.value) || 0;
                    const total = qty * actual;

                    row.querySelector('.total').value = total.toFixed(2);

                    // Formatted display
                    row.querySelector('.total-display').value =
                        total.toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        });
                }
            });
        </script>

    @endsection
