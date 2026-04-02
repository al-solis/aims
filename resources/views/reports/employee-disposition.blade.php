<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Monthly Disposition Report</title>

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
    @php
        $sortLabels = [
            'last_name' => 'Last Name',
            'hire_date' => 'Date Hired',
        ];
    @endphp

    <div class="header">
        <table width="100%" style="border:0;">
            <tr>
                <td width="7%" style="border:0;">
                    <img src="{{ public_path('images/logo.PNG') }}" style="width:80px;">
                </td>
                <td width="93%" style="border:0; text-align:center;">
                    <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
                </td>
            </tr>
        </table>
        <br>
        {{-- <div class="sub-title" style="font-weight: bolder; font-size: 15px">EMPLOYEE LISTING REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Location: {{ $pLocationName }}</div>
        <div class="sub-title">Date Range:
            {{ $pDateRange == 'custom' ? $pFromDate . ' to ' . $pToDate : $pDateRange }}</div>
        <div class="sub-title">Status: {{ $statusLabel }}</div>
        <div class="sub-title">Sort By: {{ $sortLabels[$sortField] ?? $sortField }} ({{ $sortDirection }})</div> --}}
    </div>

    {{-- DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}
        <table>
            <thead>
                <!-- TOP HEADER -->
                <tr>
                    <th rowspan="2">CLIENTS ADDRESSES AND CONTACT NOS.</th>
                    <th rowspan="2">SEQ</th>
                    <th rowspan="2">NAME OF GUARDS <br>FAMILY NAME, <br> FIRST NAME, <br>MIDDLE NAME</th>
                    <th rowspan="2">GENDER</th>
                    <th rowspan="2">EDUCATIONAL ATTAINMENT</th>

                    <th colspan="2" style="text-align: center">LICENSE</th>

                    <th rowspan="2">SSS NO</th>

                    <th colspan="3" style="text-align: center">FIREARMS ISSUED <br>(BASED ON DDO)</th>

                    <th colspan="2" style="text-align: center">INSURANCE POLICY</th>

                    <th rowspan="2" style="text-align: center">SSS MONTHLY CONTRIBUTION</th>
                </tr>

                <!-- SUB HEADER -->
                <tr>
                    <!-- LICENSE -->
                    <th>LICENSE NO.</th>
                    <th>EXPIRY DATE</th>

                    <!-- FIREARMS -->
                    <th>KIND</th>
                    <th>MAKE</th>
                    <th>SN</th>

                    <!-- INSURANCE -->
                    <th>POLICY NO.</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $prevloc = null;
                @endphp
                @foreach ($employees as $employee)
                    @php
                        $statuses = [
                            1 => 'Inactive',
                            2 => 'Active',
                            3 => 'On Leave',
                        ];
                    @endphp
                    <tr>
                        @if ($prevloc != $employee->location_id)
                            <td>{{ $employee->location->name ?? 'N/A' }}<br>{{ $employee->location->address ?? '' }}
                                <br>
                                {{ $employee->location->contact_number ?? '' }}
                            </td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}</td>
                        <td>{{ $employee->gender == 1 ? 'M' : 'F' }}</td>
                        <td>{{ $employee->highest_education }}</td>
                        <td>{{ $employee->license_no }}</td>
                        <td>{{ $employee->license_expiry_date ? Carbon::parse($employee->license_expiry_date)->format('m/d/Y') : '' }}
                        </td>
                        <td>{{ $employee->sss_no }}</td>
                        <td>{{ $employee->position ?? '' }}</td>
                        <td>{{ $employee->location_id ? $employee->location->name : '' }}</td>
                        <td></td>
                        <td>2017-11-36</td>
                        <td style="text-align: right">509.40</td>
                        <td style="text-align: right">{{ number_format($employee->sss_rate, 2) }}</td>
                    </tr>

                    @php
                        $prevLoc = $employee->location_id;
                    @endphp
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
