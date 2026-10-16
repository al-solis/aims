@extends('dashboard')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/form-theme.css') }}">
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Auth;

        // Existing request that this user is not allowed to change
        $notRequester = $budget && Auth::user()->id != $budget?->requested_by;
        $notPending = $budget && $budget?->status != 0;
        $saveDisabled = $notRequester || $notPending;
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
                <h1>{{ $budget ? 'Edit budget request' : 'New budget request' }}</h1>
                <p>Describe what the budget is for, then list each item with its quantity and price.</p>
            </div>

            <div class="ft-body">
                <form method="POST" action="{{ $budget ? route('budget.update', $budget) : route('budget.store') }}"
                    enctype="multipart/form-data" id="employeeForm">
                    @csrf
                    @if ($budget)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                        {{-- LEFT: request details + summary --}}
                        <div class="lg:col-span-1">
                            <h3 class="ft-section-title" style="margin-top: 0;">Request Details</h3>

                            <div class="space-y-4 mt-4">
                                <div>
                                    <label for="purpose" class="ft-label">Purpose</label>
                                    <input type="text" name="purpose" id="purpose" class="ft-field"
                                        value="{{ old('purpose', $budget->purpose ?? '') }}"
                                        placeholder="e.g. Office supplies" required>
                                </div>

                                <div>
                                    <label for="location_id" class="ft-label">Location</label>
                                    @if ($budget)
                                        <input type="hidden" name="location_id" value="{{ $budget->location_id }}">
                                    @endif
                                    <select name="location_id_display" id="location_id" {{ $budget ? 'disabled' : '' }}
                                        class="select2 ft-field" {{ $budget ? '' : 'required' }}>
                                        <option value="">Select a location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ old('location_id', $budget->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="remarks" class="ft-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" rows="3" class="ft-field" placeholder="Add notes for the approver">{{ old('remarks', $budget->remarks ?? '') }}</textarea>
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

                            <div id="itemFormCard" class="rounded-lg p-3 -m-3">
                                <h3 id="itemFormTitle" class="ft-section-title" style="margin-top: 0;">Add an item</h3>
                                <p class="text-xs text-gray-500 mb-4">Item codes are saved in capital letters.</p>

                                <div class="grid grid-cols-2 gap-4 md:grid-cols-6">
                                    <div class="col-span-2 md:col-span-2">
                                        <label for="item" class="ft-label">Item code</label>
                                        <input type="text" name="item" id="item" class="ft-field"
                                            value="{{ old('item', $budget->item ?? '') }}" placeholder="e.g. A4-PAPER-001">
                                    </div>

                                    <div class="col-span-2 md:col-span-4">
                                        <label for="description" class="ft-label">Description</label>
                                        <input type="text" name="description" id="description" class="ft-field"
                                            value="{{ old('description', $budget->item_description ?? '') }}"
                                            placeholder="e.g. Printer paper, A4 size">
                                    </div>

                                    <div class="col-span-2 md:col-span-2">
                                        <label for="unit_id" class="ft-label">Unit of measure</label>
                                        <select name="unit_id" id="unit_id" class="select2 ft-field">
                                            <option value="">Select a unit</option>
                                            @foreach ($uoms as $uom)
                                                <option value="{{ $uom->id }}"
                                                    {{ old('unit_id', $budget->unit_id ?? '') == $uom->id ? 'selected' : '' }}>
                                                    {{ $uom->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="qty" class="ft-label">Quantity</label>
                                        <input type="number" name="qty" id="qty" min="1" step="1"
                                            class="ft-field" value="{{ old('qty', $budget->quantity ?? '') }}"
                                            placeholder="1">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="price" class="ft-label">Unit price</label>
                                        <input type="number" name="price" id="price" min="0"
                                            step="0.01" class="ft-field"
                                            value="{{ old('price', $budget->unit_price ?? '') }}" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <div class="ft-info flex items-center gap-4">
                                        <p class="ft-eyebrow">Line total</p>
                                        <p id="linePreview" class="ft-info-value" style="margin-top: 0;">0.00</p>
                                    </div>

                                    <div class="flex gap-2">
                                        <button type="button" id="cancelEditButton" class="ft-btn ft-btn-ghost"
                                            style="display: none;">
                                            Cancel edit
                                        </button>
                                        <button type="button" id="addItemButton" class="ft-btn ft-btn-primary">
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <h3 class="ft-section-title">Items</h3>
                            <p class="text-xs text-gray-500 mb-3">Use the pencil to change an item, or the bin to remove
                                it.
                            </p>

                            <div class="ft-table-wrap">
                                <table class="ft-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Item</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">UOM</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Unit price</th>
                                            <th scope="col">Total</th>
                                            <th scope="col" class="w-24 text-right">
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTableBody">
                                        <!-- Item rows are rendered by renderTable() -->
                                    </tbody>
                                    <tfoot id="itemTableFoot"></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ft-divider mt-8 pt-5 flex flex-wrap items-center justify-end gap-x-2 gap-y-3">
                        @if ($saveDisabled)
                            <p class="mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                                @if ($notPending)
                                    This request is no longer pending, so it can't be changed.
                                @else
                                    Only the person who made this request can change it.
                                @endif
                            </p>
                        @endif

                        <a href="{{ route('budget.index') }}" id="closeButton" class="ft-btn ft-btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        <button type="submit" id="saveButton" class="ft-btn ft-btn-primary"
                            @if ($saveDisabled) disabled @endif>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path d="M11 2H9v3h2z" />
                                <path
                                    d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                            </svg>
                            Save request
                        </button>
                    </div>
                </form>
            </div>
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
        let editingIndex = null; // index of the item being edited, or null

        const ICON_PLUS =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>';
        const ICON_CHECK =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>';

        $(document).ready(function() {
            renderTable();
            setAddButtonMode();

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

            $('#addItemButton').on('click', addItem);
            $('#cancelEditButton').on('click', cancelEdit);

            $('#qty, #price').on('input', calculateTotal);
            calculateTotal();
        });

        // Shows the live line total for the item being typed
        function calculateTotal() {
            let qty = parseFloat($('#qty').val()) || 0;
            let price = parseFloat($('#price').val()) || 0;
            let total = qty * price;
            $('#linePreview').text(formatNumber(total));
            return total;
        }

        function setAddButtonMode() {
            const editing = editingIndex !== null;
            $('#addItemButton').html(editing ? ICON_CHECK + ' Update item' : ICON_PLUS + ' Add item');
            $('#itemFormTitle').text(editing ? 'Edit item' : 'Add an item');
            $('#cancelEditButton').toggle(editing);
            // Highlight the entry area with the theme accent while editing
            $('#itemFormCard').css('box-shadow', editing ? '0 0 0 2px var(--ft-accent)' : 'none');
        }

        function addItem() {
            let itemCode = $('#item').val().trim().toUpperCase();
            let description = $('#description').val().trim();
            let unitId = $('#unit_id').val();
            let unitName = $('#unit_id option:selected').text();
            let quantity = parseFloat($('#qty').val());
            let unitPrice = parseFloat($('#price').val());

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

            let newItem = {
                item_code: itemCode,
                item_description: description,
                quantity: quantity,
                unit_price: unitPrice,
                unit_id: unitId,
                unit_name: unitName,
                total_price: quantity * unitPrice
            };

            if (editingIndex !== null) {
                budgetItems[editingIndex] = newItem; // replace in place
                editingIndex = null;
            } else {
                budgetItems.push(newItem);
            }

            clearItemFields();
            setAddButtonMode();
            renderTable();
        }

        function clearItemFields() {
            $('#item').val('');
            $('#description').val('');
            $('#qty').val('');
            $('#price').val('');
            $('#unit_id').val('').trigger('change'); // reset Select2
            calculateTotal();
        }

        function clearModalFields() {
            clearItemFields();
            budgetItems = [];
            editingIndex = null;
            setAddButtonMode();
            renderTable();
        }

        function cancelEdit() {
            editingIndex = null;
            clearItemFields();
            setAddButtonMode();
            renderTable();
        }

        function renderTable() {
            let tbody = $('#itemTableBody');
            let tfoot = $('#itemTableFoot');
            tbody.empty();
            tfoot.empty();

            let grandTotal = budgetItems.reduce((sum, item) => {
                const value = parseFloat(item.total_price);
                return sum + (isNaN(value) ? 0 : value);
            }, 0);

            $('#summaryCount').text(budgetItems.length);
            $('#summaryTotal').text(formatNumber(grandTotal));

            if (budgetItems.length === 0) {
                tbody.append(`
                <tr>
                    <td colspan="7" class="ft-empty">
                        No items yet. Fill in the item form above and select Add item.
                    </td>
                </tr>
            `);
                return;
            }

            budgetItems.forEach((item, index) => {
                const isEditing = editingIndex === index;
                tbody.append(`
                <tr ${isEditing ? 'style="background: var(--ft-mist);"' : ''}>
                    <td>
                        <input type="hidden" name="items[${index}][item_code]" value="${escapeHtml(item.item_code)}">
                        ${escapeHtml(item.item_code)}
                    </td>
                    <td>
                        <input type="hidden" name="items[${index}][item_description]" value="${escapeHtml(item.item_description)}">
                        ${escapeHtml(item.item_description)}
                    </td>
                    <td>
                        <input type="hidden" name="items[${index}][unit_id]" value="${escapeHtml(item.unit_id)}">
                        ${escapeHtml(item.unit_name)}
                    </td>
                    <td>
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        ${item.quantity}
                    </td>
                    <td>
                        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                        ${formatNumber(item.unit_price)}
                    </td>
                    <td>
                        <input type="hidden" name="items[${index}][total_price]" value="${item.total_price}">
                        ${formatNumber(item.total_price)}
                    </td>
                    <td>
                        <div class="flex justify-end gap-1">
                            <button type="button" onclick="editItem(${index})" title="Edit ${escapeHtml(item.item_code)}"
                                aria-label="Edit ${escapeHtml(item.item_code)}"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                            </button>
                            <button type="button" onclick="removeItem(${index})" title="Remove ${escapeHtml(item.item_code)}"
                                aria-label="Remove ${escapeHtml(item.item_code)}"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            });

            tfoot.append(`
            <tr class="border-t-2 border-gray-300">
                <td colspan="5" class="px-4 py-3 text-right font-semibold">Grand total</td>
                <td class="px-4 py-3 font-semibold">${formatNumber(grandTotal)}</td>
                <td></td>
            </tr>
        `);
        }

        function removeItem(index) {
            if (!confirm('Are you sure you want to remove this item?')) return;

            budgetItems.splice(index, 1);

            // keep edit state consistent with the shifted list
            if (editingIndex !== null) {
                if (editingIndex === index) {
                    editingIndex = null;
                    clearItemFields();
                } else if (editingIndex > index) {
                    editingIndex--;
                }
                setAddButtonMode();
            }
            renderTable();
        }

        function editItem(index) {
            let item = budgetItems[index];

            $('#item').val(item.item_code);
            $('#description').val(item.item_description);
            $('#qty').val(item.quantity);
            $('#price').val(item.unit_price);
            $('#unit_id').val(item.unit_id).trigger('change');

            // The item stays in the list until "Update item" replaces it
            editingIndex = index;
            setAddButtonMode();
            calculateTotal();
            renderTable();

            document.getElementById('itemFormCard').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
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

        function formatNumber(value) {
            if (!value && value !== 0) return '0.00';
            return parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    </script>
@endsection
