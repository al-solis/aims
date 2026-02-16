<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Employee Listing Report</title>

    <style>
        body {
            /* font-family: DejaVu Sans, sans-serif;  */
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
        }

        .sub-title {
            font-size: 12px;
            color: #555;
        }

        .section {
            margin-top: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }

        table th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
        }

        .page-number {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            padding: 5px 0;
            border-top: 1px solid #ddd;
        }

        .signature {
            margin-top: 50px;
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .status-returned {
            color: green;
            font-weight: bold;
        }

        .status-damaged {
            color: orange;
            font-weight: bold;
        }

        .status-lost {
            color: red;
            font-weight: bold;
        }

        .status-pending {
            color: gray;
            font-weight: bold;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        @php
            $dateRangeLabels = [
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'this_year' => 'This Year',
                'last_year' => 'Last Year',
                'custom' => 'Custom Range',
            ];

            $sortLabels = [
                'last_name' => 'Last Name',
                'hire_date' => 'Date Hired',
            ];
        @endphp
        <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
        <br>
        <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">EMPLOYEE LISTING REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Location: {{ $pLocationName }}</div>
        <div class="sub-title">Date Range:
            {{ $pDateRange == 'custom' ? $pFromDate . ' to ' . $pToDate : $dateRangeLabels[$pDateRange] }}</div>
        <div class="sub-title">Status: {{ $statusLabel }}</div>
        <div class="sub-title">Sort By: {{ $sortLabels[$sortField] ?? $sortField }} ({{ $sortDirection }})</div>
    </div>

    {{-- DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Code</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Date Hired</th>
                    <th>Birthday</th>
                    <th>Designation</th>
                    <th>Location/ Department</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($employees as $employee)
                    @php
                        $statuses = [
                            1 => 'Inactive',
                            2 => 'Active',
                            3 => 'On Leave',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $employee->employee_code }}</td>
                        <td>{{ $employee->first_name }}</td>
                        <td>{{ $employee->middle_name }}</td>
                        <td>{{ $employee->last_name }}</td>
                        <td>{{ $employee->hire_date ? Carbon::parse($employee->hire_date)->format('m/d/Y') : '' }}</td>
                        <td>{{ $employee->date_of_birth ? Carbon::parse($employee->date_of_birth)->format('m/d/Y') : '' }}
                        </td>
                        <td>{{ $employee->position ?? '' }}</td>
                        <td>{{ $employee->location_id ? $employee->location->name : '' }}</td>
                    </tr>
                @endforeach

                @if ($employees->isEmpty())
                    <tr>
                        <td colspan="9" style="text-align: center; font-size: 11px; color: #555">No employees found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p>

        {{-- <div class="page-number">
            Page {PAGE_NUM} of {PAGE_COUNT} | Generated on {{ now()->format('F d, Y') }}
        </div> --}}
</body>

</html>
