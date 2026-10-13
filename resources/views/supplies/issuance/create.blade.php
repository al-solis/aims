@extends('dashboard')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    @php
        use Carbon\Carbon;

        $fieldClass =
            'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500';
        $labelClass = 'block mb-1.5 text-sm font-medium text-gray-900 dark:text-white';
    @endphp

    <div class="container mx-auto px-2 sm:px-4 lg:px-4 py-4">

        {{-- Alerts --}}
        @if ($errors->any())
            <div data-error="true"
                class="mb-4 flex gap-3 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-800">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd" />
                </svg>
                <ul class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div data-success="true"
                class="mb-4 flex items-center gap-3 rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800">
                <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    clearModalFields();
                });
            </script>
        @endif

        {{-- Page header --}}
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">New supplies issuance</h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                    Record the items issued to an employee. Saving deducts them from inventory.
                </p>
            </div>
            <a href="{{ route('issuance.index') }}" id="closeButton"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to issuance list
            </a>
        </div>

        <form action="{{ route('issuance.store') }}" method="POST" id="issuanceForm">
            @csrf

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- LEFT: issuance details + summary --}}
                <div class="space-y-5 lg:col-span-1">

                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Issuance details</h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Fields marked * are required.</p>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="purpose" class="{{ $labelClass }}">Purpose *</label>
                                <input type="text" name="purpose" id="purpose" class="{{ $fieldClass }}"
                                    placeholder="Why are these items being issued?" required>
                            </div>

                            <div>
                                <label for="issuance_date" class="{{ $labelClass }}">Issuance date *</label>
                                <input type="date" name="issuance_date" id="issuance_date"
                                    max="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}"
                                    class="{{ $fieldClass }}" required>
                            </div>

                            <div>
                                <label for="issued_to" class="{{ $labelClass }}">Issued to *</label>
                                <select id="issued_to" name="issued_to" class="{{ $fieldClass }}" required>
                                    <option value="">Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-loc="{{ $employee->location_id }}">
                                            {{ $employee->last_name }}, {{ $employee->first_name }}
                                            {{ $employee->middle_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <input type="hidden" name="location_id" id="location_id">
                                <label for="location_name" class="{{ $labelClass }}">Location</label>
                                <select id="location_name" name="location_name" class="{{ $fieldClass }}">
                                    <option value="">Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Filled in from the employee's assigned location.
                                </p>
                            </div>

                            <div>
                                <label for="remarks" class="{{ $labelClass }}">Remarks</label>
                                <textarea name="remarks" id="remarks" rows="3" class="{{ $fieldClass }}"
                                    placeholder="Add any issuance details"></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- Summary + actions --}}
                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800 lg:sticky lg:top-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Summary</h2>

                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500 dark:text-gray-400">Items</dt>
                                <dd id="summaryCount" class="font-medium text-gray-900 dark:text-white">0</dd>
                            </div>
                            <div
                                class="flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700">
                                <dt class="font-medium text-gray-900 dark:text-white">Grand total</dt>
                                <dd id="summaryTotal" class="text-xl font-semibold text-gray-900 dark:text-white">0.00
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex gap-2">
                            <a href="{{ route('issuance.index') }}"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                Cancel
                            </a>
                            <button type="submit" id="saveButton"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M11 2H9v3h2z" />
                                    <path
                                        d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                                </svg>
                                Save issuance
                            </button>
                        </div>
                    </section>
                </div>

                {{-- RIGHT: item entry + items table --}}
                <div class="space-y-5 lg:col-span-2">

                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Add an item</h2>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    The unit of measure and price come from the product.
                                </p>
                            </div>
                            <div class="text-right text-sm">
                                <div class="text-gray-500 dark:text-gray-400">Line total</div>
                                <div id="linePreview" class="font-semibold text-gray-900 dark:text-white">0.00</div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-6">
                            <div class="col-span-2 md:col-span-6">
                                <label for="supplies_id" class="{{ $labelClass }}">Product *</label>
                                <select id="supplies_id" name="supplies_id" class="{{ $fieldClass }}">
                                    <option value="">Select item</option>
                                    @foreach ($supplies as $supply)
                                        <option value="{{ $supply->id }}" data-name="{{ $supply->name }}"
                                            data-code="{{ $supply->code }}" data-uom="{{ $supply->uom_id }}"
                                            data-price="{{ $supply->unit_price }}">
                                            {{ $supply->code }} ({{ $supply->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-2 md:col-span-2">
                                <input type="hidden" name="uom_id" id="uom_id">
                                <label for="uom_name" class="{{ $labelClass }}">Unit of measure</label>
                                <select id="uom_name" name="uom_name" class="{{ $fieldClass }}">
                                    <option value="">Select UOM</option>
                                    @foreach ($uoms as $uom)
                                        <option value="{{ $uom->id }}">{{ $uom->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <input type="hidden" name="unit_price" id="unit_price">
                                <label for="unit_price_display" class="{{ $labelClass }}">Unit price</label>
                                <input type="text" id="unit_price_display" readonly placeholder="0.00"
                                    class="{{ $fieldClass }} cursor-not-allowed bg-gray-100 dark:bg-gray-600">
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label for="quantity" class="{{ $labelClass }}">Quantity *</label>
                                <input type="number" name="quantity" id="quantity" step="1" min="1"
                                    class="{{ $fieldClass }}" placeholder="0">
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button id="add-item-btn" type="button"
                                class="inline-flex items-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                </svg>
                                Add item
                            </button>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Items issued</h2>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                Change the quantity directly in the table.
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <tr>
                                        <th class="px-5 py-3 font-medium">Item</th>
                                        <th class="w-28 px-3 py-3 font-medium">Qty</th>
                                        <th class="px-3 py-3 font-medium">UOM</th>
                                        <th class="px-3 py-3 text-right font-medium">Price</th>
                                        <th class="px-3 py-3 text-right font-medium">Total</th>
                                        <th class="w-16 px-5 py-3 text-right font-medium">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="issuanceBody"
                                    class="divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
                                    {{-- Dynamic rows will be added here via JavaScript --}}
                                </tbody>
                                <tfoot>
                                    <tr
                                        class="border-t-2 border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-700">
                                        <td colspan="4"
                                            class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                            Grand total</td>
                                        <td class="px-3 py-3 text-right font-semibold text-gray-900 dark:text-white"
                                            id="grandTotal">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>

                    <!-- Hidden inputs for submitting items -->
                    <div id="hiddenInputsContainer"></div>
                </div>
            </div>
        </form>
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

            $('#location_name').select2({
                placeholder: "Select location",
                width: '100%'
            });

            $('#issued_to').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });

            // The product decides the UOM and price, so those fields are read-only
            $('#supplies_id').on('change', function() {
                const selected = $(this).find(':selected');
                const uomId = selected.data('uom');
                const price = selected.data('price');

                $('#unit_price').val(price ?? '');
                $('#unit_price_display').val(price !== undefined && price !== '' ? formatNumber(price) :
                '');

                if (uomId) {
                    $('#uom_id').val(uomId);
                    $('#uom_name').val(uomId).trigger('change');
                } else {
                    $('#uom_id').val('');
                    $('#uom_name').val('').trigger('change');
                }
                $('#uom_name').prop('disabled', true);
                updateLinePreview();
            });
            $('#uom_name').prop('disabled', true);

            // The employee decides the location
            $('#issued_to').on('change', function() {
                const locId = $(this).find(':selected').data('loc');
                $('#location_id').val(locId ?? '');
                $('#location_name').val(locId ?? '').trigger('change');
                $('#location_name').prop('disabled', true);
            });
            $('#location_name').prop('disabled', true);

            // Add item button click handler
            $('#add-item-btn').click(function() {
                addItem();
            });

            // Enter key handler for inputs
            $('#quantity, #supplies_id').keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addItem();
                }
            });

            // Live line total
            $('#quantity').on('input', updateLinePreview);

            renderTable();
        });

        function updateLinePreview() {
            const qty = parseFloat($('#quantity').val()) || 0;
            const price = parseFloat($('#unit_price').val()) || 0;
            $('#linePreview').text(formatNumber(qty * price));
        }

        function addItem() {
            // Get values
            const suppliesId = $('#supplies_id').val();
            const suppliesText = $('#supplies_id option:selected').text().trim();
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
            const tbody = $('#issuanceBody');
            tbody.empty();

            if (items.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">No items yet</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a product above and select Add item.</p>
                        </td>
                    </tr>`);
                $('#grandTotal, #summaryTotal').text('0.00');
                $('#summaryCount').text(0);
                updateHiddenInputs(); // clear stale inputs after the last item is removed
                return;
            }

            let grandTotal = 0;

            items.forEach((item) => {
                const row = `
                    <tr data-id="${item.id}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-5 py-3 font-medium">${escapeHtml(item.supplies_text)}</td>
                        <td class="px-3 py-3">
                            <input type="number" step="1" min="1" value="${item.quantity}"
                                aria-label="Quantity"
                                class="quantity-input w-24 rounded-md border border-gray-300 bg-gray-50 px-2 py-1.5 text-sm focus:border-gray-600 focus:ring-gray-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                onchange="updateItem(${item.id}, 'quantity', this.value)">
                        </td>
                        <td class="px-3 py-3">${escapeHtml(item.uom_text)}</td>
                        <td class="px-3 py-3 text-right">${formatNumber(item.unit_price)}</td>
                        <td class="px-3 py-3 text-right font-medium total-cell">${formatNumber(item.total_price)}</td>
                        <td class="px-5 py-3 text-right">
                            <button type="button" onclick="removeItem(${item.id})"
                                title="Remove ${escapeHtml(item.supplies_text)}"
                                aria-label="Remove ${escapeHtml(item.supplies_text)}"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 dark:hover:bg-red-900/30">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
                grandTotal += item.total_price;
            });

            $('#grandTotal, #summaryTotal').text(formatNumber(grandTotal));
            $('#summaryCount').text(items.length);
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
                $('#grandTotal, #summaryTotal').text(formatNumber(grandTotal));

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
            $('#uom_id').val('');
            $('#uom_name').val('').trigger('change');
            $('#unit_price').val('');
            $('#unit_price_display').val('');
            $('#quantity').val('');
            updateLinePreview();
        }

        // Called after a successful save: reset the form and item list
        function clearModalFields() {
            items = [];
            document.getElementById('issuanceForm').reset();
            $('#issued_to').val('').trigger('change');
            clearInputFields();
            renderTable();
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text).replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c]));
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
        $('#issuanceForm').submit(function(e) {
            if (items.length === 0) {
                e.preventDefault();
                showToast('Please add at least one item', 'error');
                return false;
            }

            // Validate main form fields
            const purpose = $('#purpose').val();
            const issuanceDate = $('#issuance_date').val();
            const issuedTo = $('#issued_to').val();

            if (!purpose || !issuanceDate || !issuedTo) {
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
                    toast.remove();
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
