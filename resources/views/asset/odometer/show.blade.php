@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="ft-page max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        @if ($errors->any())
            <div class="ft-alert ft-alert-error mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div class="ft-alert ft-alert-success flex items-center justify-between mb-4" data-success="true">
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

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="text-center">
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--ft-on-ink);">Odometer Readings</h1>
                    <p class="text-sm mt-1" style="color: var(--ft-on-ink-muted);">Display vehicle odometer reading details.
                    </p>
                </div>
            </div>

            <div class="ft-body space-y-6">

                {{-- Asset Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b" style="border-color: var(--ft-line);">
                    <div>
                        <p class="ft-eyebrow">Asset Code</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--ft-ink);">
                            {{ $asset->asset_code ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="ft-eyebrow">Asset Name</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--ft-ink);">
                            {{ $asset->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                {{-- Form --}}
                <form action="" method="POST" id="odometerForm">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <label for="employee_id" class="ft-label">Employee <span class="text-red-600">*</span></label>
                            <select id="employee_id" name="employee_id" class="ft-field select2">
                                <option value="0" selected>Select employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-1">
                            <label for="date" class="ft-label">Date <span class="text-red-600">*</span></label>
                            <input type="date" name="date" id="date" max="{{ now()->format('Y-m-d') }}"
                                class="ft-field">
                        </div>

                        <div class="sm:col-span-1">
                            <label for="reading_from" class="ft-label">Reading From <span
                                    class="text-red-600">*</span></label>
                            <input type="number" name="reading_from" id="reading_from" min="0" class="ft-field">
                        </div>

                        <div class="sm:col-span-1">
                            <label for="reading_to" class="ft-label">Reading To <span class="text-red-600">*</span></label>
                            <input type="number" name="reading_to" id="reading_to" min="0" class="ft-field">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="remarks" class="ft-label">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" class="ft-field" placeholder="Provide travel details, etc."></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            @if ($asset->canBeTransferred())
                                <button id="add-odometer-btn" type="button"
                                    class="ft-btn ft-btn-primary w-full justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Odometer
                                </button>
                            @else
                                <button id="add-odometer-btn" type="button"
                                    class="ft-btn ft-btn-primary w-full justify-center cursor-not-allowed opacity-50"
                                    disabled>
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Odometer
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="ft-divider mt-6 pt-6 flex justify-between items-center">
                        <a href="{{ route('asset.index') }}" class="ft-btn ft-btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        <button type="button" data-modal-target="print-modal" data-modal-toggle="print-modal"
                            class="ft-btn ft-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="mr-1" viewBox="0 0 16 16">
                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                <path
                                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                            </svg>
                            Print
                        </button>
                    </div>
                </form>

                {{-- Table --}}
                <div class="mt-6">
                    <h3 class="ft-section-title" style="margin-top: 0;">Odometer History</h3>
                    <div class="ft-table-wrap">
                        <table class="ft-table">
                            <thead>
                                <tr>
                                    <th class="w-[120px]">Date</th>
                                    <th class="w-[180px]">Employee</th>
                                    <th class="w-[200px]">Remarks</th>
                                    <th class="text-right w-[100px]">From</th>
                                    <th class="text-right w-[100px]">To</th>
                                    <th class="text-center w-[100px]">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="odoBody">
                                {{-- Loaded dynamically via JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-end text-xs mt-4"
                        style="color: var(--ft-muted);">
                        {{ $odometerReadings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print modal -->
    <div id="print-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full bg-black/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg border border-[var(--ft-line)] sm:p-1">
                <!-- Modal header -->
                <div class="flex justify-between items-center p-4 rounded-t border-b border-[var(--ft-line)]">
                    <h3 class="text-base font-bold text-[var(--ft-ink)]">
                        Print Odometer Details
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors"
                        data-modal-toggle="print-modal">
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
                    <form>
                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                            <div>
                                <label for="from_date" class="ft-label">From Date <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="from_date" id="from_date" class="ft-field" required>
                            </div>

                            <div>
                                <label for="to_date" class="ft-label">To Date <span
                                        class="text-red-600">*</span></label>
                                <input type="date" name="to_date" id="to_date" class="ft-field" required>
                            </div>
                        </div>
                        <button type="button" id="printOdometer" class="ft-btn ft-btn-primary w-full justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="mr-1" viewBox="0 0 16 16">
                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                <path
                                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                            </svg>
                            Print
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End print modal -->

    <script>
        $(document).ready(function() {
            $('#employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });
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
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('[data-success="true"]');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.remove();
                }, 3000);
            }

            // LOAD ODOMETER DETAILS TO TABLE
            const assetId = {{ $asset->id ?? null }};
            if (assetId) {
                loadOdometerReadings(assetId);
            }
        });

        function resetForm() {
            document.getElementById('employee_id').value = '0';
            document.getElementById('date').value = '';
            document.getElementById('reading_from').value = '';
            document.getElementById('reading_to').value = '';
            document.getElementById('remarks').value = '';

            const addBtn = document.getElementById('add-odometer-btn');

            addBtn.textContent = 'Add Odometer';
            addBtn.classList.remove('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-4', 'focus:outline-none',
                'focus:ring-blue-300', 'font-medium', 'rounded-md', 'text-xs', 'px-4', 'py-2.5', 'text-center',
                'dark:bg-blue-600', 'dark:hover:bg-blue-700', 'dark:focus:ring-blue-800');
            addBtn.classList.add('bg-green-700', 'hover:bg-green-800', 'focus:ring-4', 'focus:outline-none',
                'focus:ring-green-300', 'font-medium', 'rounded-md', 'text-xs', 'px-4', 'py-2.5', 'text-center',
                'dark:bg-green-600', 'dark:hover:bg-green-700', 'dark:focus:ring-green-800');

            addBtn.removeAttribute('data-record-id');
        }

        function loadOdometerReadings(assetId) {
            fetch(`/asset/${assetId}/odometer-readings`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load odometer readings');
                    }
                    return response.json();
                })
                .then(data => {
                    const tbody = document.getElementById('odoBody');
                    tbody.innerHTML = '';

                    data.odometer.forEach(odometerReadings => {
                        appendRow(odometerReadings);
                    });
                })
                .catch(error => {
                    console.error('Load Error:', error);
                });
        }

        // Save odometer reading
        document.getElementById('add-odometer-btn').addEventListener('click', function() {
            const addBtn = this;
            const recordId = addBtn.getAttribute('data-record-id');

            const assetId = {{ $asset->id ?? null }};
            const employeeId = document.getElementById('employee_id').value;
            const date = document.getElementById('date').value;
            const readingFrom = document.getElementById('reading_from').value;
            const readingTo = document.getElementById('reading_to').value;
            const remarks = document.getElementById('remarks').value;

            if (!employeeId || employeeId == 0 || !date || !readingFrom || !readingTo) {
                alert('Employee, Date, Reading From and Reading To are required.');
                return;
            }

            if (recordId) {
                updateOdometer(recordId);
                return;
            }

            fetch('/asset/odometer/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        asset_id: assetId,
                        employee_id: employeeId,
                        date: date,
                        reading_from: readingFrom,
                        reading_to: readingTo,
                        remarks: remarks
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        const errorMessage = Object.values(data.errors).flat().join('\n');
                        showToast(errorMessage || 'Validation failed', 'error');
                        return;
                    }

                    appendRow(data.odometer);
                    showToast(data.message, 'success');

                    document.getElementById('employee_id').value = '0';
                    document.getElementById('date').value = '';
                    document.getElementById('reading_from').value = '';
                    document.getElementById('reading_to').value = '';
                    document.getElementById('remarks').value = '';
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while saving odometer reading.', 'error');
                });
        });

        function appendRow(odometer) {
            const tableBody = document.getElementById('odoBody');
            const newRow = document.createElement('tr');
            newRow.setAttribute('id', 'row-' + odometer.id);

            newRow.innerHTML = `
                <td class="whitespace-nowrap align-middle font-semibold" style="color: var(--ft-ink);">${odometer.date}</td>
                <td class="align-middle" style="color: var(--ft-text);">${odometer.employee.first_name} ${odometer.employee.middle_name} ${odometer.employee.last_name}</td>
                <td class="align-middle" style="color: var(--ft-muted);">${odometer.remarks || '—'}</td>
                <td class="text-right tabular-nums align-middle" style="color: var(--ft-text);">${odometer.from_reading || ''}</td>
                <td class="text-right tabular-nums align-middle" style="color: var(--ft-text);">${odometer.to_reading || ''}</td>
                <td>
                    <div class="flex items-center justify-center gap-1">                       
                        ${odometer.can_be_transferred ? 
                            `<button type="button" onclick="editOdometer(${odometer.id})" 
                                    title="Edit odometer reading: ${odometer.from_reading} to ${odometer.to_reading} on ${odometer.date}" 
                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                    </svg>
                                </button>
                                <button type="button" onclick="deleteOdometer(${odometer.id})" 
                                    title="Delete odometer reading: ${odometer.from_reading} to ${odometer.to_reading} on ${odometer.date}" 
                                    class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                </button>` 
                            : `<span class="text-xs italic" style="color: var(--ft-muted);">No actions available</span>`}
                    </div>
                </td>
            `;
            tableBody.appendChild(newRow);
        }

        function deleteOdometer(id) {
            const row = document.getElementById(`row-${id}`);
            if (!row) return;

            if (!confirm('Are you sure you want to delete this odometer reading?')) {
                return;
            }

            fetch(`/asset/odometer/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`row-${id}`);
                        if (row) {
                            row.remove();
                        }
                        showToast(data.message || 'Odometer reading deleted successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete odometer reading');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'An error occurred', 'error');
                });
        }

        function editOdometer(id) {
            fetch(`/asset/odometer/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const odometer = data.odometer;
                        $('#employee_id').val(odometer.employee_id).trigger('change');
                        document.getElementById('date').value = odometer.date;
                        document.getElementById('reading_from').value = odometer.from_reading;
                        document.getElementById('reading_to').value = odometer.to_reading;
                        document.getElementById('remarks').value = odometer.remarks;

                        const addBtn = document.getElementById('add-odometer-btn');
                        addBtn.textContent = 'Update Odometer';

                        // Keep it simple by reusing the primary button style, but maybe change color
                        addBtn.classList.remove('bg-green-700', 'hover:bg-green-800', 'focus:ring-4',
                            'focus:outline-none', 'focus:ring-green-300', 'font-medium', 'rounded-md', 'text-sm',
                            'px-4', 'py-2.5', 'text-center', 'dark:bg-green-600', 'dark:hover:bg-green-700',
                            'dark:focus:ring-green-800');
                        addBtn.classList.add('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-4', 'focus:outline-none',
                            'focus:ring-blue-300', 'font-medium', 'rounded-md', 'text-sm', 'px-4', 'py-2.5',
                            'text-center', 'dark:bg-blue-600', 'dark:hover:bg-blue-700', 'dark:focus:ring-blue-800');

                        addBtn.setAttribute('data-record-id', odometer.id);
                        document.getElementById('employee_id').focus();
                    }
                })
                .catch(error => {
                    console.error('Error fetching odometer reading:', error);
                    showToast('Failed to load odometer reading details', 'error');
                });
        }

        function updateOdometer(id) {
            fetch(`/asset/odometer/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        employee_id: document.getElementById('employee_id').value,
                        date: document.getElementById('date').value,
                        reading_from: document.getElementById('reading_from').value,
                        reading_to: document.getElementById('reading_to').value,
                        remarks: document.getElementById('remarks').value,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('row-' + id).remove();
                    appendRow(data.odometer);
                    resetForm();
                });
        }

        // Print odometer details
        document.getElementById('printOdometer').addEventListener('click', function(e) {
            e.preventDefault();

            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;

            if (!fromDate || !toDate) {
                alert('Please select both from and to dates');
                return;
            }

            const baseUrl = "{{ route('asset.odometer.print', $asset->id) }}";
            const url = `${baseUrl}?from_date=${fromDate}&to_date=${toDate}`;
            window.open(url, '_blank');
        });
    </script>
@endsection
