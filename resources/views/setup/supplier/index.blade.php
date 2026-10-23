@extends('dashboard')
@section('content')
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>Supplier Management</h1>
                        <p>Manage supplies suppliers, view statistics, and maintain supplier information.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('setup.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>
                        <button data-modal-target="add-modal" data-modal-toggle="add-modal" class="ft-btn ft-btn-primary"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Supplier
                        </button>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Total Suppliers',
                            'value' => $totalSuppliers,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent);" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z"/></svg>',
                        ],
                        [
                            'title' => 'Active',
                            'value' => $activeSuppliers,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>',
                        ],
                        [
                            'title' => 'Inactive',
                            'value' => $inactiveSuppliers,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-danger-text);" fill="currentColor" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach ($cards as $card)
                        <div class="ft-info flex items-center gap-4" style="padding: 1rem;">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                style="background: var(--ft-surface); border: 1px solid var(--ft-line);">
                                {!! $card['icon'] !!}
                            </div>
                            <div>
                                <p class="ft-eyebrow">{{ $card['title'] }}</p>
                                <p class="text-xl font-semibold mt-0.5" style="color: var(--ft-ink);">{{ $card['value'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

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
                    <div class="md:w-1/3 w-full">
                        <select name="status" class="ft-field" onchange="this.form.submit()">
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
                                <th scope="col" class="w-[180px]">Name</th>
                                <th scope="col" class="w-[150px]">Contact Person</th>
                                <th scope="col" class="w-[120px]">Number</th>
                                <th scope="col" class="w-[180px]">Email</th>
                                <th scope="col" class="w-[250px]">Address</th>
                                <th scope="col" class="w-[100px]">Status</th>
                                <th scope="col" class="text-center w-[50px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td class="whitespace-normal break-all align-middle font-semibold"
                                        style="color: var(--ft-ink);">
                                        {{ $supplier->name }}
                                    </td>
                                    <td class="whitespace-normal break-all align-middle" style="color: var(--ft-text);">
                                        {{ $supplier->contact_person ?? '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-middle" style="color: var(--ft-muted);">
                                        {{ $supplier->contact_number ?? '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-middle" style="color: var(--ft-muted);">
                                        {{ $supplier->email ?? '—' }}
                                    </td>
                                    <td class="whitespace-normal break-all align-middle" style="color: var(--ft-muted);">
                                        {{ $supplier->address ?? '—' }}
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $statuses = [
                                                0 => ['class' => 'ft-badge-no', 'label' => 'Inactive'],
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                            ];
                                            $status = $statuses[$supplier->is_active] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }} whitespace-nowrap">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center">
                                            <button type="button" title="Edit Supplier {{ $supplier->name }}"
                                                data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}"
                                                data-contact_person="{{ $supplier->contact_person }}"
                                                data-contact_number="{{ $supplier->contact_number }}"
                                                data-email="{{ $supplier->email }}"
                                                data-address="{{ $supplier->address }}"
                                                data-status="{{ $supplier->is_active }}" onclick="openEditModal(this)"
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="ft-empty">No Suppliers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Supplier modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Add New Supplier
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
                    <form action="{{ route('supplier.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" maxlength="150" class="ft-field"
                                    placeholder="e.g. ABC Company" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="contact_person" class="ft-label">Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" maxlength="50"
                                    class="ft-field" placeholder="e.g. Juan Dela Cruz">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="contact_number" class="ft-label">Telephone</label>
                                <input type="tel" name="contact_number" id="contact_number" maxlength="20"
                                    class="ft-field" placeholder="e.g. 09123456789">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="contact_email" class="ft-label">Email</label>
                                <input type="email" name="contact_email" id="contact_email" maxlength="50"
                                    pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                                    title="Enter a valid email address, e.g. juandelacruz@yahoo.com" class="ft-field"
                                    placeholder="e.g. juandelacruz@yahoo.com">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="address" class="ft-label">Address</label>
                                <textarea name="address" id="address" rows="3" maxlength="255" class="ft-field"
                                    placeholder="Supplier address"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="status" class="ft-label">Status <span class="text-red-600">*</span></label>
                                <select id="status" name="status" class="ft-field" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Add Supplier
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create supplier modal -->

    <!-- Modal Edit -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Update Supplier
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
                            <div class="sm:col-span-2">
                                <label for="edit_name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="edit_name" id="edit_name" maxlength="150" class="ft-field"
                                    placeholder="e.g. ABC Company" required>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_contact_person" class="ft-label">Contact Person</label>
                                <input type="text" name="edit_contact_person" id="edit_contact_person" maxlength="50"
                                    class="ft-field" placeholder="e.g. Juan Dela Cruz">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_contact_number" class="ft-label">Telephone</label>
                                <input type="tel" name="edit_contact_number" id="edit_contact_number" maxlength="20"
                                    class="ft-field" placeholder="e.g. 09123456789">
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_contact_email" class="ft-label">Email</label>
                                <input type="email" name="edit_contact_email" id="edit_contact_email" maxlength="50"
                                    pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                                    title="Enter a valid email address, e.g. juandelacruz@yahoo.com" class="ft-field"
                                    placeholder="e.g. juandelacruz@yahoo.com">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_address" class="ft-label">Address</label>
                                <textarea name="edit_address" id="edit_address" rows="3" class="ft-field" placeholder="Supplier address"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_status" class="ft-label">Status <span
                                        class="text-red-600">*</span></label>
                                <select id="edit_status" name="edit_status" class="ft-field" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            Update Supplier
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End edit modal -->

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

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            document.getElementById('edit_id').value = button.getAttribute('data-id');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_contact_person').value = button.getAttribute('data-contact_person');
            document.getElementById('edit_contact_number').value = button.getAttribute('data-contact_number');
            document.getElementById('edit_contact_email').value = button.getAttribute('data-email');
            document.getElementById('edit_address').value = button.getAttribute('data-address');
            document.getElementById('edit_status').value = button.getAttribute('data-status');

            const form = document.getElementById('editForm');
            form.action = `supplier/${id}`;
        }
    </script>
@endsection
