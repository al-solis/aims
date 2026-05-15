@extends('dashboard')

@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
    @endphp

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-xs p-3 sm:p-8">

            <div class="text-center mb-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Asset Transmittal Form
                </h2>

                <p class="text-sm text-gray-600">
                    Create a new asset transmittal.
                </p>
            </div>

            <hr class="my-3">

            <form
                action="{{ isset($transmittal) ? route('transmittal.update', $transmittal->id) : route('transmittal.store') }}"
                method="POST" id="transmittalForm">
                @csrf
                @if (isset($transmittal))
                    @method('PUT')
                @endif

                <div class="grid gap-2 mb-4 sm:grid-cols-2">

                    <div>
                        <label class="block text-xs font-medium text-gray-900">
                            Date*
                        </label>

                        <input type="date" name="date" max="{{ now()->format('Y-m-d') }}"
                            value="{{ isset($transmittal) ? Carbon::parse($transmittal->transmittal_date)->format('Y-m-d') : now()->format('Y-m-d') }}"
                            class="bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2.5" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-900">
                            Transmitted To
                        </label>

                        <select id="transmit_to" name="transmit_to"
                            class="select2 bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2.5">

                            <option value="">Select employee</option>

                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ isset($transmittal) && $transmittal->transmitted_to == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->last_name }},
                                    {{ $employee->first_name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="sm:col-span-2">

                        <label class="block text-xs font-medium text-gray-900">
                            Location*
                        </label>
                        <input type="hidden" id="location_id" name="location_id"
                            value="{{ $transmittal->location_id ?? '' }}">
                        <select id="location_name" name="location_name" {{ isset($transmittal) ? 'disabled' : '' }}
                            class="select2 bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2.5"
                            required>

                            <option value="">Select location</option>

                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ isset($transmittal) && $transmittal->location_id == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="sm:col-span-2">

                        <label class="block text-xs font-medium text-gray-900">
                            Remarks
                        </label>

                        <textarea name="remarks" rows="3" class="bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2.5"
                            placeholder="Remarks">{{ $transmittal->remarks ?? '' }}</textarea>
                    </div>
                </div>

                <div class="grid gap-2 mb-4 sm:grid-cols-4">
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-medium text-gray-900">
                            Asset*
                        </label>

                        <select id="asset_id"
                            class="select2 bg-gray-50 border border-gray-300 text-xs rounded-lg block w-full p-2.5">
                            <option value="">Select asset</option>
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <button id="add-item-btn" type="button"
                            class="mt-4 h-fit text-white bg-green-700 hover:bg-green-800 font-medium rounded-md text-xs px-4 py-2.5">
                            Add Asset
                        </button>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-4 mb-2">
                    Asset(s) to be transmitted
                </h3>

                <div class="bg-white border rounded-xl overflow-x-auto">

                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-200 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    Asset
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody id="issuanceBody">
                            <tr>
                                <td colspan="2" class="text-center py-4 text-gray-500">
                                    No assets added yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="hiddenInputsContainer"></div>

                <hr class="my-4 mt-4">
                <div class="mt-4 flex justify-end gap-x-2">

                    <a href="{{ route('transmittal.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Back
                    </a>

                    <button type="submit" @disabled(Auth::user()->role == 2 || (isset($transmittal) && $transmittal->status == 0))
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-xs font-medium text-white
                        {{ Auth::user()->role == 2 || (isset($transmittal) && $transmittal->status == 0) ? 'bg-gray-500 cursor-not-allowed' : 'bg-gray-900 hover:bg-gray-800' }} rounded-lg">
                        Save Transaction
                    </button>
                </div>
            </form>
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

            $('#location_name').on('select2:opening', function(e) {

                if (items.length > 0) {
                    e.preventDefault();
                }
            });

            $('#location_name').on('change', function() {
                const locationId = $(this).val();
                $('#location_id').val(locationId);

                // alert(locationId);

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

                            $('#asset_id').append(`
                            <option value="${asset.id}">
                                ${asset.asset_code ?? ''} - ${asset.name}
                            </option>
                        `);
                        });

                        $('#asset_id').prop('disabled', false);
                    }
                });
            });

            const isEdit = @json(isset($transmittal));

            if (isEdit) {
                $('#location_name')
                    .trigger('change')
                    .prop('disabled', true);
            }

            $('#add-item-btn').click(function() {
                addItem();
            });
        });

        function addItem() {

            const assetId = $('#asset_id').val();
            const assetText = $('#asset_id option:selected').text();

            if (!assetId) {
                showToast('Please select an asset', 'error');
                return;
            }

            const existing = items.find(
                item => item.asset_id == assetId
            );

            if (existing) {
                showToast('Asset already added', 'error');
                return;
            }

            const newItem = {
                id: itemId++,
                asset_id: parseInt(assetId),
                asset_text: assetText.trim(),
                quantity: 1
            };

            items.push(newItem);

            renderTable();

            $('#asset_id').val('').trigger('change');

            showToast('Asset added successfully', 'success');
        }

        function renderTable() {

            const tbody = $('#issuanceBody');

            tbody.empty();

            // Disable location if with records
            if (items.length > 0) {

                $('#location_name')
                    .prop('disabled', true);

            } else {

                $('#location_name')
                    .prop('disabled', false);
            }

            if (items.length === 0) {

                tbody.html(`
                    <tr>
                        <td colspan="2"
                            class="text-center py-4 text-gray-500">
                            No assets added yet.
                        </td>
                    </tr>
                `);

                return;
            }

            items.forEach(item => {

                tbody.append(`
                    <tr data-id="${item.id}">
                        <td class="px-4 py-2">
                            ${item.asset_text}
                        </td>

                        <td class="px-4 py-2">
                            <button type="button"
                                title="Remove asset: ${item.asset_text}"
                                onclick="removeItem(${item.id})"
                                class="text-red-600 hover:text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
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
                <input type="hidden"
                    name="items[${index}][asset_id]"
                    value="${item.asset_id}">

                <input type="hidden"
                    name="items[${index}][quantity]"
                    value="1">
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
