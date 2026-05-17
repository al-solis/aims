@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Auth;
    @endphp
    <div class="max-w-2xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">
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
                    Budget Request
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Please fill out the form below to create a new budget request.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">

            <form method="POST" action="{{ $budget ? route('budget.update', $budget) : route('budget.store') }}"
                enctype="multipart/form-data" id="employeeForm">
                @csrf
                @if ($budget)
                    @method('PUT')
                @endif
                <div class="grid gap-2 mb-1 sm:grid-cols-4">
                    <div class="sm:col-span-4">
                        <label for="purpose" class="block mb-1 text-xs font-medium text-gray-900">Purpose</label>
                        <input type="text" name="purpose" id="purpose"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            value="{{ old('purpose', $budget->purpose ?? '') }}" placeholder="e.g. Office supplies"
                            required>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="location_id" class="block mb-1 text-xs font-medium text-gray-900">Location</label>
                        @if ($budget)
                            <input type="hidden" name="location_id" value="{{ $budget->location_id }}">
                        @endif
                        <select name="location_id_display" id="location_id" {{ $budget ? 'disabled' : '' }}
                            class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            {{ $budget ? '' : 'required' }}>
                            <option value="">Select a location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ old('location_id', $budget->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="remarks" class="block mb-1 text-xs font-medium text-gray-900">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Input remarks or notes">{{ old('remarks', $budget->remarks ?? '') }}</textarea>
                    </div>
                </div>
                <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0; mt-4">
                <div class="grid gap-2 mb-1 sm:grid-cols-4">
                    <div class="sm:col-span-1">
                        <label for="item" class="block mb-1 text-xs font-medium text-gray-900">Item Code</label>
                        <input type="text" name="item" id="item"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            value="{{ old('item', $budget->item ?? '') }}" placeholder="e.g. A4-PAPER-001">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block mb-1 text-xs font-medium text-gray-900">Description</label>
                        <input type="text" name="description" id="description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            value="{{ old('description', $budget->item_description ?? '') }}"
                            placeholder="e.g. Printer paper, A4 size">
                    </div>

                    <div class="sm:col-span-1">
                        <label for="unit_id" class="block mb-1 text-xs font-medium text-gray-900">UOM</label>
                        <select name="unit_id" id="unit_id"
                            class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                            <option value="">Select a unit</option>
                            @foreach ($uoms as $uom)
                                <option value="{{ $uom->id }}"
                                    {{ old('unit_id', $budget->unit_id ?? '') == $uom->id ? 'selected' : '' }}>
                                    {{ $uom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="qty" class="block mb-1 text-xs font-medium text-gray-900">Quantity</label>
                        <input type="number" name="qty" id="qty" min="1" step="1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            value="{{ old('qty', $budget->quantity ?? '') }}" placeholder="1">
                    </div>

                    <div class="sm:col-span-1">
                        <label for="price" class="block mb-1 text-xs font-medium text-gray-900">Price</label>
                        <input type="number" name="price" id="price" min="0" step="0.01"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            value="{{ old('price', $budget->unit_price ?? '') }}" placeholder="0.00">
                    </div>

                    <div class="sm:col-span-1 flex items-end">
                        <button type="button" id="addItemButton"
                            class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-plus" viewBox="0 0 16 16">
                                <path
                                    d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                            </svg>
                            Add Item
                        </button>
                    </div>
                </div>



                <table class="min-w-full text-xs mt-4">
                    <thead class="bg-gray-200 text-gray-600">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left w-[100px]">Item</th>
                            <th scope="col" class="px-4 py-3 text-left w-[200px]">Description</th>
                            <th scope="col" class="px-4 py-3 text-left w-[100px]">UOM</th>
                            <th scope="col" class="px-4 py-3 text-left w-[100px]">Quantity</th>
                            <th scope="col" class="px-4 py-3 text-left w-[100px]">Unit Price</th>
                            <th scope="col" class="px-4 py-3 text-left w-[120px]">Total</th>
                            <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="itemTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Item rows will be added here dynamically -->
                    </tbody>
                </table>

                <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">
                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('budget.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>

                    <button type="submit" id="saveButton" @if (($budget && $budget?->status != 0) || ($budget && Auth::user()->id != $budget?->requested_by)) disabled @endif
                        class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 {{ ($budget && $budget?->status != 0) || ($budget && Auth::user()->id != $budget?->requested_by) ? ' cursor-not-allowed' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-floppy" viewBox="0 0 16 16">
                            <path d="M11 2H9v3h2z" />
                            <path
                                d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                        </svg>
                        Save Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        @php
            $budgetItems = [];

            if ($budget && $budget->budgetDetails) {
                $budgetItems = $budget->budgetDetails
                    ->map(function ($item) {
                        return [
                            'item_code' => $item->item_code,
                            'item_description' => $item->item_description,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'unit_id' => $item->unit_id,
                            'unit_name' => $item->unit ? $item->unit->name : null,
                            'total_price' => $item->total_price,
                        ];
                    })
                    ->values();
            }
        @endphp

        let budgetItems = @json($budgetItems);
        let editingIndex = null; // Track if we're editing an item

        $(document).ready(function() {
            renderTable();

            $('#location_id').select2({
                placeholder: "Select location",
                allowClear: true,
                width: '100%'
            });

            $('#unit_id').select2({
                placeholder: "Select unit",
                allowClear: true,
                width: '100%'
            });

            // Add item button click handler
            $('#addItemButton').on('click', function() {
                addItem();
            });

            // Optional: Calculate total price dynamically when quantity or price changes
            $('#qty, #price').on('input', function() {
                calculateTotal();
            });
        });

        function calculateTotal() {
            let qty = parseFloat($('#qty').val()) || 0;
            let price = parseFloat($('#price').val()) || 0;
            let total = qty * price;
            // You can display this total somewhere if needed
            console.log('Total:', total);
            return total;
        }

        function addItem() {
            // Get form values
            let itemCode = $('#item').val().trim().toUpperCase();
            let description = $('#description').val().trim();
            let unitId = $('#unit_id').val();
            let unitName = $('#unit_id option:selected').text();
            let quantity = parseFloat($('#qty').val());
            let unitPrice = parseFloat($('#price').val());

            // Validate required fields
            if (!itemCode) {
                alert('Please enter item code');
                return;
            }
            if (!description) {
                alert('Please enter description');
                return;
            }
            if (!unitId) {
                alert('Please select unit of measurement');
                return;
            }
            if (!quantity || quantity <= 0) {
                alert('Please enter valid quantity');
                return;
            }
            if (!unitPrice || unitPrice <= 0) {
                alert('Please enter valid unit price');
                return;
            }

            // Calculate total price
            let totalPrice = quantity * unitPrice;

            // Create item object
            let newItem = {
                item_code: itemCode,
                item_description: description,
                quantity: quantity,
                unit_price: unitPrice,
                unit_id: unitId,
                unit_name: unitName,
                total_price: totalPrice
            };

            // If we're editing an existing item, replace it
            if (editingIndex !== null) {
                budgetItems[editingIndex] = newItem;
                editingIndex = null;
                $('#addItemButton').html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                Add Item
            `);
            } else {
                // Add new item to array
                budgetItems.push(newItem);
            }

            // Clear form fields
            clearItemFields();

            // Re-render table
            renderTable();
        }

        function clearItemFields() {
            $('#item').val('');
            $('#description').val('');
            $('#qty').val('');
            $('#price').val('');
            $('#unit_id').val('').trigger('change'); // Reset Select2
        }

        function clearModalFields() {
            clearItemFields();
            budgetItems = [];
            renderTable();
            editingIndex = null;
            $('#addItemButton').html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Add Item
        `);
        }

        function renderTable() {
            let tbody = $('#itemTableBody');
            tbody.empty();

            if (budgetItems.length === 0) {
                tbody.append(`
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        No items added yet. Click "Add Item" to add budget items.
                    </td>
                </tr>
            `);
                return;
            }

            budgetItems.forEach((item, index) => {
                tbody.append(`
                <tr>
                    <td class="px-4 py-2">
                        <input type="hidden" name="items[${index}][item_code]" value="${escapeHtml(item.item_code)}">
                        ${escapeHtml(item.item_code)}
                    </td>
                    <td class="px-4 py-2">
                        <input type="hidden" name="items[${index}][item_description]" value="${escapeHtml(item.item_description)}">
                        ${escapeHtml(item.item_description)}
                    </td>
                    <td class="px-4 py-2">
                        <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
                        ${escapeHtml(item.unit_name)}
                    </td>
                    <td class="px-4 py-2">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        ${item.quantity}
                    </td>
                    <td class="px-4 py-2 text-right">
                        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                        ${formatNumber(item.unit_price)}
                    </td>
                    <td class="px-4 py-2 text-right">
                        <input type="hidden" name="items[${index}][total_price]" value="${item.total_price}">
                        ${formatNumber(item.total_price)}
                    </td>
                    <td class="px-4 py-2 text-center">
                        <div class="flex gap-2 justify-center">
                            <button type="button" onclick="editItem(${index})" class="text-blue-600 hover:text-blue-800">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </button>
                            <button type="button" onclick="removeItem(${index})" class="text-red-600 hover:text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            });

            // Calculate and display grand total
            let grandTotal = budgetItems.reduce((sum, item) => sum + item.total_price, 0);
            tbody.append(`
            <tr class="bg-gray-50 font-bold">
                <td colspan="5" class="px-4 py-3 text-right">GRAND TOTAL:</td>
                <td class="px-4 py-3 text-right">${formatNumber(grandTotal)}</td>
                <td class="px-4 py-3"></td>
            </tr>
        `);
        }

        function removeItem(index) {
            if (confirm('Are you sure you want to remove this item?')) {
                budgetItems.splice(index, 1);
                renderTable();
            }
        }

        function editItem(index) {
            let item = budgetItems[index];

            // Populate form fields with item data
            $('#item').val(item.item_code);
            $('#description').val(item.item_description);
            $('#qty').val(item.quantity);
            $('#price').val(item.unit_price);
            $('#unit_id').val(item.unit_id).trigger('change');

            // Remove the item from the array
            budgetItems.splice(index, 1);

            // Set editing mode
            editingIndex = index;

            // Change button text to indicate editing mode
            $('#addItemButton').html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
            Update Item
        `);

            renderTable();
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Helper function to format numbers
        function formatNumber(value) {
            if (!value && value !== 0) return '0.00';
            return parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    </script>
@endsection
