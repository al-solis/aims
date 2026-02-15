@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 pt-1">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 pt-1"
                data-success="true">
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Clear form fields after successful submission
                    clearModalFields();
                });
            </script>
        @endif

        <div class="bg-white rounded-xl shadow-xs p-3 sm:p-8">
            <div class="text-center mb-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Odometer Readings
                </h2>
                <p class="text-sm text-gray-600">
                    Display vehicle odometer reading details.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">
            <div class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Asset Code</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $asset->asset_code ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Asset Name</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $asset->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <form action="" method="POST" id="odometerForm">
                @csrf
                @method('PUT')
                <div class="grid ml-1 mr-1 mt-2 gap-2 mb-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="employee_id"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">Employee*</label>
                        <select id="employee_id" name="employee_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option value = "0" selected disabled>Select employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->last_name }},
                                    {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="date" class="block text-xs font-medium text-gray-900 dark:text-white">Date*</label>
                        <input type="date" name="date" id="date" max="{{ now()->format('Y-m-d') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="reading_from" class="block text-xs font-medium text-gray-900 dark:text-white">Reading
                            From*</label>
                        <input type="number" name="reading_from" id="reading_from" min="0"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="reading_to" class="block text-xs font-medium text-gray-900 dark:text-white">Reading
                            To*</label>
                        <input type="number" name="reading_to" id="reading_to" min="0"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required></input>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="remarks"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Provide travel details, etc."></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <button id="add-odometer-btn" type="button"
                            class="mt-4 h-fit text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-sm px-4 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            Add Odometer
                        </button>
                    </div>

                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-4 mb-2">Odometer</h3>

                <div class="bg-white border rounded-xl overflow-x-auto overflow-y-auto md:overflow-visible scroll-smooth">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-200 text-gray-600">
                            <tr class="rounded-xl">
                                <th class="px-4 py-3 text-left w-[120px]">Date</th>
                                <th class="px-4 py-3 text-left w-[120px]">Employee</th>
                                <th class="px-4 py-3 text-left w-[200px]">Remarks</th>
                                <th class="px-4 py-3 text-left w-[100px]">From</th>
                                <th class="px-4 py-3 text-left w-[100px]">To</th>
                                <th class="px-4 py-3 text-left w-[140px]">Action</th>
                            </tr>
                        </thead>

                        <tbody id="odoBody" class="divide-y">
                            {{-- @foreach ($odometerReadings as $detail)
                                <tr class="hover:bg-gray-50">
                                    <input type="hidden" name="odometer_id[]" value="{{ $detail->id }}">

                                    <td class="px-4 py-3 w-[120px]">
                                        {{ Carbon::parse($detail->date ?? 'N/A')->format('Y-m-d') }}
                                    </td>

                                    <td class="px-4 py-3 w-[120px]">
                                        {{ $detail->employee ? $detail->employee->last_name . ', ' . $detail->employee->first_name . ' ' . $detail->employee->middle_name : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 w-[200px]">{{ $detail->remarks ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 w-[100px]">{{ $detail->from_reading ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 w-[100px]">{{ $detail->to_reading ?? 'N/A' }}</td>

                                    <td class="px-4 py-2 w-[50px]">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button type="button" title="Edit asset: {{ $asset->asset_code }}"
                                                data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                                data-id="{{ $asset->id }}" data-code="{{ $asset->asset_code }}"
                                                data-name="{{ $asset->name }}" onclick="openEditModal(this)"
                                                class="group flex items-center space-x-1 text-gray-500 hover:text-blue-600 transition-colors">

                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </button>


                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($odometerReadings->isEmpty())
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-center text-gray-600">
                                        No odometer readings found.
                                    </td>
                                </tr>
                            @endif --}}
                        </tbody>
                    </table>
                    <!-- Pagination Links -->
                    <div
                        class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
                        {{ $odometerReadings->links() }}
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('asset.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>

                    {{-- <button type="submit" id="saveBtn"
                        class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                        Save Changes
                    </button> --}}

                </div>
        </div>

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

            function formatDateForInput(dateString) {
                if (!dateString) return '';

                const date = new Date(dateString);
                if (isNaN(date)) return '';

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            document.addEventListener('DOMContentLoaded', function() {
                const successMessage = document.querySelector('[data-success="true"]');
                if (successMessage) {
                    setTimeout(() => {
                        successMessage.remove();
                    }, 3000); // Remove after 3 seconds
                }

                //LOAD ODOMEETER DETAILS TO TABLE
                const assetId = {{ $asset->id ?? null }};
                if (assetId) {
                    loadOdometerReadings(assetId);
                }

            });

            function resetForm() {

                document.getElementById('employee_id').value = '';
                document.getElementById('date').value = '';
                document.getElementById('reading_from').value = '';
                document.getElementById('reading_to').value = '';
                document.getElementById('remarks').value = '';

                const addBtn = document.getElementById('add-odometer-btn');

                addBtn.textContent = 'Add Odometer Reading';
                addBtn.classList.remove('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-4', 'focus:outline-none',
                    'focus:ring-blue-300', 'font-medium', 'rounded-md', 'text-sm', 'px-4', 'py-2.5', 'text-center',
                    'dark:bg-blue-600', 'dark:hover:bg-blue-700', 'dark:focus:ring-blue-800');
                addBtn.classList.add('bg-green-700', 'hover:bg-green-800', 'focus:ring-4', 'focus:outline-none',
                    'focus:ring-green-300', 'font-medium', 'rounded-md', 'text-sm', 'px-4', 'py-2.5', 'text-center',
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


                if (!employeeId || !date || !readingFrom || !readingTo) {
                    alert('Employee, Date, Reading From and Reading To are required.');
                    return;
                }

                if (recordId) {
                    // alert('Odo: Updating record with ID: ' + recordId);
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
                            // Handle validation errors
                            const errorMessage = Object.values(data.errors)
                                .flat()
                                .join('\n');
                            showToast(errorMessage || 'Validation failed', 'error');
                            return;
                        }

                        // Success case
                        appendRow(data.odometer);
                        showToast(data.message, 'success');

                        // Reset form
                        document.getElementById('employee_id').value = '';
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
                newRow.classList.add('hover:bg-gray-50');
                newRow.innerHTML = `
                <td class="px-2 py-2 font-medium w-[120px]">${odometer.date}</td>
                <td class="px-2 py-2 w-[120px]">${odometer.employee.first_name} ${odometer.employee.middle_name} ${odometer.employee.last_name}</td>
                <td class="px-2 py-2 w-[200px]">${odometer.remarks || ''}</td>
                <td class="px-2 py-2 w-[100px]">${odometer.from_reading || ''}</td>
                <td class="px-2 py-2 w-[100px]">${odometer.to_reading || ''}</td>
                <td class="px-2 py-2 w-[50px]">
                    <div class="flex items-center justify-center">
                        <div class="flex items-center justify-center space-x-2">
                                <button type="button" onclick="editOdometer(${odometer.id})" 
                                    title="Edit odometer reading: ${odometer.from_reading} to ${odometer.to_reading} on ${odometer.date}" 
                                    class="text-blue-600 hover:text-blue-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                        fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                    </svg>
                                </button>
                                <button type="button" onclick="deleteOdometer(${odometer.id})" 
                                    title="Delete odometer reading: ${odometer.from_reading} to ${odometer.to_reading} on ${odometer.date}" 
                                    class="text-red-600 hover:text-red-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                </button>
                        </div>
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
                            // Remove the row from the table
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
                            document.getElementById('employee_id').value = odometer.employee_id;
                            document.getElementById('date').value = odometer.date;
                            document.getElementById('reading_from').value = odometer.from_reading;
                            document.getElementById('reading_to').value = odometer.to_reading;
                            document.getElementById('remarks').value = odometer.remarks;

                            // Change button to "Update Odometer Reading"
                            const addBtn = document.getElementById('add-odometer-btn');
                            addBtn.textContent = 'Update Odometer Reading';
                            addBtn.classList.remove('bg-green-700', 'hover:bg-green-800', 'focus:ring-4',
                                'focus:outline-none', 'focus:ring-green-300', 'font-medium', 'rounded-md', 'text-sm',
                                'px-4', 'py-2.5', 'text-center', 'dark:bg-green-600', 'dark:hover:bg-green-700',
                                'dark:focus:ring-green-800');
                            addBtn.classList.add('bg-blue-700', 'hover:bg-blue-800', 'focus:ring-4', 'focus:outline-none',
                                'focus:ring-blue-300', 'font-medium', 'rounded-md', 'text-sm', 'px-4', 'py-2.5',
                                'text-center', 'dark:bg-blue-600', 'dark:hover:bg-blue-700', 'dark:focus:ring-blue-800');

                            // Store the record ID in a hidden field or data attribute
                            addBtn.setAttribute('data-record-id', odometer.id);

                            // Scroll to the form
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
        </script>

    @endsection
