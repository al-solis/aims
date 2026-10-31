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
                        <h1>Supplies Management</h1>
                        <p>Manage supplies, inventory and issuance.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @if (!$canCreate) disabled @endif data-modal-target="add-modal"
                            data-modal-toggle="{{ $canCreate ? 'add-modal' : '' }}"
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Item
                        </button>

                        <a href="{{ route('receiving.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
                                <path
                                    d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" />
                            </svg>
                            Receiving
                        </a>

                        <a href="{{ route('issuance.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="m11.798 8.271-3.182 1.97c-.27.166-.616-.036-.616-.372V9.1s-2.571-.3-4 2.4c.571-4.8 3.143-4.8 4-4.8v-.769c0-.336.346-.538.616-.371l3.182 1.969c.27.166.27.576 0 .742" />
                                <path
                                    d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m.694 2.09A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09l-.636 7a1 1 0 0 1-.996.91H2.826a1 1 0 0 1-.995-.91zM6.172 2a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z" />
                            </svg>
                            Issuance
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
                    <div class="md:w-1/3 w-full flex flex-col md:flex-row gap-3">
                        <select name="searchsupplier" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Suppliers</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ request('searchsupplier') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="searchcat" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('searchcat') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="ft-field flex-1" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </form>

                {{-- Table --}}
                <div class="ft-table-wrap">
                    <table class="ft-table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-[150px]">Code</th>
                                <th scope="col" class="w-[180px]">Name</th>
                                <th scope="col" class="w-[200px]">Description</th>
                                <th scope="col" class="w-[150px]">Supplier</th>
                                <th scope="col" class="w-[150px]">Category</th>
                                <th scope="col" class="w-[80px]">UOM</th>
                                <th scope="col" class="text-right w-[100px]">Unit Price</th>
                                <th scope="col" class="text-right w-[100px]">Re-order Qty</th>
                                <th scope="col" class="text-right w-[100px]">Available Qty</th>
                                @if ($canUpdate || $canDelete)
                                    <th scope="col" class="text-center w-[50px]">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplies as $supply)
                                <tr>
                                    <td class="whitespace-normal break-all align-top font-semibold"
                                        style="color: var(--ft-ink);">
                                        {{ $supply->code }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $supply->name }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $supply->description ?? '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $supply->supplier_id ? $supply->supplier->name : '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $supply->category_id ? $supply->category->name : 'N/A' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $supply->uom ? $supply->uom->name : 'N/A' }}
                                    </td>
                                    <td class="text-right tabular-nums whitespace-normal break-all align-top"
                                        style="color: var(--ft-text);">
                                        {{ number_format($supply->unit_price, 2) }}
                                    </td>
                                    <td class="text-right tabular-nums whitespace-normal break-all align-top"
                                        style="color: var(--ft-text);">
                                        {{ number_format($supply->reorder_quantity, 2) }}
                                    </td>
                                    <td class="text-right tabular-nums whitespace-normal break-all align-top"
                                        style="color: var(--ft-text);">
                                        {{ number_format($supply->available_stock, 2) }}
                                    </td>
                                    @if ($canUpdate)
                                        <td>
                                            <div class="flex items-center justify-center">
                                                <button type="button" title="Edit Supply {{ $supply->name }}"
                                                    data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                    data-id="{{ $supply->id }}" data-name="{{ $supply->name }}"
                                                    data-code="{{ $supply->code }}"
                                                    data-description="{{ $supply->description }}"
                                                    data-supplier_id="{{ $supply->supplier_id }}"
                                                    data-category_id="{{ $supply->category_id }}"
                                                    data-uom_id="{{ $supply->uom_id }}"
                                                    data-unit_price="{{ $supply->unit_price }}"
                                                    data-reorder_quantity="{{ $supply->reorder_quantity }}"
                                                    data-available_stock="{{ $supply->available_stock }}"
                                                    data-status="{{ $supply->status }}" onclick="openEditModal(this)"
                                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd"
                                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="ft-empty">No supplies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!$canCreate && !$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create or update supplies information.
                    </p>
                @elseif (!$canCreate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create supplies information.
                    </p>
                @elseif (!$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to update supplies information.
                    </p>
                @endif

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $supplies->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create supplies modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Add New Item
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="add-modal">
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
                    <form action="{{ route('supplies.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <label for="name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" class="ft-field"
                                    placeholder="e.g. A4 Bond Paper" required>
                            </div>

                            <div class="w-full md:col-span-2">
                                <label for="description" class="ft-label">Description</label>
                                <textarea name="description" id="description" rows="3" class="ft-field"
                                    placeholder="e.g. Hard Copy A4 Bond Paper 20/70 gsm"></textarea>
                            </div>

                            <div class="w-full md:col-span-2">
                                <label for="supplier_id" class="ft-label">Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="ft-field">
                                    <option value="" selected>Select supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="category_id" class="ft-label">Category <span
                                        class="text-red-600">*</span></label>
                                <select name="category_id" id="category_id" class="ft-field" required>
                                    <option value="" selected>Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="uom_id" class="ft-label">UOM <span class="text-red-600">*</span></label>
                                <select name="uom_id" id="uom_id" class="ft-field" required>
                                    <option value="" selected>Select UOM</option>
                                    @foreach ($uoms as $uom)
                                        <option value="{{ $uom->id }}">{{ $uom->code }} ({{ $uom->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full md:col-span-1">
                                <label for="unit_price" class="ft-label">Unit Price <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="unit_price" id="unit_price" min="0" step="0.01"
                                    class="ft-field" placeholder="225.50" required>
                            </div>

                            <div class="w-full md:col-span-1">
                                <label for="initial_stock" class="ft-label">Initial Stock <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="initial_stock" id="initial_stock" min="0"
                                    step="1" class="ft-field" placeholder="100" required>
                            </div>

                            <div class="w-full md:col-span-2">
                                <label for="reorder_quantity" class="ft-label">Reorder Quantity</label>
                                <input type="number" name="reorder_quantity" id="reorder_quantity" min="0"
                                    step="1" class="ft-field" placeholder="100">
                            </div>

                        </div>
                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Add Item
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create supplies modal -->

    <!-- Modal Edit -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Update Item
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="edit-modal">
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
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="edit_id" id="edit_id">

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="w-full md:col-span-2">
                                <label for="edit_name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="edit_name" id="edit_name" class="ft-field"
                                    placeholder="e.g. A4 Bond Paper" required>
                            </div>

                            <div class="w-full md:col-span-2">
                                <label for="edit_description" class="ft-label">Description</label>
                                <textarea name="edit_description" id="edit_description" rows="3" class="ft-field"
                                    placeholder="e.g. Hard Copy A4 Bond Paper 20/70 gsm"></textarea>
                            </div>

                            <div class="w-full md:col-span-2">
                                <label for="edit_supplier_id" class="ft-label">Supplier</label>
                                <select name="edit_supplier_id" id="edit_supplier_id" class="ft-field">
                                    <option value="" selected>Select supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_category_id" class="ft-label">Category <span
                                        class="text-red-600">*</span></label>
                                <select name="edit_category_id" id="edit_category_id" class="ft-field" required>
                                    <option value="" selected>Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_uom_id" class="ft-label">UOM <span class="text-red-600">*</span></label>
                                <select name="edit_uom_id" id="edit_uom_id" class="ft-field" required>
                                    <option value="" selected>Select UOM</option>
                                    @foreach ($uoms as $uom)
                                        <option value="{{ $uom->id }}">{{ $uom->code }} ({{ $uom->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full md:col-span-1">
                                <label for="edit_unit_price" class="ft-label">Unit Price <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="edit_unit_price" id="edit_unit_price" min="0"
                                    step="0.01" class="ft-field" placeholder="225.50" required>
                            </div>

                            <div class="w-full md:col-span-1">
                                <label for="edit_reorder_quantity" class="ft-label">Reorder Quantity</label>
                                <input type="number" name="edit_reorder_quantity" id="edit_reorder_quantity"
                                    min="0" step="1" class="ft-field" placeholder="100">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_status" class="ft-label">Status <span
                                        class="text-red-600">*</span></label>
                                <select name="edit_status" id="edit_status" class="ft-field" required>
                                    <option value="" selected>Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            Update Item
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End edit modal -->

    <script>
        function markAsComplete(id, requestNumber) {
            if (confirm(
                    `You will not be able to edit the data once marked as complete. Are you sure you want to mark clearance ${requestNumber} as complete?`
                )) {
                $.ajax({
                    url: `/clearance/${id}/mark-complete`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while marking as complete.');
                    }
                });
            }
        }

        function voidClearance(id, requestNumber) {
            if (confirm(
                    `You will not be able to edit the data once voided. Are you sure you want to void clearance ${requestNumber}?`
                )) {
                $.ajax({
                    url: `/clearance/${id}/void`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while voiding the clearance.');
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

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_description').value = button.getAttribute('data-description');
            document.getElementById('edit_supplier_id').value = button.getAttribute('data-supplier_id');
            document.getElementById('edit_category_id').value = button.getAttribute('data-category_id');
            document.getElementById('edit_uom_id').value = button.getAttribute('data-uom_id');
            document.getElementById('edit_unit_price').value = button.getAttribute('data-unit_price');
            document.getElementById('edit_reorder_quantity').value = button.getAttribute('data-reorder_quantity');
            document.getElementById('edit_status').value = button.getAttribute('data-status');

            const form = document.getElementById('editForm');
            form.action = `/supplies/${id}`;
        }
    </script>
@endsection
