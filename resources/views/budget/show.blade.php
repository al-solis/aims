@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Auth;

        $fieldClass =
            'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500';
        $labelClass = 'block mb-1.5 text-sm font-medium text-gray-900 dark:text-white';

        // Existing request that this user is not allowed to change
        $notRequester = $budget && Auth::user()->id != $budget?->requested_by;
        $notPending = $budget && $budget?->status != 0;
        $saveDisabled = $notRequester || $notPending;
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
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $budget ? 'Edit budget request' : 'New budget request' }}
                </h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                    Describe what the budget is for, then list each item with its quantity and price.
                </p>
            </div>
            <a href="{{ route('budget.index') }}" id="closeButton"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to budget requests
            </a>
        </div>

        <form method="POST" action="{{ $budget ? route('budget.update', $budget) : route('budget.store') }}"
            enctype="multipart/form-data" id="employeeForm">
            @csrf
            @if ($budget)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- LEFT: request details + summary --}}
                <div class="space-y-5 lg:col-span-1">

                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Request details</h2>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="purpose" class="{{ $labelClass }}">Purpose</label>
                                <input type="text" name="purpose" id="purpose" class="{{ $fieldClass }}"
                                    value="{{ old('purpose', $budget->purpose ?? '') }}" placeholder="e.g. Office supplies"
                                    required>
                            </div>

                            <div>
                                <label for="location_id" class="{{ $labelClass }}">Location</label>
                                @if ($budget)
                                    <input type="hidden" name="location_id" value="{{ $budget->location_id }}">
                                @endif
                                <select name="location_id_display" id="location_id" {{ $budget ? 'disabled' : '' }}
                                    class="select2 {{ $fieldClass }}" {{ $budget ? '' : 'required' }}>
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
                                <label for="remarks" class="{{ $labelClass }}">Remarks</label>
                                <textarea name="remarks" id="remarks" rows="3" class="{{ $fieldClass }}"
                                    placeholder="Add notes for the approver">{{ old('remarks', $budget->remarks ?? '') }}</textarea>
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
                                <dd id="summaryTotal" class="text-xl font-semibold text-gray-900 dark:text-white">0.00</dd>
                            </div>
                        </dl>

                        @if ($saveDisabled)
                            <p
                                class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                @if ($notPending)
                                    This request is no longer pending, so it can't be changed.
                                @else
                                    Only the person who made this request can change it.
                                @endif
                            </p>
                        @endif

                        <div class="mt-5 flex gap-2">
                            <a href="{{ route('budget.index') }}"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                Cancel
                            </a>
                            <button type="submit" id="saveButton" @if ($saveDisabled) disabled @endif
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 {{ $saveDisabled ? 'cursor-not-allowed opacity-50' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M11 2H9v3h2z" />
                                    <path
                                        d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                                </svg>
                                Save request
                            </button>
                        </div>
                    </section>
                </div>

                {{-- RIGHT: item entry + items table --}}
                <div class="space-y-5 lg:col-span-2">

                    <section id="itemFormCard" class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 id="itemFormTitle" class="text-base font-semibold text-gray-900 dark:text-white">Add an
                                    item</h2>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    Item codes are saved in capital letters.
                                </p>
                            </div>
                            <div class="text-right text-sm">
                                <div class="text-gray-500 dark:text-gray-400">Line total</div>
                                <div id="linePreview" class="font-semibold text-gray-900 dark:text-white">0.00</div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-6">
                            <div class="col-span-2 md:col-span-2">
                                <label for="item" class="{{ $labelClass }}">Item code</label>
                                <input type="text" name="item" id="item" class="{{ $fieldClass }}"
                                    value="{{ old('item', $budget->item ?? '') }}" placeholder="e.g. A4-PAPER-001">
                            </div>

                            <div class="col-span-2 md:col-span-4">
                                <label for="description" class="{{ $labelClass }}">Description</label>
                                <input type="text" name="description" id="description" class="{{ $fieldClass }}"
                                    value="{{ old('description', $budget->item_description ?? '') }}"
                                    placeholder="e.g. Printer paper, A4 size">
                            </div>

                            <div class="col-span-2 md:col-span-2">
                                <label for="unit_id" class="{{ $labelClass }}">Unit of measure</label>
                                <select name="unit_id" id="unit_id" class="select2 {{ $fieldClass }}">
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
                                <label for="qty" class="{{ $labelClass }}">Quantity</label>
                                <input type="number" name="qty" id="qty" min="1" step="1"
                                    class="{{ $fieldClass }}" value="{{ old('qty', $budget->quantity ?? '') }}"
                                    placeholder="1">
                            </div>

                            <div class="md:col-span-2">
                                <label for="price" class="{{ $labelClass }}">Unit price</label>
                                <input type="number" name="price" id="price" min="0" step="0.01"
                                    class="{{ $fieldClass }}" value="{{ old('price', $budget->unit_price ?? '') }}"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" id="cancelEditButton"
                                class="hidden rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                Cancel edit
                            </button>
                            <button type="button" id="addItemButton"
                                class="inline-flex items-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                            </button>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Items</h2>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                Use the pencil to change an item, or the bin to remove it.
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 font-medium">Item</th>
                                        <th scope="col" class="px-3 py-3 font-medium">Description</th>
                                        <th scope="col" class="px-3 py-3 font-medium">UOM</th>
                                        <th scope="col" class="px-3 py-3 text-right font-medium">Qty</th>
                                        <th scope="col" class="px-3 py-3 text-right font-medium">Unit price</th>
                                        <th scope="col" class="px-3 py-3 text-right font-medium">Total</th>
                                        <th scope="col" class="w-24 px-5 py-3 text-right font-medium">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody"
                                    class="divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
                                    <!-- Item rows are rendered by renderTable() -->
                                </tbody>
                                <tfoot id="itemTableFoot"></tfoot>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </form>
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
            $('#cancelEditButton').toggleClass('hidden', !editing);
            $('#itemFormCard').toggleClass('ring-2 ring-blue-400', editing);
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
                    <td colspan="7" class="px-5 py-12 text-center">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">No items yet</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fill in the item form above and select Add item.</p>
                    </td>
                </tr>
            `);
                return;
            }

            budgetItems.forEach((item, index) => {
                const isEditing = editingIndex === index;
                tbody.append(`
                <tr class="${isEditing ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'}">
                    <td class="px-5 py-3 font-medium">
                        <input type="hidden" name="items[${index}][item_code]" value="${escapeHtml(item.item_code)}">
                        ${escapeHtml(item.item_code)}
                    </td>
                    <td class="px-3 py-3">
                        <input type="hidden" name="items[${index}][item_description]" value="${escapeHtml(item.item_description)}">
                        ${escapeHtml(item.item_description)}
                    </td>
                    <td class="px-3 py-3">
                        <input type="hidden" name="items[${index}][unit_id]" value="${escapeHtml(item.unit_id)}">
                        ${escapeHtml(item.unit_name)}
                    </td>
                    <td class="px-3 py-3 text-right">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        ${item.quantity}
                    </td>
                    <td class="px-3 py-3 text-right">
                        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                        ${formatNumber(item.unit_price)}
                    </td>
                    <td class="px-3 py-3 text-right font-medium">
                        <input type="hidden" name="items[${index}][total_price]" value="${item.total_price}">
                        ${formatNumber(item.total_price)}
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-1">
                            <button type="button" onclick="editItem(${index})" title="Edit ${escapeHtml(item.item_code)}"
                                aria-label="Edit ${escapeHtml(item.item_code)}"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:hover:bg-blue-900/30">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                            </button>
                            <button type="button" onclick="removeItem(${index})" title="Remove ${escapeHtml(item.item_code)}"
                                aria-label="Remove ${escapeHtml(item.item_code)}"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 dark:hover:bg-red-900/30">
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
            <tr class="border-t-2 border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-700">
                <td colspan="5" class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">Grand total</td>
                <td class="px-3 py-3 text-right font-semibold text-gray-900 dark:text-white">${formatNumber(grandTotal)}</td>
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
