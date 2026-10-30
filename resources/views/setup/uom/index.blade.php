@extends('dashboard')
@section('content')
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>UOM Management</h1>
                        <p>Manage units of measure</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('setup.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>
                        <button data-modal-target="add-modal" data-modal-toggle="add-modal"
                            @if (!$canCreate) disabled @endif
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'cursor-not-allowed opacity-50' : '' }}"
                            style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add UOM
                        </button>
                    </div>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Stats Cards --}}
                @php
                    $cards = [
                        [
                            'title' => 'Total UOMs',
                            'value' => $totalUoms,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-accent);" fill="currentColor" viewBox="0 0 16 16"><path d="M1 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h5v-1H2v-1h4v-1H4v-1h2v-1H2v-1h4V9H4V8h2V7H2V6h4V2h1v4h1V4h1v2h1V2h1v4h1V4h1v2h1V2h1v4h1V1a1 1 0 0 0-1-1z"/></svg>',
                        ],
                        [
                            'title' => 'Active',
                            'value' => $activeUoms,
                            'icon' =>
                                '<svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>',
                        ],
                        [
                            'title' => 'Inactive',
                            'value' => $inactiveUoms,
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
                                <th scope="col" class="w-[80px]">Code</th>
                                <th scope="col" class="w-[150px]">Name</th>
                                <th scope="col" class="w-[80px]">Factor</th>
                                <th scope="col" class="w-[80px]">Base</th>
                                <th scope="col" class="w-[80px]">Status</th>
                                <th scope="col" class="text-center w-[50px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($uoms as $uom)
                                <tr>
                                    <td class="whitespace-normal break-all align-top font-semibold"
                                        style="color: var(--ft-ink);">
                                        {{ $uom->code }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-text);">
                                        {{ $uom->name }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $uom->conversion_factor }}
                                    </td>
                                    <td class="whitespace-normal break-all align-top" style="color: var(--ft-muted);">
                                        {{ $uom->base_uom }}
                                    </td>
                                    <td>
                                        @php
                                            $statuses = [
                                                0 => ['class' => 'ft-badge-no', 'label' => 'Inactive'],
                                                1 => ['class' => 'ft-badge-ok', 'label' => 'Active'],
                                            ];
                                            $status = $statuses[$uom->is_active] ?? [
                                                'class' => 'ft-badge-na',
                                                'label' => 'Unknown',
                                            ];
                                        @endphp
                                        <span class="ft-badge {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center">
                                            <button type="button" title="Edit UOM {{ $uom->name }}"
                                                @if (!$canUpdate) disabled @endif
                                                data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                data-id="{{ $uom->id }}" data-name="{{ $uom->name }}"
                                                data-code="{{ $uom->code }}" data-base_uom="{{ $uom->base_uom }}"
                                                data-conversion_factor="{{ $uom->conversion_factor }}"
                                                data-status="{{ $uom->is_active }}" onclick="openEditModal(this)"
                                                class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors
                                                {{ !$canUpdate ? 'cursor-not-allowed opacity-50' : '' }}">
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
                                    <td colspan="6" class="ft-empty">No UOMs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!$canCreate && !$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create or update supplies unit of measure information.
                    </p>
                @elseif (!$canCreate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create supplies unit of measure information.
                    </p>
                @elseif (!$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to update supplies unit of measure information.
                    </p>
                @endif

                {{-- Pagination --}}
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs"
                    style="color: var(--ft-muted);">
                    {{ $uoms->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create UOM modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Add New UOM
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
                <div class="p-4 overflow-y-auto max-h-[70vh]">
                    <form action="{{ route('uom.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 mb-4 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label for="name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" maxlength="20" class="ft-field"
                                    placeholder="e.g. Pack" required>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="code" class="ft-label">Code <span class="text-red-600">*</span></label>
                                <input type="text" name="code" id="code" maxlength="10" class="ft-field"
                                    placeholder="e.g. PK" required>
                            </div>

                            <div class="md:col-span-1">
                                <label for="conversion_factor" class="ft-label">Conversion Factor <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="conversion_factor" id="conversion_factor" min="0.01"
                                    step="0.01" class="ft-field" placeholder="e.g. 12" required>
                            </div>

                            <div class="md:col-span-1">
                                <label for="base_uom" class="ft-label">Base UOM <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="base_uom" id="base_uom" maxlength="10" class="ft-field"
                                    placeholder="e.g. PC" required>
                            </div>

                            <div class="md:col-span-1">
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
                            Add UOM
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create UOM modal -->

    <!-- Modal Edit -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Update UOM
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
                <div class="p-4 overflow-y-auto max-h-[70vh]">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="edit_id" id="edit_id">

                        <div class="grid gap-4 mb-4 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label for="edit_name" class="ft-label">Name <span class="text-red-600">*</span></label>
                                <input type="text" name="edit_name" id="edit_name" maxlength="20" class="ft-field"
                                    placeholder="e.g. Pack" required>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="edit_code" class="ft-label">Code <span class="text-red-600">*</span></label>
                                <input type="text" name="edit_code" id="edit_code" maxlength="10" class="ft-field"
                                    placeholder="e.g. PK" required>
                            </div>

                            <div class="md:col-span-1">
                                <label for="edit_conversion_factor" class="ft-label">Conversion Factor <span
                                        class="text-red-600">*</span></label>
                                <input type="number" name="edit_conversion_factor" id="edit_conversion_factor"
                                    min="0.01" step="0.01" class="ft-field" placeholder="e.g. 12" required>
                            </div>

                            <div class="md:col-span-1">
                                <label for="edit_base_uom" class="ft-label">Base UOM <span
                                        class="text-red-600">*</span></label>
                                <input type="text" name="edit_base_uom" id="edit_base_uom" maxlength="10"
                                    class="ft-field" placeholder="e.g. PC" required>
                            </div>

                            <div class="md:col-span-1">
                                <label for="edit_status" class="ft-label">Status <span
                                        class="text-red-600">*</span></label>
                                <select id="edit_status" name="edit_status" class="ft-field" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="ft-btn ft-btn-primary w-full justify-center">
                            Update UOM
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
            document.getElementById('edit_code').value = button.getAttribute('data-code');
            document.getElementById('edit_conversion_factor').value = button.getAttribute('data-conversion_factor');
            document.getElementById('edit_base_uom').value = button.getAttribute('data-base_uom');
            document.getElementById('edit_status').value = button.getAttribute('data-status');

            const form = document.getElementById('editForm');
            form.action = `uom/${id}`;
        }

        document.getElementById('base_uom').addEventListener('change', function() {
            const baseUom = this.value.trim();
            document.getElementById('base_uom').value = baseUom.toUpperCase();
        });

        document.getElementById('code').addEventListener('change', function() {
            const code = this.value.trim();
            document.getElementById('code').value = code.toUpperCase();
        });
    </script>
@endsection
