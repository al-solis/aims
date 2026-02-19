@extends('dashboard')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
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
                    Supply Receiving
                </h2>
                <p class="text-sm text-gray-600">
                    Receive items to update inventory.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">

            <form action="{{ route('receiving.store') }}" method="POST" id="receivingForm">
                @csrf
                <div class="grid ml-1 mr-1 mt-2 gap-2 mb-2 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="description"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Description*</label>
                        <input type="text" name="description" id="description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Type receiving description." required></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="received_date" class="block text-xs font-medium text-gray-900 dark:text-white">Received
                            Date*</label>
                        <input type="date" name="received_date" id="received_date" max="{{ now()->format('Y-m-d') }}"
                            value="{{ now()->format('Y-m-d') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="reference_no" class="block text-xs font-medium text-gray-900 dark:text-white">Reference
                            No*</label>
                        <input type="text" name="reference_no" id="reference_no"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Type reference/ PO number." required></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="supplier_id"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">Supplier*</label>
                        <select id="supplier_id" name="supplier_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option value="">Select supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="employee_id"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">Received By*</label>
                        <select id="employee_id" name="employee_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option value="">Select employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->first_name }}
                                    {{ $employee->middle_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="remarks"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Provide receiving details, etc."></textarea>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="supplies_id"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">Product*</label>
                        <select id="supplies_id" name="supplies_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                            <option value="">Select item</option>
                            @foreach ($supplies as $supply)
                                <option value="{{ $supply->id }}" data-name="{{ $supply->name }}"
                                    data-code="{{ $supply->code }}" data-uom="{{ $supply->uom_id }}">{{ $supply->code }}
                                    ({{ $supply->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <input type="hidden" name="uom_id" id="uom_id">
                        <label for="uom_name"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">UOM*</label>
                        <select id="uom_name" name="uom_name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                            <option value="">Select UOM</option>
                            @foreach ($uoms as $uom)
                                <option value="{{ $uom->id }}">{{ $uom->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="quantity"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Quantity*</label>
                        <input type="number" name="quantity" id="quantity" step="0.01" min="0.01"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="0.00">
                    </div>

                    <div class="sm:col-span-1">
                        <label for="unit_price" class="block text-xs font-medium text-gray-900 dark:text-white">Unit
                            Price*</label>
                        <input type="number" name="unit_price" id="unit_price" step="0.01" min="0"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="0.00">
                    </div>

                    <div class="sm:col-span-2">
                        <button id="add-item-btn" type="button"
                            class="mt-2 h-fit text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-xs px-4 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            Add Item
                        </button>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-4 mb-2">Item/s Received</h3>
                <div class="bg-white border rounded-xl overflow-x-auto overflow-y-auto md:overflow-visible scroll-smooth">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-200 text-gray-600">
                            <tr class="rounded-xl">
                                <th class="px-4 py-3 text-left w-[200px]">Item</th>
                                <th class="px-4 py-3 text-left w-[100px]">Qty</th>
                                <th class="px-4 py-3 text-left w-[150px]">UOM</th>
                                <th class="px-4 py-3 text-left w-[120px]">Price</th>
                                <th class="px-4 py-3 text-left w-[120px]">Total</th>
                                <th class="px-4 py-3 text-left w-[100px]">Action</th>
                            </tr>
                        </thead>

                        <tbody id="receivingBody" class="divide-y">
                            {{-- Dynamic rows will be added here via JavaScript --}}
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="4" class="px-4 py-3 text-right">Grand Total:</td>
                                <td class="px-4 py-3" id="grandTotal">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Hidden inputs for submitting items -->
                <div id="hiddenInputsContainer"></div>

                <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">
                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('receiving.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>

                    <button type="submit" id="saveButton"
                        class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-floppy" viewBox="0 0 16 16">
                            <path d="M11 2H9v3h2z" />
                            <path
                                d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                        </svg>
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <script>
        // Store items data
        let items = [];
        let itemId = 0;

        $(document).ready(function() {
            // Initialize Select2
            $('#supplies_id').select2({
                placeholder: "Select item",
                allowClear: true,
                width: '100%'
            });

            $('#uom_name').select2({
                placeholder: "Select UOM",
                allowClear: true,
                width: '100%'
            });

            $('#supplies_id').on('change', function() {
                let uomId = $(this).find(':selected').data('uom');

                if (uomId) {
                    $('#uom_id').val(uomId);
                    $('#uom_name').val(uomId).trigger('change');
                }
                $('#uom_name').prop('disabled', true);
            });

            $('#employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });

            $('#supplier_id').select2({
                placeholder: "Select supplier",
                allowClear: true,
                width: '100%'
            });

            // Add item button click handler
            $('#add-item-btn').click(function() {
                addItem();
            });

            // Enter key handler for inputs
            $('#quantity, #unit_price, #supplies_id, #uom_id').keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addItem();
                }
            });
        });

        // document.getElementById('supplies_id').addEventListener('change', function() {
        //     const selectedOption = this.options[this.selectedIndex];
        //     const uomId = selectedOption.getAttribute('data-uom');
        //     document.getElementById('uom_id').value = uomId;
        //     $('#uom_name').val(uomId).trigger('change');
        // });

        // document.getElementById('supplies_id').addEventListener('input', function() {
        //     const selectedOption = this.options[this.selectedIndex];
        //     const uomId = selectedOption.getAttribute('data-uom');
        //     document.getElementById('uom_id').value = uomId;
        //     $('#uom_name').val(uomId).trigger('change');
        // });

        function addItem() {
            // Get values
            const suppliesId = $('#supplies_id').val();
            const suppliesText = $('#supplies_id option:selected').text();
            const uomId = $('#uom_id').val();
            const uomText = $('#uom_name option:selected').text();
            const quantity = parseFloat($('#quantity').val()) || 0;
            const unitPrice = parseFloat($('#unit_price').val()) || 0;

            // Validation
            if (!suppliesId) {
                showToast('Please select an item', 'error');
                return;
            }

            if (!uomId) {
                showToast('Please select UOM', 'error');
                return;
            }

            if (quantity <= 0) {
                showToast('Please enter a valid quantity', 'error');
                return;
            }

            if (unitPrice < 0) {
                showToast('Please enter a valid unit price', 'error');
                return;
            }

            // Check for duplicate item
            const existingItem = items.find(item =>
                item.supplies_id == suppliesId && item.uom_id == uomId
            );

            if (existingItem) {
                if (confirm('Item with same UOM already exists. Do you want to update the quantity and price?')) {
                    // Update existing item
                    existingItem.quantity = quantity;
                    existingItem.unit_price = unitPrice;
                    existingItem.total_price = quantity * unitPrice;
                    renderTable();
                    clearInputFields();
                    showToast('Item updated successfully', 'success');
                }
                return;
            }

            // Add new item
            const newItem = {
                id: itemId++,
                supplies_id: parseInt(suppliesId),
                supplies_text: suppliesText,
                uom_id: parseInt(uomId),
                uom_text: uomText,
                quantity: quantity,
                unit_price: unitPrice,
                total_price: quantity * unitPrice
            };

            items.push(newItem);
            renderTable();
            clearInputFields();
            showToast('Item added successfully', 'success');
        }

        function renderTable() {
            const tbody = $('#receivingBody');
            tbody.empty();

            if (items.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center py-4 text-gray-500">No items added yet.</td></tr>');
                $('#grandTotal').text('0.00');
                return;
            }

            let grandTotal = 0;

            items.forEach((item, index) => {
                const row = `
                    <tr data-id="${item.id}">
                        <td class="px-4 py-2">${item.supplies_text}</td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.01" min="0.01" value="${item.quantity}" 
                                class="quantity-input w-20 px-2 py-1 border rounded text-xs" 
                                onchange="updateItem(${item.id}, 'quantity', this.value)">
                        </td>
                        <td class="px-4 py-2">${item.uom_text}</td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.01" min="0" value="${item.unit_price}" 
                                class="price-input w-24 px-2 py-1 border rounded text-xs" 
                                onchange="updateItem(${item.id}, 'unit_price', this.value)">
                        </td>
                        <td class="px-4 py-2 total-cell">${formatNumber(item.total_price)}</td>
                        <td class="px-4 py-2">
                            <button type="button" onclick="removeItem(${item.id})" 
                                class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
                grandTotal += item.total_price;
            });

            $('#grandTotal').text(formatNumber(grandTotal));
            updateHiddenInputs();
        }

        window.updateItem = function(id, field, value) {
            const item = items.find(i => i.id === id);
            if (item) {
                value = parseFloat(value) || 0;

                if (field === 'quantity') {
                    item.quantity = value;
                } else if (field === 'unit_price') {
                    item.unit_price = value;
                }

                item.total_price = item.quantity * item.unit_price;

                // Update total cell
                $(`tr[data-id="${id}"] .total-cell`).text(formatNumber(item.total_price));

                // Recalculate grand total
                let grandTotal = items.reduce((sum, i) => sum + i.total_price, 0);
                $('#grandTotal').text(formatNumber(grandTotal));

                updateHiddenInputs();
            }
        }

        window.removeItem = function(id) {
            if (confirm('Are you sure you want to remove this item?')) {
                items = items.filter(i => i.id !== id);
                renderTable();
                showToast('Item removed successfully', 'success');
            }
        }

        function clearInputFields() {
            $('#supplies_id').val('').trigger('change');
            $('#uom_id').val('').trigger('change');
            $('#quantity').val('');
            $('#unit_price').val('');
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }

        function updateHiddenInputs() {
            const container = $('#hiddenInputsContainer');
            container.empty();

            items.forEach((item, index) => {
                container.append(`
                    <input type="hidden" name="items[${index}][supplies_id]" value="${item.supplies_id}">
                    <input type="hidden" name="items[${index}][uom_id]" value="${item.uom_id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                    <input type="hidden" name="items[${index}][total_price]" value="${item.total_price}">
                `);
            });
        }

        // Form submission validation
        $('#receivingForm').submit(function(e) {
            if (items.length === 0) {
                e.preventDefault();
                showToast('Please add at least one item', 'error');
                return false;
            }

            // Validate main form fields
            const description = $('#description').val();
            const receivedDate = $('#received_date').val();
            const referenceNo = $('#reference_no').val();
            const supplierId = $('#supplier_id').val();
            const employeeId = $('#employee_id').val();

            if (!description || !receivedDate || !referenceNo || !supplierId || !employeeId) {
                e.preventDefault();
                showToast('Please fill in all required fields', 'error');
                return false;
            }

            return true;
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 
                ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 
                type === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 
                'bg-blue-100 text-blue-700 border border-blue-300'}`;
            toast.textContent = message;
            toast.style.animation = 'slideInRight 0.3s ease-out';

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Auto-hide success message
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('[data-success="true"]');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.remove();
                }, 3000);
            }
        });
    </script>
@endsection
