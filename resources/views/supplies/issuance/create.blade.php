@extends('dashboard')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    @php
        use Carbon\Carbon;
    @endphp

    {{-- Colors come from <html data-theme="..."> defined in the layout; do not set data-theme here --}}
    <div class="ft-page max-w-6xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        {{-- Alerts --}}
        @if ($errors->any())
            <div data-error="true" class="ft-alert ft-alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div data-success="true" class="ft-alert ft-alert-success">
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    clearModalFields();
                });
            </script>
        @endif

        <div class="ft-card">
            <div class="ft-card-head">
                <h1>New supplies issuance</h1>
                <p>Record the items issued to an employee. Saving deducts them from inventory.</p>
            </div>

            <div class="ft-body">
                <form action="{{ route('issuance.store') }}" method="POST" id="issuanceForm">
                    @csrf

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                        {{-- LEFT: issuance details + summary --}}
                        <div class="lg:col-span-1">
                            <h3 class="ft-section-title" style="margin-top: 0;">Issuance Details</h3>
                            <p class="text-xs text-gray-500 mb-4">Fields marked * are required.</p>

                            <div class="space-y-4">
                                <div>
                                    <label for="purpose" class="ft-label">Purpose *</label>
                                    <input type="text" name="purpose" id="purpose" class="ft-field"
                                        placeholder="Why are these items being issued?" required>
                                </div>

                                <div>
                                    <label for="issuance_date" class="ft-label">Issuance date *</label>
                                    <input type="date" name="issuance_date" id="issuance_date"
                                        max="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}"
                                        class="ft-field" required>
                                </div>

                                <div>
                                    <label for="issued_to" class="ft-label">Issued to *</label>
                                    <select id="issued_to" name="issued_to" class="ft-field" required>
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
                                    <label for="location_name" class="ft-label">Location</label>
                                    <select id="location_name" name="location_name" class="ft-field">
                                        <option value="">Select location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Filled in from the employee's assigned location.
                                    </p>
                                </div>

                                <div>
                                    <label for="remarks" class="ft-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" rows="3" class="ft-field" placeholder="Add any issuance details"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="ft-info">
                                        <p class="ft-eyebrow">Items</p>
                                        <p id="summaryCount" class="ft-info-value">0</p>
                                    </div>
                                    <div class="ft-info">
                                        <p class="ft-eyebrow">Grand total</p>
                                        <p id="summaryTotal" class="ft-info-value">0.00</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: item entry + items table --}}
                        <div class="lg:col-span-2">
                            <h3 class="ft-section-title" style="margin-top: 0;">Add an Item</h3>
                            <p class="text-xs text-gray-500 mb-4">The unit of measure and price come from the product.</p>

                            <div class="grid grid-cols-2 gap-4 md:grid-cols-6">
                                <div class="col-span-2 md:col-span-6">
                                    <label for="supplies_id" class="ft-label">Product *</label>
                                    <select id="supplies_id" name="supplies_id" class="ft-field">
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
                                    <label for="uom_name" class="ft-label">Unit of measure</label>
                                    <select id="uom_name" name="uom_name" class="ft-field">
                                        <option value="">Select UOM</option>
                                        @foreach ($uoms as $uom)
                                            <option value="{{ $uom->id }}">{{ $uom->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <input type="hidden" name="unit_price" id="unit_price">
                                    <label for="unit_price_display" class="ft-label">Unit price</label>
                                    <input type="text" id="unit_price_display" readonly placeholder="0.00"
                                        class="ft-field">
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label for="quantity" class="ft-label">Quantity *</label>
                                    <input type="number" name="quantity" id="quantity" step="1" min="1"
                                        class="ft-field" placeholder="0">
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-3">
                                <div class="ft-info flex items-center gap-4">
                                    <p class="ft-eyebrow">Line total</p>
                                    <p id="linePreview" class="ft-info-value" style="margin-top: 0;">0.00</p>
                                </div>

                                <button id="add-item-btn" type="button" class="ft-btn ft-btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                    </svg>
                                    Add item
                                </button>
                            </div>

                            <h3 class="ft-section-title">Items Issued</h3>
                            <p class="text-xs text-gray-500 mb-3">Change the quantity directly in the table.</p>

                            <div class="ft-table-wrap">
                                <table class="ft-table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="w-28">Qty</th>
                                            <th>UOM</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th class="w-16 text-right">
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody id="issuanceBody">
                                        {{-- Dynamic rows will be added here via JavaScript --}}
                                    </tbody>
                                    <tfoot>
                                        <tr class="ft-divider">
                                            <td colspan="4" class="px-4 py-3 text-right font-semibold">Grand total</td>
                                            <td class="px-4 py-3 font-semibold" id="grandTotal">0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hidden inputs for submitting items -->
                            <div id="hiddenInputsContainer"></div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ft-divider mt-8 pt-5 flex justify-end gap-x-2">
                        <a href="{{ route('issuance.index') }}" id="closeButton" class="ft-btn ft-btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        <button type="submit" id="saveButton" class="ft-btn ft-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path d="M11 2H9v3h2z" />
                                <path
                                    d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                            </svg>
                            Save issuance
                        </button>
                    </div>
                </form>
            </div>
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
                        <td colspan="6" class="ft-empty">
                            No items yet. Choose a product above and select Add item.
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
                    <tr data-id="${item.id}">
                        <td>${escapeHtml(item.supplies_text)}</td>
                        <td>
                            <input type="number" step="1" min="1" value="${item.quantity}"
                                aria-label="Quantity"
                                class="quantity-input ft-field"
                                onchange="updateItem(${item.id}, 'quantity', this.value)">
                        </td>
                        <td>${escapeHtml(item.uom_text)}</td>
                        <td>${formatNumber(item.unit_price)}</td>
                        <td class="total-cell">${formatNumber(item.total_price)}</td>
                        <td class="text-right">
                            <button type="button" onclick="removeItem(${item.id})"
                                title="Remove ${escapeHtml(item.supplies_text)}"
                                aria-label="Remove ${escapeHtml(item.supplies_text)}"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
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
            toast.className = 'ft-alert fixed top-4 right-4 z-50 max-w-sm shadow-lg ' +
                (type === 'success' ? 'ft-alert-success' : 'ft-alert-error');
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
