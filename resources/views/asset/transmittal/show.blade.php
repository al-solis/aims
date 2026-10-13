@extends('dashboard')

@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;

        $fieldClass =
            'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500';
        $labelClass = 'block mb-1.5 text-sm font-medium text-gray-900 dark:text-white';

        $isEdit = isset($transmittal);
        $saveDisabled = Auth::user()->role == 2 || ($isEdit && $transmittal->status == 0);
    @endphp

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

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
        @endif

        @if (session('success'))
            <div data-success="true"
                class="mb-4 flex items-center gap-3 rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800">
                <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Page header --}}
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $isEdit ? 'Edit asset transmittal' : 'New asset transmittal' }}
                </h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                    Choose a location, then add the assets being transmitted from it.
                </p>
            </div>
            <a href="{{ route('transmittal.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to transmittals
            </a>
        </div>

        <form action="{{ $isEdit ? route('transmittal.update', $transmittal->id) : route('transmittal.store') }}"
            method="POST" id="transmittalForm">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- LEFT: transmittal details + summary --}}
                <div class="space-y-5 lg:col-span-1">

                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Transmittal details</h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Fields marked * are required.</p>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="date" class="{{ $labelClass }}">Date *</label>
                                <input type="date" name="date" id="date" max="{{ now()->format('Y-m-d') }}"
                                    value="{{ $isEdit ? Carbon::parse($transmittal->transmittal_date)->format('Y-m-d') : now()->format('Y-m-d') }}"
                                    class="{{ $fieldClass }}" required>
                            </div>

                            <div>
                                <label for="transmit_to" class="{{ $labelClass }}">Transmitted to</label>
                                <select id="transmit_to" name="transmit_to" class="select2 {{ $fieldClass }}">
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
                                <label for="location_name" class="{{ $labelClass }}">Location *</label>
                                <input type="hidden" id="location_id" name="location_id"
                                    value="{{ $transmittal->location_id ?? '' }}">
                                <select id="location_name" name="location_name" {{ $isEdit ? 'disabled' : '' }}
                                    class="select2 {{ $fieldClass }}" required>
                                    <option value="">Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ $isEdit && $transmittal->location_id == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Locked once an asset is added. Remove all assets to change it.
                                </p>
                            </div>

                            <div>
                                <label for="remarks" class="{{ $labelClass }}">Remarks</label>
                                <textarea name="remarks" id="remarks" rows="3" class="{{ $fieldClass }}" placeholder="Add any notes">{{ $transmittal->remarks ?? '' }}</textarea>
                            </div>
                        </div>
                    </section>

                    {{-- Summary + actions --}}
                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800 lg:sticky lg:top-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Summary</h2>

                        <dl class="mt-4 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="font-medium text-gray-900 dark:text-white">Assets to transmit</dt>
                                <dd id="summaryCount" class="text-xl font-semibold text-gray-900 dark:text-white">0</dd>
                            </div>
                        </dl>

                        @if ($saveDisabled)
                            <p
                                class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                You can't save changes to this transmittal.
                            </p>
                        @endif

                        <div class="mt-5 flex gap-2">
                            <a href="{{ route('transmittal.index') }}"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                Cancel
                            </a>
                            <button type="submit" id="saveButton" @disabled($saveDisabled)
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 {{ $saveDisabled ? 'cursor-not-allowed opacity-50' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M11 2H9v3h2z" />
                                    <path
                                        d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                                </svg>
                                Save transmittal
                            </button>
                        </div>
                    </section>
                </div>

                {{-- RIGHT: asset picker + asset list --}}
                <div class="space-y-5 lg:col-span-2">

                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Add an asset</h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            Assets are listed once you choose a location.
                        </p>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="flex-1">
                                <label for="asset_id" class="{{ $labelClass }}">Asset *</label>
                                <select id="asset_id" class="select2 {{ $fieldClass }}">
                                    <option value="">Select asset</option>
                                </select>
                            </div>

                            <button id="add-item-btn" type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                </svg>
                                Add asset
                            </button>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Assets to be transmitted</h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <tr>
                                        <th class="w-12 px-5 py-3 font-medium">#</th>
                                        <th class="px-3 py-3 font-medium">Asset</th>
                                        <th class="w-16 px-5 py-3 text-right font-medium">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="issuanceBody"
                                    class="divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
                                    <tr>
                                        <td colspan="3" class="px-5 py-12 text-center">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">No assets yet</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Choose a location, then add assets to this transmittal.
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div id="hiddenInputsContainer"></div>
                </div>
            </div>
        </form>
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
                        <td colspan="3" class="px-5 py-12 text-center">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">No assets yet</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Choose a location, then add assets to this transmittal.
                            </p>
                        </td>
                    </tr>
                `);
                updateHiddenInputs(); // clear stale inputs after the last asset is removed
                return;
            }

            items.forEach((item, index) => {
                tbody.append(`
                    <tr data-id="${item.id}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">${index + 1}</td>
                        <td class="px-3 py-3 font-medium">${escapeHtml(item.asset_text)}</td>
                        <td class="px-5 py-3 text-right">
                            <button type="button"
                                title="Remove ${escapeHtml(item.asset_text)}"
                                aria-label="Remove ${escapeHtml(item.asset_text)}"
                                onclick="removeItem(${item.id})"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 dark:hover:bg-red-900/30">
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

            toast.className = `
            fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50
            ${type === 'success'
                ? 'bg-green-100 text-green-700 border border-green-300'
                : 'bg-red-100 text-red-700 border border-red-300'}
        `;

            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>

@endsection
