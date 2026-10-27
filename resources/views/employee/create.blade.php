@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;
    @endphp

    {{-- Colors come from <html data-theme="..."> defined in the layout; do not set data-theme here --}}
    <style>
        /* Tabs and a few states built only from theme variables */
        .ft-tabs {
            border-bottom: 1px solid var(--ft-line);
        }

        .ft-tab {
            display: inline-block;
            padding: .75rem 1rem;
            margin-bottom: -1px;
            border-bottom: 2px solid transparent;
            border-radius: 6px 6px 0 0;
            font-size: .8rem;
            font-weight: 600;
            transition: color .15s, border-color .15s;
        }

        .ft-tab:focus-visible {
            outline: 2px solid var(--ft-accent);
            outline-offset: 2px;
        }

        .ft-tab-active {
            color: var(--ft-ink);
            border-color: var(--ft-accent);
        }

        .ft-tab-inactive {
            color: var(--ft-muted);
            border-color: transparent;
        }

        .ft-tab-inactive:hover {
            color: var(--ft-ink);
            border-color: var(--ft-border);
        }

        .ft-field.is-invalid {
            border-color: var(--ft-danger-solid);
        }

        .emp-photo {
            border: 1px solid var(--ft-border);
        }
    </style>

    <div class="ft-page max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        {{-- Alerts --}}
        @if ($errors->any())
            <div class="ft-alert ft-alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div class="ft-alert ft-alert-success" data-success="true">
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Clear form fields after successful submission
                    clearModalFields();
                });
            </script>
        @endif

        <div class="ft-card">
            <div class="ft-card-head">
                <h1>Employee Information</h1>
                <p>Display employee information.</p>
            </div>

            <div class="ft-body">
                <form method="POST"
                    action="{{ $employee ? route('employee.update', $employee->id) : route('employee.store') }}"
                    enctype="multipart/form-data" id="employeeForm">
                    @csrf
                    @if ($employee)
                        @method('PUT')
                    @endif

                    {{-- Tabs --}}
                    <div class="ft-tabs mb-5">
                        <ul class="flex flex-wrap" id="employee-tab" data-tabs-toggle="#employee-tab-content"
                            data-tabs-active-classes="ft-tab-active" data-tabs-inactive-classes="ft-tab-inactive"
                            role="tablist">
                            <li class="me-1" role="presentation">
                                <button class="ft-tab" id="basic-tab" data-tabs-target="#basic" type="button"
                                    role="tab" aria-controls="basic" aria-selected="true">Basic Info</button>
                            </li>

                            <li class="me-1" role="presentation">
                                <button class="ft-tab" id="employment-tab" data-tabs-target="#employment" type="button"
                                    role="tab" aria-controls="employment" aria-selected="false">Employment
                                    Details</button>
                            </li>

                            @if ($employee)
                                <li class="me-1" role="presentation">
                                    <button class="ft-tab" id="idinfo-tab" data-tabs-target="#idinfo" type="button"
                                        role="tab" aria-controls="idinfo" aria-selected="false">ID Info</button>
                                </li>

                                <li class="me-1" role="presentation">
                                    <button class="ft-tab" id="history-tab" data-tabs-target="#history" type="button"
                                        role="tab" aria-controls="history" aria-selected="false">Employment
                                        History</button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div id="employee-tab-content">

                        {{-- BASIC INFO --}}
                        <div id="basic" role="tabpanel" aria-labelledby="basic-tab">
                            <p class="mb-4 text-xs text-gray-500">Please fill the following information
                                <strong class="font-semibold text-gray-700">Basic Information tab's associated
                                    content</strong>.
                            </p>

                            <div class="grid gap-4 sm:grid-cols-3 mb-4">
                                <input type="hidden" id="id" name="id"
                                    value="{{ old('id', $employee->id ?? '') }}">

                                <input type="hidden" name="employee_path" id="employee_path"
                                    value="{{ old('employee_path', $employee->photo_path ?? '') }}">
                                <input type="hidden" name="temp_photo_name" id="temp_photo_name" value="">

                                <div class="sm:col-span-1">
                                    <label for="imgPreview" class="ft-label">Employee Photo (Click to upload)</label>
                                    <div class="flex items-center gap-3 mb-2">
                                        <img id="empPreview"
                                            class="emp-photo inline-block size-32 rounded-lg cursor-pointer hover:opacity-80 transition-opacity duration-200"
                                            src="{{ $employee && $employee->photo_path ? asset('storage/' . $employee->photo_path) : asset('storage/default/employee.jpg') }}"
                                            alt="Employee photo" title="Click to upload photo">
                                        <input type="file" id="photoInput" name="photo" accept="image/*"
                                            class="hidden">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Click on the image to upload a photo (Max: 2MB)
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="idno" class="ft-label">ID No*</label>
                                            <input type="text" name="idno" id="idno"
                                                value="{{ old('idno', $employee->employee_code ?? $employeeNumber) }}"
                                                class="ft-field" placeholder="e.g. 001-26, 2026-00001" required>
                                            <small id="id-feedback" class="text-red-500 text-xs mb-1 hidden">
                                                ID number already exists in another record.
                                            </small>
                                        </div>

                                        <div>
                                            <label for="date" class="ft-label">Date Hired*</label>
                                            <input type="date" name="date" id="date"
                                                value="{{ old('date', $employee->hire_date ?? '') }}" class="ft-field"
                                                placeholder="e.g. 04/08/1984" required>
                                        </div>

                                        <div>
                                            <label for="status" class="ft-label">Status*</label>
                                            <select id="status" name="status" class="ft-field" required>
                                                <option value="0" @selected(old('status', $employee->status ?? 0) == 0)>Inactive</option>
                                                <option value="1" @selected(old('status', $employee->status ?? 1) == 1)>Active</option>
                                                <option value="2" @selected(old('status', $employee->status ?? 2) == 2)>On leave</option>
                                                <option value="3" @selected(old('status', $employee->status ?? 3) == 3)>Resigned</option>
                                                <option value="4" @selected(old('status', $employee->status ?? 4) == 4)>Retired</option>
                                                <option value="5" @selected(old('status', $employee->status ?? 5) == 5)>Terminated</option>
                                                <option value="6" @selected(old('status', $employee->status ?? 6) == 6)>AWOL</option>
                                                <option value="7" @selected(old('status', $employee->status ?? 7) == 7)>Deceased</option>
                                                <option value="8" @selected(old('status', $employee->status ?? 8) == 8)>Dropped</option>
                                                <option value="9" @selected(old('status', $employee->status ?? 9) == 9)>Labor</option>
                                                <option value="10" @selected(old('status', $employee->status ?? 10) == 10)>Floating</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="termination_date" class="ft-label">Termination Date</label>
                                            <input type="date" name="termination_date" id="termination_date"
                                                value="{{ old('termination_date', $employee->termination_date ?? '') }}"
                                                class="ft-field">
                                        </div>

                                        <div>
                                            <label for="gender" class="ft-label">Gender*</label>
                                            <select id="gender" name="gender" class="ft-field" required>
                                                <option value="1" @selected(old('gender', $employee->gender ?? 1) == 1)>Male</option>
                                                <option value="2" @selected(old('gender', $employee->gender ?? 2) == 2)>Female</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="marital_status" class="ft-label">Marital Status*</label>
                                            <select id="marital_status" name="marital_status" class="ft-field" required>
                                                <option value="1" @selected(old('marital_status', $employee->marital_status ?? 1) == 1)>Single</option>
                                                <option value="2" @selected(old('marital_status', $employee->marital_status ?? 2) == 2)>Married</option>
                                                <option value="3" @selected(old('marital_status', $employee->marital_status ?? 3) == 3)>Widowed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-3 mb-4 sm:grid-cols-1 md:grid-cols-3">
                                <div class="w-full">
                                    <label for="lname" class="ft-label">Last Name*</label>
                                    <input type="text" name="lname" id="lname"
                                        value="{{ old('lname', $employee->last_name ?? '') }}" class="ft-field"
                                        placeholder="e.g. Dela Cruz" required>
                                </div>
                                <div class="w-full">
                                    <label for="fname" class="ft-label">First Name*</label>
                                    <input type="text" name="fname" id="fname"
                                        value="{{ old('fname', $employee->first_name ?? '') }}" class="ft-field"
                                        placeholder="e.g. Juan" required>
                                </div>
                                <div class="w-full">
                                    <label for="mname" class="ft-label">Middle Name</label>
                                    <input type="text" name="mname" id="mname"
                                        value="{{ old('mname', $employee->middle_name ?? '') }}" class="ft-field"
                                        placeholder="e.g. Santos">
                                </div>
                            </div>

                            <div class="grid gap-3 mb-4 sm:grid-cols-1 md:grid-cols-3">
                                <div class="w-full">
                                    <label for="bday" class="ft-label">Birthday</label>
                                    <input type="date" name="bday" id="bday"
                                        value="{{ old('bday', $employee->date_of_birth ?? '') }}" class="ft-field"
                                        placeholder="e.g. mm/dd/yyyy">
                                </div>
                                <div class="w-full">
                                    <label for="mobile" class="ft-label">Mobile No</label>
                                    <input type="tel" name="mobile" id="mobile"
                                        value="{{ old('mobile', $employee->mobile ?? '') }}" class="ft-field"
                                        placeholder="e.g. 09xxxxxxxxx">
                                </div>
                                <div class="w-full">
                                    <label for="email" class="ft-label">Email</label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $employee->email ?? '') }}" class="ft-field"
                                        placeholder="e.g. juandelacruz@yahoo.com">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="ft-label">Address*</label>
                                <textarea name="address" id="address" rows="3" class="ft-field" placeholder="Address..." required>{{ old('address', $employee->address ?? '') }}</textarea>
                            </div>

                            <div class="grid gap-3 mb-4 sm:grid-cols-1 md:grid-cols-4">
                                <div class="w-full">
                                    <label for="city" class="ft-label">City</label>
                                    <input type="text" name="city" id="city"
                                        value="{{ old('city', $employee->city ?? '') }}" class="ft-field"
                                        placeholder="e.g. City">
                                </div>
                                <div class="w-full">
                                    <label for="state" class="ft-label">State/ Province</label>
                                    <input type="text" name="state" id="state"
                                        value="{{ old('state', $employee->state ?? '') }}" class="ft-field"
                                        placeholder="e.g. State/ Province">
                                </div>
                                <div class="w-full">
                                    <label for="country" class="ft-label">Country</label>
                                    <input type="text" name="country" id="country"
                                        value="{{ old('country', $employee->country ?? '') }}" class="ft-field"
                                        placeholder="e.g. Philippines">
                                </div>
                                <div class="w-full">
                                    <label for="zip" class="ft-label">Zip Code</label>
                                    <input type="text" name="zip" id="zip"
                                        value="{{ old('zip', $employee->postal_code ?? '') }}" class="ft-field"
                                        placeholder="e.g. 4103">
                                </div>
                            </div>

                            <div class="grid gap-3 mb-2 sm:grid-cols-1 md:grid-cols-2">
                                <div class="w-full">
                                    <label for="highest_education" class="ft-label">Highest Education</label>
                                    <input type="text" name="highest_education" id="highest_education"
                                        value="{{ old('highest_education', $employee->highest_education ?? '') }}"
                                        class="ft-field" placeholder="e.g. College Graduate">
                                </div>

                                <div class="w-full">
                                    <label for="school" class="ft-label">School</label>
                                    <input type="text" name="school" id="school"
                                        value="{{ old('school', $employee->school ?? '') }}" class="ft-field"
                                        placeholder="e.g. University of the Philippines">
                                </div>

                                <div class="w-full">
                                    <label for="course" class="ft-label">Course</label>
                                    <input type="text" name="course" id="course"
                                        value="{{ old('course', $employee->course ?? '') }}" class="ft-field"
                                        placeholder="e.g. Bachelor of Science in Computer Science">
                                </div>

                                <div class="w-full">
                                    <label for="year_attended" class="ft-label">Year Attended/ Graduated</label>
                                    <input type="text" name="year_attended" id="year_attended"
                                        value="{{ old('year_attended', $employee->year_attended ?? '') }}"
                                        class="ft-field" placeholder="e.g. 2024-2025">
                                </div>

                                <div class="w-full">
                                    <label for="emergency" class="ft-label">Emergency Contact</label>
                                    <input type="text" name="emergency" id="emergency"
                                        value="{{ old('emergency', $employee->emergency_contact ?? '') }}"
                                        class="ft-field" placeholder="e.g. Emergency Contact">
                                </div>
                                <div class="w-full">
                                    <label for="e_no" class="ft-label">Emergency No.</label>
                                    <input type="tel" name="e_no" id="e_no"
                                        value="{{ old('e_no', $employee->emergency_phone ?? '') }}" class="ft-field"
                                        placeholder="e.g. 09xxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        {{-- EMPLOYMENT DETAILS --}}
                        <div id="employment" role="tabpanel" aria-labelledby="employment-tab">
                            <p class="mb-4 text-xs text-gray-500">Please fill the following information
                                <strong class="font-semibold text-gray-700">Employment Details tab's associated
                                    content</strong>.
                            </p>

                            <div class="grid gap-3 mb-3 sm:grid-cols-1 md:grid-cols-2">
                                <div class="w-full">
                                    <label for="position" class="ft-label">Position</label>
                                    <input type="text" name="position" id="position"
                                        value="{{ old('position', $employee->position ?? '') }}" class="ft-field"
                                        placeholder="e.g. Manager">
                                </div>
                                <div class="w-full">
                                    <label for="department" class="ft-label">Department</label>
                                    <select name="department" id="department" class="ft-field">
                                        <option selected="" value="">Select Department/ Location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ old('department', $employee->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-3 mb-2 sm:grid-cols-1 md:grid-cols-2">
                                <div>
                                    <label for="employment_type" class="ft-label">Employment Type*</label>
                                    <select id="employment_type" name="employment_type" class="ft-field" required>
                                        <option value="1" @selected(old('employment_type', $employee->employment_type ?? 1) == 1)>Regular</option>
                                        <option value="2" @selected(old('employment_type', $employee->employment_type ?? 1) == 2)>Contractual</option>
                                        <option value="3" @selected(old('employment_type', $employee->employment_type ?? 1) == 3)>Probationary</option>
                                    </select>
                                </div>

                                <div class="w-full">
                                    <label for="monthly_salary" class="ft-label">Monthly Salary</label>
                                    <input type="number" name="monthly_salary" id="monthly_salary"
                                        value="{{ old('monthly_salary', $employee->monthly_salary ?? '') }}"
                                        class="ft-field" placeholder="e.g. 50000">
                                </div>

                                <div class="w-full">
                                    <label for="daily_rate" class="ft-label">Daily Rate</label>
                                    <input type="number" name="daily_rate" id="daily_rate"
                                        value="{{ old('daily_rate', $employee->daily_rate ?? '') }}" class="ft-field"
                                        placeholder="e.g. 2000">
                                </div>

                                <div class="w-full">
                                    <label for="sss_rate" class="ft-label">SSS Rate</label>
                                    <input type="number" name="sss_rate" id="sss_rate"
                                        value="{{ old('sss_rate', $employee->sss_rate ?? '') }}" class="ft-field"
                                        placeholder="e.g. 500">
                                </div>

                                <div class="w-full">
                                    <label for="philhealth_rate" class="ft-label">PhilHealth Rate</label>
                                    <input type="number" name="philhealth_rate" id="philhealth_rate"
                                        value="{{ old('philhealth_rate', $employee->philhealth_rate ?? '') }}"
                                        class="ft-field" placeholder="e.g. 250">
                                </div>

                                <div class="w-full">
                                    <label for="pagibig_rate" class="ft-label">PAGIBIG Rate</label>
                                    <input type="number" name="pagibig_rate" id="pagibig_rate"
                                        value="{{ old('pagibig_rate', $employee->pagibig_rate ?? '') }}" class="ft-field"
                                        placeholder="e.g. 250">
                                </div>
                            </div>
                        </div>

                        @if ($employee)
                            {{-- ID INFORMATION --}}
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <div id="employeeIdSection" data-employee-id="{{ $employee->id }}">
                                <div class="hidden" id="idinfo" role="tabpanel" aria-labelledby="idinfo-tab">
                                    <p class="mb-4 text-xs text-gray-500">Maintenance of
                                        <strong class="font-semibold text-gray-700">Employee ID</strong> records.
                                    </p>
                                    <input type="hidden" id="employee_id_record_id" name="employee_id_record_id"
                                        value="">

                                    <div class="grid gap-3 mb-3 sm:grid-cols-1 md:grid-cols-3">
                                        <div class="w-full">
                                            <label for="employee_id_type" class="ft-label">ID Type*</label>
                                            <select name="employee_id_type" id="employee_id_type" class="ft-field">
                                                <option selected="" value="">Select ID Type</option>
                                                @foreach ($idTypes as $idType)
                                                    <option value="{{ $idType->id }}">{{ $idType->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-full">
                                            <label for="employee_id_number" class="ft-label">ID Number*</label>
                                            <input type="text" name="employee_id_number" id="employee_id_number"
                                                class="ft-field" placeholder="e.g. 242-327-268">
                                        </div>
                                        <div class="w-full">
                                            <label for="badge" class="ft-label">Badge Number</label>
                                            <input type="text" name="badge" id="badge" class="ft-field"
                                                placeholder="e.g. 04-4146789-0">
                                        </div>
                                    </div>

                                    <div class="grid gap-3 mb-4 sm:grid-cols-1 md:grid-cols-3">
                                        <div class="w-full">
                                            <label for="employee_id_issued_date" class="ft-label">Issued Date</label>
                                            <input type="date" name="employee_id_issued_date"
                                                id="employee_id_issued_date" class="ft-field"
                                                placeholder="e.g. mm/dd/yyyy">
                                        </div>
                                        <div class="w-full">
                                            <label for="employee_id_expiry_date" class="ft-label">Expiry Date</label>
                                            <input type="date" name="employee_id_expiry_date"
                                                id="employee_id_expiry_date" class="ft-field"
                                                placeholder="e.g. mm/dd/yyyy">
                                        </div>

                                        <div class="flex items-end">
                                            <button id="add-id-btn" type="button" class="ft-btn ft-btn-primary"
                                                @if (!$canCreate || !$canUpdate) disabled @endif>
                                                Add ID
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Table --}}
                                    <div class="ft-table-wrap">
                                        <table class="ft-table" id="employeeIdsTable">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID Type</th>
                                                    <th scope="col">ID Number</th>
                                                    <th scope="col">Badge Number</th>
                                                    <th scope="col">Issued</th>
                                                    <th scope="col">Expiry</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody id="employeeIdsBody">
                                                @forelse($employeeIds as $employeeId)
                                                    <tr id="row-{{ $employeeId->id }}">
                                                        <td>{{ $employeeId->idType->name }}</td>
                                                        <td>{{ $employeeId->id_number }}</td>
                                                        <td>{{ $employeeId->badge }}</td>
                                                        <td>{{ $employeeId->issue_date }}</td>
                                                        <td>{{ $employeeId->expiry_date }}</td>
                                                        <td>
                                                            <div class="flex items-center gap-1">
                                                                <button type="button"
                                                                    onclick="editEmployeeId({{ $employeeId->id }})"
                                                                    title="Edit ID"
                                                                    class="rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                                        <path fill-rule="evenodd"
                                                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                                    </svg>
                                                                </button>
                                                                <button type="button"
                                                                    onclick="deleteEmployeeId({{ $employeeId->id }})"
                                                                    title="Delete ID"
                                                                    class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                                        <path
                                                                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr id="no-ids-row">
                                                        <td colspan="6" class="ft-empty">No IDs found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- EMPLOYMENT HISTORY --}}
                            <div class="hidden" id="history" role="tabpanel" aria-labelledby="history-tab">
                                <p class="mb-4 text-xs text-gray-500">Employee's <strong
                                        class="font-semibold text-gray-700">Work History</strong>.</p>

                                <div class="grid gap-3 mb-3 sm:grid-cols-1 md:grid-cols-2">
                                    <div class="w-full">
                                        <label for="company_name" class="ft-label">Company Name*</label>
                                        <input type="text" name="company_name" id="company_name" class="ft-field"
                                            placeholder="e.g. ABC Corporation">
                                    </div>
                                    <div class="w-full">
                                        <label for="position_held" class="ft-label">Position Held*</label>
                                        <input type="text" name="position_held" id="position_held" class="ft-field"
                                            placeholder="e.g. Software Engineer">
                                    </div>
                                </div>
                                <div class="grid gap-3 mb-4 sm:grid-cols-1 md:grid-cols-3">
                                    <div class="w-full">
                                        <label for="start_date" class="ft-label">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="ft-field"
                                            placeholder="e.g. mm/dd/yyyy">
                                    </div>
                                    <div class="w-full">
                                        <label for="end_date" class="ft-label">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="ft-field"
                                            placeholder="e.g. mm/dd/yyyy">
                                    </div>

                                    <div class="flex items-end">
                                        <button id="add-workhistory-btn" type="button" class="ft-btn ft-btn-primary"
                                            @if (!$canCreate || !$canUpdate) disabled @endif>
                                            Add Work History
                                        </button>
                                    </div>
                                </div>

                                {{-- Table --}}
                                <div class="ft-table-wrap">
                                    <table class="ft-table" id="employeeWorkHistoryTable">
                                        <thead>
                                            <tr>
                                                <th scope="col">Company Name</th>
                                                <th scope="col">Position Held</th>
                                                <th scope="col">Start Date</th>
                                                <th scope="col">End Date</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody id="employeeWorkHistoryBody">
                                            {{-- Work history records will be dynamically added here --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="ft-divider mt-8 pt-5 flex justify-end gap-x-2">
                        <a href="{{ route('employee.index') }}" id="closeButton" class="ft-btn ft-btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>
                        <button type="submit" id="saveBtn" class="ft-btn ft-btn-primary"
                            @if (!$canCreate || !$canUpdate) disabled @endif>
                            @if ($employee)
                                Save changes
                            @else
                                Create Employee
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const canDelete = @json($canDelete ?? false);

        let uploadedTempPhoto = null;
        let isSubmitting = false;

        // Shared markup for the edit / delete icon buttons in the dynamic tables
        const BTN_EDIT_CLASS =
            'rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300';
        const BTN_DELETE_CLASS =
            'rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300';
        const ICON_EDIT =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>';
        const ICON_TRASH =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg>';

        function actionButtons(editCall, deleteCall, editTitle, deleteTitle) {
            return `
                <div class="flex items-center gap-1">
                    <button type="button" onclick="${editCall}" title="${editTitle}" class="${BTN_EDIT_CLASS}">${ICON_EDIT}</button>
                    <button type="button" onclick="${deleteCall}" title="${deleteTitle}" class="${BTN_DELETE_CLASS}">${ICON_TRASH}</button>
                </div>`;
        }

        function clearModalFields() {
            const form = document.querySelector('form');
            form.reset();

            const imgPreview = document.getElementById('empPreview');
            imgPreview.src = "{{ asset('storage/default/employee.jpg') }}";

            document.getElementById('employee_path').value = '';
            document.getElementById('temp_photo_name').value = '';

            if (uploadedTempPhoto) {
                cleanupTempFile(uploadedTempPhoto.path);
                uploadedTempPhoto = null;
            }
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


        function cleanupTempFile(filePath) {
            if (!filePath) return;

            fetch('{{ route('employee.cleanupTempFile') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        file_path: filePath
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Temp file cleaned up:', data);
                })
                .catch(err => {
                    console.error('Error cleaning up temp file:', err);
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const imgPreview = document.getElementById('empPreview');
            const fileInput = document.getElementById('photoInput');
            const employeePathInput = document.getElementById('employee_path');
            const tempPhotoNameInput = document.getElementById('temp_photo_name');

            imgPreview.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function() {
                if (fileInput.files && fileInput.files[0]) {
                    const maxSize = 2 * 1024 * 1024;
                    if (fileInput.files[0].size > maxSize) {
                        alert('File size exceeds 2MB limit. Please choose a smaller file.');
                        fileInput.value = '';
                        return;
                    }

                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(fileInput.files[0].type)) {
                        alert('Please select a valid image file (JPEG, PNG, JPG, GIF, WEBP).');
                        fileInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(fileInput.files[0]);

                    uploadImageToServer(fileInput.files[0]);
                }
            });

            function uploadImageToServer(file) {
                const formData = new FormData();
                formData.append('photo', file);
                formData.append('_token', '{{ csrf_token() }}');

                imgPreview.classList.add('opacity-50');
                imgPreview.title = 'Uploading...';

                fetch('{{ route('employee.uploadImage') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        imgPreview.classList.remove('opacity-50');

                        if (data.path) {
                            if (uploadedTempPhoto) {
                                cleanupTempFile(uploadedTempPhoto.path);
                            }

                            uploadedTempPhoto = {
                                path: data.path,
                                temp_name: data.temp_name,
                                url: data.url
                            };

                            employeePathInput.value = data.path;
                            tempPhotoNameInput.value = data.temp_name;

                            imgPreview.src = data.url;
                            imgPreview.title = 'Photo uploaded. Form must be saved to keep.';

                        } else if (data.error) {
                            throw new Error(data.error);
                        }
                    })
                    .catch(err => {
                        console.error('Upload error:', err);
                        imgPreview.classList.remove('opacity-50');
                        imgPreview.src = "{{ asset('storage/default/employee.jpg') }}";
                        imgPreview.title = 'Upload failed. Click to try again.';
                        fileInput.value = '';
                        alert('Error uploading image: ' + err.message);
                    });
            }

            document.getElementById('employeeForm').addEventListener('submit', function() {
                isSubmitting = true;
            });

            // Handle page unload (user leaves without saving)
            window.addEventListener('beforeunload', function(e) {
                if (uploadedTempPhoto && !isSubmitting) {
                    cleanupTempFile(uploadedTempPhoto.path);
                }
            });

            // Handle form reset (clear button or browser reset)
            document.getElementById('employeeForm').addEventListener('reset', function() {
                if (uploadedTempPhoto) {
                    cleanupTempFile(uploadedTempPhoto.path);
                    uploadedTempPhoto = null;
                }
            });

            // Handle back button click
            document.getElementById('closeButton').addEventListener('click', function(e) {
                if (uploadedTempPhoto) {
                    e.preventDefault(); // Prevent immediate navigation

                    // Clean up temp file first, then navigate
                    cleanupTempFile(uploadedTempPhoto.path);
                    uploadedTempPhoto = null;

                    // Navigate after cleanup
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 100);
                }
            });

            // ========== EMPLOYEE ID MANAGEMENT ==========
            const employeeIdSection = document.getElementById('employeeIdSection');

            if (employeeIdSection) {
                const addIdBtn = document.getElementById('add-id-btn');
                const employeeIdForm = {
                    recordId: document.getElementById('employee_id_record_id'),
                    type: document.getElementById('employee_id_type'),
                    number: document.getElementById('employee_id_number'),
                    badge: document.getElementById('badge'),
                    issuedDate: document.getElementById('employee_id_issued_date'),
                    expiryDate: document.getElementById('employee_id_expiry_date')
                };

                // Add ID Button Click Handler
                addIdBtn.addEventListener('click', function() {
                    const employeeId = employeeIdForm.recordId.value;
                    const idType = employeeIdForm.type.value;
                    const idNumber = employeeIdForm.number.value.trim();
                    const badge = employeeIdForm.badge.value.trim();
                    const issuedDate = employeeIdForm.issuedDate.value;
                    const expiryDate = employeeIdForm.expiryDate.value;

                    // Validation
                    if (!idType) {
                        alert('Please select ID Type.');
                        employeeIdForm.type.focus();
                        return;
                    }

                    if (!idNumber) {
                        showToast('ID Number is required.', 'error');
                        employeeIdForm.number.classList.add('is-invalid');
                        employeeIdForm.number.focus();
                        return;
                    } else {
                        employeeIdForm.number.classList.remove('is-invalid');
                    }

                    const employeeIdData = {
                        id: employeeId || null,
                        id_type_id: idType,
                        id_number: idNumber,
                        badge: badge || null,
                        issue_date: issuedDate || null,
                        expiry_date: expiryDate || null,
                        _token: document.querySelector('meta[name="csrf-token"]').content
                    };

                    const url = employeeId ?
                        `/employee/{{ $employee->id ?? 0 }}/ids/${employeeId}` :
                        `/employee/{{ $employee->id ?? 0 }}/ids`;

                    const method = employeeId ? 'PUT' : 'POST';


                    // Make AJAX request
                    fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': employeeIdData._token
                            },
                            body: JSON.stringify(employeeIdData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Clear form
                                clearIdForm();

                                // Refresh the table
                                refreshEmployeeIds();

                                // Show success message
                                showToast(data.message || 'ID saved successfully!', 'success');
                            } else {
                                throw new Error(data.message || 'Failed to save ID');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast(error.message || 'An error occurred', 'error');
                        });
                });

                // Function to clear ID form
                function clearIdForm() {
                    employeeIdForm.recordId.value = '';
                    employeeIdForm.type.value = '';
                    employeeIdForm.number.value = '';
                    employeeIdForm.badge.value = '';
                    employeeIdForm.issuedDate.value = '';
                    employeeIdForm.expiryDate.value = '';
                    addIdBtn.textContent = 'Add ID';
                }

                // Function to refresh employee IDs table
                function refreshEmployeeIds() {
                    fetch(`/employee/{{ $employee->id ?? 0 }}/ids`)
                        .then(response => response.json())
                        .then(data => {
                            const tbody = document.getElementById('employeeIdsBody');
                            const noIdsRow = document.getElementById('no-ids-row');

                            if (data.length === 0) {
                                if (!noIdsRow) {
                                    tbody.innerHTML = `
                                        <tr id="no-ids-row">
                                            <td colspan="6" class="ft-empty">No IDs found.</td>
                                        </tr>
                                    `;
                                }
                                return;
                            }

                            // Remove no-ids row if exists
                            if (noIdsRow) {
                                noIdsRow.remove();
                            }

                            // Build table rows
                            let rowsHtml = '';
                            data.forEach(id => {
                                rowsHtml += `
                                    <tr id="row-${id.id}">
                                        <td>${id.id_type.name}</td>
                                        <td>${id.id_number}</td>
                                        <td>${id.badge || ''}</td>
                                        <td>${id.issue_date || ''}</td>
                                        <td>${id.expiry_date || ''}</td>
                                        <td>${actionButtons(`editEmployeeId(${id.id})`, `deleteEmployeeId(${id.id})`, 'Edit ID', 'Delete ID')}</td>
                                    </tr>
                                `;
                            });

                            tbody.innerHTML = rowsHtml;
                        })
                        .catch(error => {
                            console.error('Error refreshing IDs:', error);
                        });
                }
            }

            //LOAD EMPLOYEE WORK HISTORY
            const employeeId = {{ $employee->id ?? 'null' }};
            if (employeeId) {
                loadWorkHistory(employeeId);
            }

        });

        document.getElementById('idno').addEventListener('blur', function() {
            let idno = this.value.trim();
            let empId = document.getElementById('id')?.value || 0;
            const feedback = document.getElementById('id-feedback');
            const saveBtn = document.getElementById('saveBtn');

            if (idno === '') {
                this.classList.remove('is-invalid');
                feedback.classList.add('hidden');
                saveBtn.disabled = false;
                return;
            };

            fetch(`/employee/check-idno`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        idno: idno,
                        id: empId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const idnoInput = document.getElementById('idno');
                    if (data.exists) {
                        idnoInput.classList.add('is-invalid');
                        feedback.classList.remove('hidden');
                        saveBtn.disabled = true;
                    } else {
                        this.classList.remove('is-invalid');
                        feedback.classList.add('hidden');
                        saveBtn.disabled = false;
                    }
                })
                .catch(err => console.error('Error:', err));
        });


        // Function to edit employee ID
        function editEmployeeId(id) {
            // Fetch the ID details
            fetch(`/employee/{{ $employee->id ?? 0 }}/ids/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the form with the ID data
                        document.getElementById('employee_id_record_id').value = data.id.id;
                        document.getElementById('employee_id_type').value = data.id.id_type_id;
                        document.getElementById('employee_id_number').value = data.id.id_number;
                        document.getElementById('badge').value = data.id.badge || '';
                        document.getElementById('employee_id_issued_date').value = formatDateForInput(data.id
                            .issue_date) || '';
                        document.getElementById('employee_id_expiry_date').value = formatDateForInput(data.id
                            .expiry_date) || '';

                        // Change button to "Update ID"
                        document.getElementById('add-id-btn').textContent = 'Update ID';

                        // Scroll to the form
                        document.getElementById('employee_id_number').focus();
                    }
                })
                .catch(error => {
                    console.error('Error fetching ID:', error);
                    showToast('Failed to load ID details', 'error');
                });
        }

        // Function to delete employee ID
        function deleteEmployeeId(id) {
            if (!canDelete) {
                showToast('You do not have permission to delete this ID.', 'error');
                return;
            }

            const row = document.getElementById(`row-${id}`);
            if (!row) return;

            const idNumberCell = row.querySelector('td:nth-child(2)');
            const idNumber = idNumberCell ? idNumberCell.textContent.trim() : 'this';

            if (!confirm(`Are you sure you want to delete this ID (${idNumber})?`)) {
                return;
            }

            fetch(`/employee/{{ $employee->id ?? 0 }}/ids/${id}`, {
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

                        // Check if table is empty
                        const tbody = document.getElementById('employeeIdsBody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = `
                                <tr id="no-ids-row">
                                    <td colspan="6" class="ft-empty">No IDs found.</td>
                                </tr>
                            `;
                        }

                        showToast(data.message || 'ID deleted successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete ID');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'An error occurred', 'error');
                });
        }

        //EMPLOYEE HISTORY 
        function loadWorkHistory(employeeId) {

            fetch(`/employee/${employeeId}/history`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load work history');
                    }
                    return response.json();
                })
                .then(data => {

                    const tbody = document.getElementById('employeeWorkHistoryBody');
                    tbody.innerHTML = '';

                    data.forEach(history => {
                        appendRow(history);
                    });

                })
                .catch(error => {
                    console.error('Load Error:', error);
                });
        }


        // Save Work History (the button only exists when editing an existing employee)
        const addWorkHistoryBtn = document.getElementById('add-workhistory-btn');
        if (addWorkHistoryBtn) {
            addWorkHistoryBtn.addEventListener('click', function() {
                const addBtn = this;
                const recordId = addBtn.getAttribute('data-record-id');

                const companyName = document.getElementById('company_name').value.trim();
                const positionHeld = document.getElementById('position_held').value.trim();
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;


                if (!companyName || !positionHeld) {
                    alert('Company Name and Position Held are required.');
                    return;
                }

                if (recordId) {
                    updateWorkHistory(recordId);
                    return;
                }

                fetch('/employee-history/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            employee_id: {{ $employee->id ?? 'null' }},
                            company_name: companyName,
                            position_held: positionHeld,
                            start_date: startDate || null,
                            end_date: endDate || null
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {

                        appendRow(data);

                        document.getElementById('company_name').value = '';
                        document.getElementById('position_held').value = '';
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';

                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while saving work history. Please try again.');
                    });
            });
        }


        function appendRow(history) {
            const tableBody = document.getElementById('employeeWorkHistoryBody');
            const newRow = document.createElement('tr');
            const startDate = history.start_date ? new Date(history.start_date).toLocaleDateString() : '';
            const endDate = history.end_date ? new Date(history.end_date).toLocaleDateString() : '';
            newRow.setAttribute('id', 'row-' + history.id);
            newRow.innerHTML = `
                <td>${history.company}</td>
                <td>${history.position}</td>
                <td>${startDate || ''}</td>
                <td>${endDate || ''}</td>
                <td>${actionButtons(
                    `editWorkHistory(${history.id})`,
                    `deleteWorkHistory(${history.id})`,
                    `Edit work history: ${history.company} - ${history.position}`,
                    `Delete work history: ${history.company} - ${history.position}`
                )}</td>
            `;
            tableBody.appendChild(newRow);
        }

        function deleteWorkHistory(id) {
            if (!canDelete) {
                showToast('You do not have permission to delete this work history.', 'error');
                return;
            }

            const row = document.getElementById(`row-${id}`);
            if (!row) return;

            if (!confirm('Are you sure you want to delete this work history record?')) {
                return;
            }

            fetch(`/employee-history/${id}`, {
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

                        showToast(data.message || 'Work history record deleted successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete work history record');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'An error occurred', 'error');
                });
        }

        function editWorkHistory(id) {
            fetch(`/employee-history/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const history = data.history;
                        document.getElementById('company_name').value = history.company;
                        document.getElementById('position_held').value = history.position;
                        document.getElementById('start_date').value = formatDateForInput(history.start_date) || '';
                        document.getElementById('end_date').value = formatDateForInput(history.end_date) || '';

                        // Change button to "Update Work History"
                        const addBtn = document.getElementById('add-workhistory-btn');
                        addBtn.textContent = 'Update Work History';

                        // Store the record ID in a hidden field or data attribute
                        addBtn.setAttribute('data-record-id', history.id);

                        // Scroll to the form
                        document.getElementById('company_name').focus();
                    }
                })
                .catch(error => {
                    console.error('Error fetching work history:', error);
                    showToast('Failed to load work history details', 'error');
                });
        }

        function updateWorkHistory(id) {

            fetch(`/employee-history/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        company: document.getElementById('company_name').value,
                        position: document.getElementById('position_held').value,
                        start_date: document.getElementById('start_date').value,
                        end_date: document.getElementById('end_date').value,
                    })
                })
                .then(res => res.json())
                .then(data => {

                    document.getElementById('row-' + id).remove();
                    appendRow(data);

                    resetForm();

                });
        }

        function resetForm() {

            document.getElementById('company_name').value = '';
            document.getElementById('position_held').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';

            const addBtn = document.getElementById('add-workhistory-btn');

            addBtn.textContent = 'Add Work History';
            addBtn.removeAttribute('data-record-id');
        }



        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = 'ft-alert fixed top-4 right-4 z-50 max-w-sm shadow-lg ' +
                (type === 'success' ? 'ft-alert-success' : 'ft-alert-error');
            toast.textContent = message;
            toast.style.animation = 'slideInRight 0.3s ease-out';

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }


        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection
