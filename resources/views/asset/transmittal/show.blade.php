@extends('dashboard')

@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;

        $isEdit = isset($transmittal);
        $saveDisabled = Auth::user()->role == 2 || ($isEdit && $transmittal->status == 0);
    @endphp

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form-theme.css') }}">

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
        @endif

        @if (session('success'))
            <div data-success="true" class="ft-alert ft-alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="ft-card">
            <div class="ft-card-head">
                <h1>{{ $isEdit ? 'Edit asset transmittal' : 'New asset transmittal' }}</h1>
                <p>Choose a location, then add the assets being transmitted from it.</p>
            </div>

            <div class="ft-body">
                <form action="{{ $isEdit ? route('transmittal.update', $transmittal->id) : route('transmittal.store') }}"
                    method="POST" id="transmittalForm">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                        {{-- LEFT: transmittal details --}}
                        <div class="lg:col-span-1">
                            <h3 class="ft-section-title" style="margin-top: 0;">Transmittal Details</h3>
                            <p class="text-xs text-gray-500 mb-4">Fields marked * are required.</p>

                            <div class="space-y-4">
                                <div>
                                    <label for="date" class="ft-label">Date *</label>
                                    <input type="date" name="date" id="date" max="{{ now()->format('Y-m-d') }}"
                                        value="{{ $isEdit ? Carbon::parse($transmittal->transmittal_date)->format('Y-m-d') : now()->format('Y-m-d') }}"
                                        class="ft-field" required>
                                </div>

                                <div>
                                    <label for="transmit_to" class="ft-label">Transmitted to</label>
                                    <select id="transmit_to" name="transmit_to" class="select2 ft-field">
                                        <option value="">Select employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ $isEdit && $transmittal->transmitted_to == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->last_name }}, {{ $employee->first_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="location_name" class="ft-label">Location *</label>
                                    <input type="hidden" id="location_id" name="location_id"
                                        value="{{ $transmittal->location_id ?? '' }}">
                                    <select id="location_name" name="location_name" {{ $isEdit ? 'disabled' : '' }}
                                        class="select2 ft-field" required>
                                        <option value="">Select location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ $isEdit && $transmittal->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Locked once an asset is added. Remove all assets to change it.
                                    </p>
                                </div>

                                <div>
                                    <label for="remarks" class="ft-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" rows="3" class="ft-field" placeholder="Add any notes">{{ $transmittal->remarks ?? '' }}</textarea>
                                </div>

                                <div class="ft-info">
                                    <p class="ft-eyebrow">Assets to transmit</p>
                                    <p id="summaryCount" class="ft-info-value">0</p>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: asset picker + asset list --}}
                        <div class="lg:col-span-2">
                            <h3 class="ft-section-title" style="margin-top: 0;">Add an Asset</h3>
                            <p class="text-xs text-gray-500 mb-4">Assets are listed once you choose a location.</p>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                <div class="flex-1">
                                    <label for="asset_id" class="ft-label">Asset *</label>
                                    <select id="asset_id" class="select2 ft-field">
                                        <option value="">Select asset</option>
                                    </select>
                                </div>

                                <button id="add-item-btn" type="button" class="ft-btn ft-btn-primary justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                    </svg>
                                    Add asset
                                </button>
                            </div>

                            <h3 class="ft-section-title">Assets to be Transmitted</h3>
                            <div class="ft-table-wrap">
                                <table class="ft-table">
                                    <thead>
                                        <tr>
                                            <th class="w-12">#</th>
                                            <th>Asset</th>
                                            <th class="w-16 text-right">
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="issuanceBody">
                                        <tr>
                                            <td colspan="3" class="ft-empty">
                                                No assets yet. Choose a location, then add assets to this transmittal.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div id="hiddenInputsContainer"></div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ft-divider mt-8 pt-5 flex flex-wrap items-center justify-end gap-x-2 gap-y-3">
                        @if ($saveDisabled)
                            <p class="mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                                You can't save changes to this transmittal.
                            </p>
                        @endif

                        <a href="{{ route('transmittal.index') }}" class="ft-btn ft-btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        <button type="submit" id="saveButton" class="ft-btn ft-btn-primary" @disabled($saveDisabled)>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path d="M11 2H9v3h2z" />
                                <path
                                    d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                            </svg>
                            Save transmittal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <script>
        let items = [];
        let itemId = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const transmittalId = @json($transmittal->id ?? null);

            if (transmittalId) {
                $('#location_name').prop('disabled', true);

                $.ajax({
                    url: `/get-transmittal-items/${transmittalId}`,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(item => {
                            items.push({
                                id: itemId++,
                                asset_id: item.asset_id,
                                asset_text: item.asset_text
                            });
                        });

                        renderTable();
                    }
                });
            }
        });

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            // Location is locked while the list has assets
            $('#location_name').on('select2:opening', function(e) {
                if (items.length > 0) {
                    e.preventDefault();
                }
            });

            $('#location_name').on('change', function() {
                const locationId = $(this).val();
                $('#location_id').val(locationId);

                $('#asset_id')
                    .html('<option>Loading...</option>')
                    .prop('disabled', true);

                if (!locationId) {
                    $('#asset_id')
                        .html('<option value="">Select asset</option>')
                        .prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: `/get-assets/${locationId}`,
                    type: 'GET',

                    success: function(data) {
                        $('#asset_id')
                            .empty()
                            .append('<option value="">Select asset</option>');

                        $.each(data, function(_, asset) {
                            $('#asset_id').append(
                                $('<option>')
                                .val(asset.id)
                                .text(`${asset.asset_code ?? ''} - ${asset.name}`)
                            );
                        });

                        $('#asset_id').prop('disabled', false);
                    },

                    error: function() {
                        $('#asset_id')
                            .html('<option value="">Select asset</option>')
                            .prop('disabled', false);
                        showToast('Could not load assets for this location', 'error');
                    }
                });
            });

            const isEdit = @json($isEdit);

            if (isEdit) {
                $('#location_name')
                    .trigger('change')
                    .prop('disabled', true);
            }

            $('#add-item-btn').click(function() {
                addItem();
            });

            renderTable();
        });

        function addItem() {
            const assetId = $('#asset_id').val();
            const assetText = $('#asset_id option:selected').text();

            if (!assetId) {
                showToast('Please select an asset', 'error');
                return;
            }

            const existing = items.find(item => item.asset_id == assetId);

            if (existing) {
                showToast('Asset already added', 'error');
                return;
            }

            items.push({
                id: itemId++,
                asset_id: parseInt(assetId),
                asset_text: assetText.trim(),
                quantity: 1
            });

            renderTable();

            $('#asset_id').val('').trigger('change');

            showToast('Asset added successfully', 'success');
        }

        function renderTable() {
            const tbody = $('#issuanceBody');
            tbody.empty();

            // Lock the location while there are assets
            $('#location_name').prop('disabled', items.length > 0);
            $('#summaryCount').text(items.length);

            if (items.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="3" class="ft-empty">
                            No assets yet. Choose a location, then add assets to this transmittal.
                        </td>
                    </tr>
                `);
                updateHiddenInputs(); // clear stale inputs after the last asset is removed
                return;
            }

            items.forEach((item, index) => {
                tbody.append(`
                    <tr data-id="${item.id}">
                        <td>${index + 1}</td>
                        <td>${escapeHtml(item.asset_text)}</td>
                        <td class="text-right">
                            <button type="button"
                                title="Remove ${escapeHtml(item.asset_text)}"
                                aria-label="Remove ${escapeHtml(item.asset_text)}"
                                onclick="removeItem(${item.id})"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `);
            });

            updateHiddenInputs();
        }

        function updateHiddenInputs() {
            const container = $('#hiddenInputsContainer');
            container.empty();

            items.forEach((item, index) => {
                container.append(`
                    <input type="hidden" name="items[${index}][asset_id]" value="${item.asset_id}">
                    <input type="hidden" name="items[${index}][quantity]" value="1">
                `);
            });
        }

        window.removeItem = function(id) {
            items = items.filter(i => i.id !== id);
            renderTable();
            showToast('Asset removed successfully', 'success');
        }

        $('#transmittalForm').submit(function(e) {
            if (items.length === 0) {
                e.preventDefault();
                showToast('Please add at least one asset', 'error');
                return false;
            }
            return true;
        });

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

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');

            toast.className = 'ft-alert fixed top-4 right-4 z-50 max-w-sm shadow-lg ' +
                (type === 'success' ? 'ft-alert-success' : 'ft-alert-error');
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>

@endsection
