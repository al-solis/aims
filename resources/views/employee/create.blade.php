@extends('dashboard')
@section('content')
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
                    Employee Information
                </h2>
                <p class="text-sm text-gray-600">
                    Display employee information.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">

            <form method="POST" action="{{ $employee ? route('employee.update', $employee->id) : route('employee.store') }}"
                enctype="multipart/form-data" id="employeeForm">
                @csrf
                @if ($employee)
                    @method('PUT')
                @endif

                <div class="">
                    <div class="mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="employee-tab"
                            data-tabs-toggle="#employee-tab-content"
                            data-tabs-active-classes="text-primary-600 hover:text-primary-600 dark:text-primary-500 dark:hover:text-primary-500 border-primary-600 dark:border-primary-500"
                            data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300 role="tablist">
                            <li class="me-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="basic-tab"
                                    data-tabs-target="#basic" type="button" role="tab" aria-controls="basic"
                                    aria-selected="false">Basic Information</button>
                            </li>
                            @if ($employee)
                                <li class="me-2" role="presentation">
                                    <button
                                        class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                        id="idinfo-tab" data-tabs-target="#idinfo" type="button" role="tab"
                                        aria-controls="idinfo" aria-selected="false">ID Information</button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div id="employee-tab-content">
                        <div class="hidden p-1 rounded-lg bg-gray-50 dark:bg-gray-800" id="basic" role="tabpanel"
                            aria-labelledby="basic-tab">
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Please fill the following information
                                <strong class="font-medium text-gray-800 dark:text-white">Basic Information tab's associated
                                    content</strong>.
                            </p>
                            <div class="grid gap-2 sm:grid-cols-2 sm:gap-2 mb-2">
                                <input type="hidden" id="id" name="id"
                                    value="{{ old('id', $employee->id ?? '') }}">

                                <input type="hidden" name="employee_path" id="employee_path"
                                    value="{{ old('employee_path', $employee->photo_path ?? '') }}">
                                <input type="hidden" name="temp_photo_name" id="temp_photo_name" value="">

                                <div class="sm:col-span-1">
                                    <label for="imgPreview"
                                        class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                        Employee Photo (Click to upload)
                                    </label>
                                    <div class="flex items-center gap-3 mb-2">
                                        <img id="empPreview"
                                            class="inline-block size-32 rounded-lg ring-white dark:ring-neutral-900 cursor-pointer hover:opacity-80 transition-opacity duration-200 border border-gray-300"
                                            src="{{ $employee && $employee->photo_path ? asset('storage/' . $employee->photo_path) : asset('storage/default/employee.jpg') }}"
                                            alt="Employee photo" title="Click to upload photo">
                                        <input type="file" id="photoInput" name="photo" accept="image/*"
                                            class="hidden">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Click on the image to upload a photo (Max: 2MB)
                                    </p>
                                </div>

                                <div class="w-full">
                                    <label for="idno" class="block text-xs font-medium text-gray-900 dark:text-white">ID
                                        No*</label>
                                    <input type="text" name="idno" id="idno"
                                        value="{{ old('idno', $employee->employee_code ?? '') }}"
                                        class="mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. 001-26, 2026-00001" required>
                                    <small id="id-feedback" class="text-red-500 text-xs mb-1 hidden">
                                        ID number already exists in another record.
                                    </small>

                                    <label for="date"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Date
                                        Hired*</label>
                                    <input type="date" name="date" id="date"
                                        value="{{ old('date', $employee->hire_date ?? '') }}"
                                        class="mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. 04/08/1984" required>

                                    <label for="status"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Status*</label>
                                    <select id="status" name="status"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        required>
                                        <option value="1" @selected(old('status', $employee->status ?? 1) == 1)>Active</option>
                                        <option value="0" @selected(old('status', $employee->status ?? 1) == 0)>Inactive</option>
                                        <option value="2" @selected(old('status', $employee->status ?? 1) == 2)>On leave</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-2 mb-2 sm:grid-cols-1 md:grid-cols-3">
                                <div class="w-full">
                                    <label for="lname"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Last
                                        Name*</label>
                                    <input type="text" name="lname" id="lname"
                                        value ="{{ old('lname', $employee->last_name ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. Dela Cruz" required="">
                                </div>
                                <div class="w-full">
                                    <label for="fname"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">First
                                        Name*</label>
                                    <input type="text" name="fname" id="fname"
                                        value ="{{ old('fname', $employee->first_name ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. Juan" required="">
                                </div>
                                <div class="w-full">
                                    <label for="mname"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Middle
                                        Name</label>
                                    <input type="text" name="mname" id="mname"
                                        value ="{{ old('mname', $employee->middle_name ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. Santos">
                                </div>
                            </div>

                            <div class="grid gap-2 mb-2 sm:grid-cols-1 md:grid-cols-3">
                                <div class="w-full">
                                    <label for="bday"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Birthday</label>
                                    <input type="date" name="bday" id="bday"
                                        value="{{ old('bday', $employee->date_of_birth ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. mm/dd/yyyy">
                                </div>
                                <div class="w-full">
                                    <label for="mobile"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Mobile
                                        No</label>
                                    <input type="tel" name="mobile" id="mobile"
                                        value="{{ old('mobile', $employee->mobile ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. 09xxxxxxxxx">
                                </div>
                                <div class="w-full">
                                    <label for="email"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Email</label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $employee->email ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. juandelacruz@yahoo.com">
                                </div>
                            </div>

                            <div class="grid gap-2 mb-2 sm:grid-cols-1 md:grid-cols-2">
                                <div class="w-full">
                                    <label for="position"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Position</label>
                                    <input type="text" name="position" id="position"
                                        value="{{ old('position', $employee->position ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. Manager">
                                </div>
                                <div class="w-full">
                                    <label for="department"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Department</label>
                                    <select name="department" id="department"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                        <option selected="" value="">Select Department/ Location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ old('department', $employee->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <label for="address"
                                class="block text-xs font-medium text-gray-900 dark:text-white">Address*</label>
                            <textarea name="address" id="address" rows="3" text="{{ old('address', $employee->address ?? '') }}"
                                class="mb-2 px-3 py-2 w-full bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                placeholder="Address..." required>{{ old('address', $employee->address ?? '') }}
                </textarea>

                            <div class="grid gap-2 mb-2 sm:grid-cols-1 md:grid-cols-4">
                                <div class="w-full">
                                    <label for="city"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">City</label>
                                    <input type="text" name="city" id="city"
                                        value="{{ old('city', $employee->city ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. City">
                                </div>
                                <div class="w-full">
                                    <label for="state"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">State/
                                        Province</label>
                                    <input type="text" name="state" id="state"
                                        value="{{ old('state', $employee->state ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. State/ Province">
                                </div>
                                <div class="w-full">
                                    <label for="country"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Country</label>
                                    <input type="text" name="country" id="country"
                                        value="{{ old('country', $employee->country ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. Philippines">
                                </div>
                                <div class="w-full">
                                    <label for="zip"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Zip
                                        Code</label>
                                    <input type="text" name="zip" id="zip"
                                        value="{{ old('zip', $employee->postal_code ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. 4103">
                                </div>
                            </div>

                            <div class="grid gap-2 mb-2 sm:grid-cols-1 md:grid-cols-2">
                                <div class="w-full">
                                    <label for="emergency"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Emergency
                                        Contact</label>
                                    <input type="text" name="emergency" id="emergency"
                                        value="{{ old('emergency', $employee->emergency_contact ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. Emergency Contact">
                                </div>
                                <div class="w-full">
                                    <label for="e_no"
                                        class="block text-xs font-medium text-gray-900 dark:text-white">Emergency
                                        No.</label>
                                    <input type="tel" name="e_no" id="e_no"
                                        value="{{ old('e_no', $employee->emergency_phone ?? '') }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="e.g. 09xxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        @if ($employee)
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <div id="employeeIdSection" data-employee-id="{{ $employee->id }}">
                                <div class="hidden p-1 rounded-lg bg-gray-50 dark:bg-gray-800" id="idinfo"
                                    role="tabpanel" aria-labelledby="idinfo-tab">
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Maintenance of
                                        <strong class="font-medium text-gray-800 dark:text-white">Employee ID</strong>
                                        records.
                                    </p>
                                    <input type="hidden" id="employee_id_record_id" name="employee_id_record_id"
                                        value="">
                                    <div class="grid gap-2 mb-2 sm:grid-cols-1 md:grid-cols-2">
                                        <div class="w-full">
                                            <label for="employee_id_type"
                                                class="block text-xs font-medium text-gray-900 dark:text-white">ID
                                                Type*</label>
                                            <select name="employee_id_type" id="employee_id_type"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                                <option selected="" value="">Select ID Type</option>
                                                @foreach ($idTypes as $idType)
                                                    <option value="{{ $idType->id }}">{{ $idType->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-full">
                                            <label for="employee_id_number"
                                                class="block text-xs font-medium text-gray-900 dark:text-white">ID
                                                Number*</label>
                                            <input type="text" name="employee_id_number" id="employee_id_number"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                                placeholder="e.g. 242-327-268">
                                        </div>
                                    </div>

                                    <div class="grid gap-2 mb-2 sm:grid-cols-1 md:grid-cols-3">
                                        <div class="w-full">
                                            <label for="employee_id_issued_date"
                                                class="block text-xs font-medium text-gray-900 dark:text-white">Issued
                                                Date</label>
                                            <input type="date" name="employee_id_issued_date"
                                                id="employee_id_issued_date"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                                placeholder="e.g. mm/dd/yyyy">
                                        </div>
                                        <div class="w-full">
                                            <label for="employee_id_expiry_date"
                                                class="block text-xs font-medium text-gray-900 dark:text-white">Expiry
                                                Date</label>
                                            <input type="date" name="employee_id_expiry_date"
                                                id="employee_id_expiry_date"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                                placeholder="e.g. mm/dd/yyyy">
                                        </div>

                                        <button id="add-id-btn" type="button"
                                            class="mt-4 h-fit text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-sm px-4 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                            Add ID
                                        </button>
                                    </div>

                                    {{-- Table --}}
                                    <div class="bg-white border rounded-xl overflow-hidden">
                                        <table class="min-w-full text-xs" id="employeeIdsTable">
                                            <thead class="bg-gray-200 text-gray-600">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3 text-left w-[100px]">ID Type</th>
                                                    <th scope="col" class="px-4 py-3 text-left w-[80px]">ID Number</th>
                                                    <th scope="col" class="px-4 py-3 text-left w-[60px]">Issued</th>
                                                    <th scope="col" class="px-4 py-3 text-left w-[60px]">Expiry</th>
                                                    <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody id="employeeIdsBody" class="divide-y">
                                                @forelse($employeeIds as $employeeId)
                                                    <tr id="row-{{ $employeeId->id }}" class="hover:bg-gray-50">
                                                        <td class="px-2 py-2 font-medium w-[100px]">
                                                            {{ $employeeId->idType->name }}</td>
                                                        <td class="px-2 py-2 w-[80px]">{{ $employeeId->id_number }}</td>
                                                        <td class="px-2 py-2 w-[60px]">{{ $employeeId->issue_date }}</td>
                                                        <td class="px-2 py-2 w-[60px]">{{ $employeeId->expiry_date }}</td>
                                                        <td class="px-2 py-2 w-[50px]">
                                                            <div class="flex items-center justify-center space-x-2">
                                                                <button type="button"
                                                                    onclick="editEmployeeId({{ $employeeId->id }})"
                                                                    title="Edit ID"
                                                                    class="text-blue-600 hover:text-blue-800 transition-colors">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                                        <path fill-rule="evenodd"
                                                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                                    </svg>
                                                                </button>
                                                                <button type="button"
                                                                    onclick="deleteEmployeeId({{ $employeeId->id }})"
                                                                    title="Delete ID"
                                                                    class="text-red-600 hover:text-red-800 transition-colors">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        class="bi bi-trash" viewBox="0 0 16 16">
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
                                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                                            No IDs found.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('employee.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>
                    <button type="submit" id="saveBtn"
                        class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
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

    <script>
        let uploadedTempPhoto = null;
        let isSubmitting = false;

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
                    issuedDate: document.getElementById('employee_id_issued_date'),
                    expiryDate: document.getElementById('employee_id_expiry_date')
                };

                // Add ID Button Click Handler
                addIdBtn.addEventListener('click', function() {
                    const employeeId = employeeIdForm.recordId.value;
                    const idType = employeeIdForm.type.value;
                    const idNumber = employeeIdForm.number.value.trim();
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
                        employeeIdForm.number.classList.add('border-red-500');
                        employeeIdForm.number.focus();
                        return;
                    } else {
                        employeeIdForm.number.classList.remove('border-red-500');
                    }

                    const employeeIdData = {
                        id: employeeId || null,
                        id_type_id: idType,
                        id_number: idNumber,
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
                    employeeIdForm.issuedDate.value = '';
                    employeeIdForm.expiryDate.value = '';
                    addIdBtn.textContent = 'Add ID';
                    addIdBtn.classList.remove('bg-blue-700', 'hover:bg-blue-800');
                    addIdBtn.classList.add('bg-green-700', 'hover:bg-green-800');
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
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    No IDs found.
                                </td>
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
                        <tr id="row-${id.id}" class="hover:bg-gray-50">
                            <td class="px-2 py-2 font-medium w-[100px]">${id.id_type.name}</td>
                            <td class="px-2 py-2 w-[80px]">${id.id_number}</td>
                            <td class="px-2 py-2 w-[60px]">${id.issue_date || ''}</td>
                            <td class="px-2 py-2 w-[60px]">${id.expiry_date || ''}</td>
                            <td class="px-2 py-2 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
                                    <button type="button" onclick="editEmployeeId(${id.id})" 
                                        title="Edit ID" 
                                        class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </button>
                                    <button type="button" onclick="deleteEmployeeId(${id.id})" 
                                        title="Delete ID" 
                                        class="text-red-600 hover:text-red-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                            fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
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

        });

        document.getElementById('idno').addEventListener('blur', function() {
            let idno = this.value.trim();
            let empId = document.getElementById('id')?.value || 0;
            const feedback = document.getElementById('id-feedback');
            const saveBtn = document.getElementById('saveBtn');

            if (idno === '') {
                this.classList.remove('border-red-500');
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
                        idnoInput.classList.add('border-red-500');
                        feedback.classList.remove('hidden');
                        saveBtn.disabled = true;
                    } else {
                        this.classList.remove('border-red-500');
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
                        document.getElementById('employee_id_issued_date').value = data.id.issue_date || '';
                        document.getElementById('employee_id_expiry_date').value = data.id.expiry_date ||
                            '';

                        // Change button to "Update ID"
                        const addBtn = document.getElementById('add-id-btn');
                        addBtn.textContent = 'Update ID';
                        addBtn.classList.remove('bg-green-700', 'hover:bg-green-800');
                        addBtn.classList.add('bg-blue-700', 'hover:bg-blue-800');

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
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                        No IDs found.
                                    </td>
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
