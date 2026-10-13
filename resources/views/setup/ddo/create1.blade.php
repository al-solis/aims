@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="max-w-2xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">
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
                    DDO Setup
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Setup your DDO regular and reliever employees here. Please fill out the form below to create a new DDO
                    transaction.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">

            <form method="POST" action="{{ $ddo ? route('ddo.update', $ddo) : route('ddo.store') }}"
                enctype="multipart/form-data" id="employeeForm">
                @csrf
                @if ($ddo)
                    @method('PUT')
                @endif
                <div class="grid gap-2 mb-1 sm:grid-cols-4">
                    {{-- <div class="sm:col-span-2">
                        @if ($ddo)
                            <input type="hidden" name="location_id" value="{{ $ddo->location_id }}">
                        @endif

                        <label for="location_id" class="block mb-1 text-xs font-medium text-gray-900">Location</label>
                        <select name="location_id" id="location_id" {{ $ddo ? 'disabled' : '' }}
                            class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            value="{{ old('location_id', $ddo->location_id ?? '') }}" required>
                            <option value="">Select a location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ old('location_id', $ddo->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}

                    <div class="sm:col-span-2">
                        <label for="location_id" class="block mb-1 text-xs font-medium text-gray-900">Location</label>
                        @if ($ddo)
                            <input type="hidden" name="location_id" value="{{ $ddo->location_id }}">
                        @endif
                        <select name="location_id_display" id="location_id" {{ $ddo ? 'disabled' : '' }}
                            class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            {{ $ddo ? '' : 'required' }}>
                            <option value="">Select a location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ old('location_id', $ddo->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="remarks" class="block mb-1 text-xs font-medium text-gray-900">Remarks</label>
                        <input type="text" name="remarks" id="remarks"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            value="{{ old('remarks', $ddo->remarks ?? '') }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="employee_id" class="block mb-1 text-xs font-medium text-gray-900">Employee</label>
                        <select type="text" name="employee_id" id="employee_id"
                            class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                            <option value="">Select an employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employee_id', $ddo->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="type" class="block mb-1 text-xs font-medium text-gray-900">Employee</label>
                        <select type="text" name="type" id="type"
                            class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                            <option value="">Select a type</option>
                            <option value="1" {{ old('type', $ddo->type ?? '') == '1' ? 'selected' : '' }}>
                                Regular</option>
                            <option value="2" {{ old('type', $ddo->type ?? '') == '2' ? 'selected' : '' }}>
                                Reliever</option>
                        </select>
                    </div>
                    <div class="sm:col-span-1 flex items-end">
                        <button type="button" id="addEmployeeButton"
                            class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-plus" viewBox="0 0 16 16">
                                <path
                                    d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                            </svg>
                            Add Employee
                        </button>
                    </div>
                </div>

                <table class="min-w-full text-xs mt-4">
                    <thead class="bg-gray-200 text-gray-600">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left w-[250px]">Employee</th>
                            <th scope="col" class="px-4 py-3 text-left w-[200px]">Type</th>
                            <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Employee rows will be added here dynamically -->
                    </tbody>
                </table>

                <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">
                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('ddo.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>

                    <button type="submit" id="saveButton"
                        class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-floppy" viewBox="0 0 16 16">
                            <path d="M11 2H9v3h2z" />
                            <path
                                d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                        </svg>
                        Save Setup
                    </button>
                </div>
            </form>
        </div>
    </div>

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

            // Remove any error messages after a delay
            setTimeout(() => {
                const errorMessage = document.querySelector('[data-error]');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }, 3000);
        }

        $(document).ready(function() {
            @if ($ddo && $ddo->ddoDetails)
                let existingEmployees = @json($ddo->ddoDetails);

                existingEmployees.forEach(emp => {
                    window.employeeList.push({
                        employee_id: emp.employee_id,
                        name: `${emp.employee.last_name}, ${emp.employee.first_name} ${emp.employee.middle_name ?? ''}`,
                        type: emp.type
                    });
                });

                renderTable();
            @endif

            $('#location_id').select2({
                placeholder: "Select location",
                allowClear: true,
                width: '100%'
            });

            $('#employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });

            $('#location_id').on('change', function() {
                let locationId = $(this).val();

                if (!locationId) return;

                $.ajax({
                    url: "{{ url('/ddo/get-employees-by-location') }}",
                    type: "GET",
                    data: {
                        location_id: locationId
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        let employeeSelect = $('#employee_id');
                        employeeSelect.empty();
                        employeeSelect.append('<option value="">Select an employee</option>');

                        data.employees.forEach(emp => {
                            employeeSelect.append(
                                `<option value="${emp.id}">
                            ${emp.last_name}, ${emp.first_name} ${emp.middle_name ?? ''}
                        </option>`
                            );
                        });

                        employeeSelect.trigger('change'); // refresh select2

                        window.employeeList = [];

                        data.employeeInLocation.forEach(emp => {
                            window.employeeList.push({
                                employee_id: emp.id,
                                name: `${emp.last_name}, ${emp.first_name} ${emp.middle_name ?? ''}`,
                                type: 1
                            });
                        });

                        renderTable();
                    }
                });
            });


            $('#employeeForm').submit(function(e) {
                // alert('Submitting form with ' + employeeList.length + ' employees');
                if (window.employeeList.length === 0) {
                    alert('Please add at least one employee');
                    e.preventDefault();
                    return;
                }

                // remove old inputs
                $('.dynamic-input').remove();

                window.employeeList.forEach((emp, index) => {
                    $('#employeeForm').append(`
                    <input type="hidden" name="employees[${index}][employee_id]" value="${emp.employee_id}" class="dynamic-input">
                    <input type="hidden" name="employees[${index}][type]" value="${emp.type}" class="dynamic-input">
                    `);
                });

            });

        });

        window.employeeList = [];

        $('#addEmployeeButton').click(function() {

            let locationId = $('#location_id').val() || $('input[name="location_id"]').val();;
            let empId = $('#employee_id').val();
            let empText = $('#employee_id option:selected').text();
            let type = $('#type').val();

            if (!locationId) {
                alert('Please select a location first');
                return;
            }

            if (!empId || !type) {
                alert('Please select employee and type');
                return;
            }

            // prevent duplicate in table
            if (window.employeeList.find(e => e.employee_id == empId)) {
                alert('Employee already added');
                return;
            }

            window.employeeList.push({
                employee_id: empId,
                name: empText,
                type: type
            });

            renderTable();

            $('#employee_id').val(null).trigger('change');
            $('#type').val(null).trigger('change');
        });


        function renderTable() {
            let tbody = $('#employeeTableBody');
            tbody.empty();

            window.employeeList.forEach((emp, index) => {
                tbody.append(`
            <tr>
                <td class="px-4 py-2">${emp.name}</td>
                <td class="px-4 py-2">${emp.type==1 ? 'Regular' : 'Reliever'}</td>
                <td class="px-4 py-2 text-center">
                    <button type="button" onclick="removeEmployee(${index})"
                        title="Remove Employee"
                        class="text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                            fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                        </svg>
                        </button>
                </td>
            </tr>
        `);
            });
        }

        function removeEmployee(index) {
            window.employeeList.splice(index, 1);
            renderTable();
        }
    </script>
@endsection
