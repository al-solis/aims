@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;

        $fieldClass =
            'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500';
        $labelClass = 'block mb-1.5 text-sm font-medium text-gray-900 dark:text-white';
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
                    {{ $ddo ? 'Edit DDO setup' : 'New DDO setup' }}
                </h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                    Choose a location, then build the list of regular and reliever employees assigned to it.
                </p>
            </div>
            <a href="{{ route('ddo.index') }}" id="closeButton"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to DDO list
            </a>
        </div>

        <form method="POST" action="{{ $ddo ? route('ddo.update', $ddo) : route('ddo.store') }}"
            enctype="multipart/form-data" id="employeeForm">
            @csrf
            @if ($ddo)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">

                {{-- LEFT: setup + add employee --}}
                <div class="space-y-5 lg:col-span-2">

                    {{-- Duty details --}}
                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Duty details</h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            {{ $ddo ? 'The location cannot be changed once the DDO is created.' : 'Picking a location loads its current employees as regular.' }}
                        </p>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="location_id" class="{{ $labelClass }}">Location</label>
                                @if ($ddo)
                                    <input type="hidden" name="location_id" value="{{ $ddo->location_id }}">
                                @endif
                                <select name="location_id_display" id="location_id" {{ $ddo ? 'disabled' : '' }}
                                    class="select2 {{ $fieldClass }}" {{ $ddo ? '' : 'required' }}>
                                    <option value="">Select a location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ old('location_id', $ddo->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="remarks" class="{{ $labelClass }}">Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="{{ $fieldClass }}"
                                    placeholder="Optional note for this DDO"
                                    value="{{ old('remarks', $ddo->remarks ?? '') }}">
                            </div>
                        </div>
                    </section>

                    {{-- Add employee --}}
                    <section class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Add an employee</h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            Use this for relievers or anyone not loaded from the location.
                        </p>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="employee_id" class="{{ $labelClass }}">Employee</label>
                                <select name="employee_id" id="employee_id" class="select2 {{ $fieldClass }}">
                                    <option value="">Select an employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('employee_id', $ddo->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->last_name }}, {{ $employee->first_name }}
                                            {{ $employee->middle_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="type" class="{{ $labelClass }}">Assignment type</label>
                                <select name="type" id="type" class="select2 {{ $fieldClass }}">
                                    <option value="">Select a type</option>
                                    <option value="1" {{ old('type', $ddo->type ?? '') == '1' ? 'selected' : '' }}>
                                        Regular</option>
                                    <option value="2" {{ old('type', $ddo->type ?? '') == '2' ? 'selected' : '' }}>
                                        Reliever</option>
                                </select>
                            </div>

                            <button type="button" id="addEmployeeButton"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gray-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                </svg>
                                Add to roster
                            </button>
                        </div>
                    </section>
                </div>

                {{-- RIGHT: roster --}}
                <section class="flex flex-col rounded-xl bg-white shadow-sm dark:bg-gray-800 lg:col-span-3">
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 p-5 dark:border-gray-700">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Roster</h2>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                <span id="countTotal">0</span> employees on this DDO
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-medium">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-2.5 py-1 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                Regular <span id="countRegular">0</span>
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                                Reliever <span id="countReliever">0</span>
                            </span>
                        </div>
                    </div>

                    {{-- Empty state --}}
                    <div id="emptyState" class="flex flex-1 flex-col items-center justify-center px-6 py-14 text-center">
                        <div
                            class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                            <svg class="h-6 w-6 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">No employees on the roster yet</p>
                        <p class="mt-1 max-w-xs text-sm text-gray-500 dark:text-gray-400">
                            Select a location to load its employees, or add them one at a time.
                        </p>
                    </div>

                    {{-- Table --}}
                    <div id="rosterWrap" class="hidden flex-1 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="w-12 px-5 py-3 font-medium">#</th>
                                    <th scope="col" class="px-3 py-3 font-medium">Employee</th>
                                    <th scope="col" class="w-32 px-3 py-3 font-medium">Type</th>
                                    <th scope="col" class="w-20 px-5 py-3 text-right font-medium">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody"
                                class="divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
                                <!-- Employee rows are rendered by renderTable() -->
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer actions --}}
                    <div
                        class="flex items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60 rounded-b-xl">
                        <a href="{{ route('ddo.index') }}"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" id="saveButton"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path d="M11 2H9v3h2z" />
                                <path
                                    d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                            </svg>
                            Save setup
                        </button>
                    </div>
                </section>
            </div>
        </form>
    </div>

    <script>
        function clearModalFields() {
            const form = document.querySelector('form');
            form.reset();

            setTimeout(() => {
                const successMessage = document.querySelector('[data-success]');
                if (successMessage) successMessage.remove();
            }, 3000);

            setTimeout(() => {
                const errorMessage = document.querySelector('[data-error]');
                if (errorMessage) errorMessage.remove();
            }, 3000);
        }

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c]));
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
                                `<option value="${emp.id}">${escapeHtml(emp.last_name)}, ${escapeHtml(emp.first_name)} ${escapeHtml(emp.middle_name)}</option>`
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
            let locationId = $('#location_id').val() || $('input[name="location_id"]').val();
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
                name: empText.trim(),
                type: type
            });

            renderTable();

            $('#employee_id').val(null).trigger('change');
            $('#type').val(null).trigger('change');
        });

        function renderTable() {
            let tbody = $('#employeeTableBody');
            tbody.empty();

            let regular = 0,
                reliever = 0;

            window.employeeList.forEach((emp, index) => {
                const isRegular = emp.type == 1;
                isRegular ? regular++ : reliever++;

                const badge = isRegular ?
                    '<span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900 dark:text-blue-300">Regular</span>' :
                    '<span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900 dark:text-amber-300">Reliever</span>';

                tbody.append(`
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">${index + 1}</td>
                    <td class="px-3 py-3 font-medium">${escapeHtml(emp.name)}</td>
                    <td class="px-3 py-3">${badge}</td>
                    <td class="px-5 py-3 text-right">
                        <button type="button" onclick="removeEmployee(${index})"
                            title="Remove ${escapeHtml(emp.name)}"
                            aria-label="Remove ${escapeHtml(emp.name)}"
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

            const total = window.employeeList.length;
            $('#countTotal').text(total);
            $('#countRegular').text(regular);
            $('#countReliever').text(reliever);
            $('#emptyState').toggleClass('hidden', total > 0).toggleClass('flex', total === 0);
            $('#rosterWrap').toggleClass('hidden', total === 0).toggleClass('flex', false);
        }

        function removeEmployee(index) {
            window.employeeList.splice(index, 1);
            renderTable();
        }
    </script>
@endsection
