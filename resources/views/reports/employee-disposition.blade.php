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
            text-align: left;
            font-size: 8px;
            color: #666;
            padding: 5px 0;
            /* border-top: 1px solid #ddd; */
        }

        .signature {
            margin-top: 50px;
            width: 45%;
            display: inline-block;
            text-align: center;
            font-size: 10px;
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
                    <th style="text-align: center">KIND</th>
                    <th style="text-align: center">MAKE</th>
                    <th style="text-align: center">SN</th>

                    <!-- INSURANCE -->
                    <th>POLICY NO.</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php $seq = 1; @endphp
                @forelse ($employees as $locId => $empGroup)

                    @php
                        // total rows for this location
                        $locationRowspan = $empGroup->flatten()->count();
                        $locationFirst = true;
                    @endphp

                    @foreach ($empGroup as $empId => $empRows)
                        @php
                            $empRowspan = $empRows->count();
                        @endphp

                        @foreach ($empRows as $index => $employee)
                            <tr>

                                {{-- LOCATION (only once) --}}
                                @if ($locationFirst)
                                    <td rowspan="{{ $locationRowspan }}">
                                        {{ $employee->location_name ?? 'N/A' }}<br>
                                        {{ $employee->location_address ?? '' }}
                                    </td>
                                    @php $locationFirst = false; @endphp
                                @endif

                                {{-- EMPLOYEE DETAILS (only once per employee) --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}">{{ $seq++ }}</td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->last_name }},
                                        {{ $employee->first_name }}
                                        {{ $employee->middle_name }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->gender == 1 ? 'M' : 'F' }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->highest_education }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->license_no ?? '' }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->license_expiry ? Carbon::parse($employee->license_expiry)->format('m/d/Y') : '' }}
                                    </td>

                                    <td rowspan="{{ $empRowspan }}">
                                        {{ $employee->sss_no ?? '' }}
                                    </td>
                                @endif

                                {{-- ASSET / FIREARM (ALWAYS REPEATED) --}}
                                <td>{{ $employee->asset_kind ?? '' }}</td>
                                <td>{{ $employee->asset_make ?? '' }}</td>
                                <td>{{ $employee->asset_serial ?? '' }}</td>

                                {{-- INSURANCE --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}">{{ $employee->policy_no ?? '' }}</td>
                                    <td rowspan="{{ $empRowspan }}" style="text-align:right">
                                        {{ $employee->policy_amount ?? '' }}</td>
                                @endif

                                {{-- SSS --}}
                                @if ($index == 0)
                                    <td rowspan="{{ $empRowspan }}" style="text-align:right">
                                        {{ number_format($employee->sss_rate, 2) }}
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    @endforeach

                @empty
                    <tr>
                        <td colspan="14" style="text-align:center;">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p> --}}

        {{-- <div class="page-number">
            Page {PAGE_NUM} of {PAGE_COUNT} | Generated on {{ now()->format('F d, Y') }}
        </div> --}}
    </div>

    <div class="section">
        <p style="font-size: 10px; font-weight: bold;">5. GAINS AND LOSSES:</p>
        <p style="font-size: 10px; font-weight: bold;">A. GAINS</p>
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAME OF GUARDS (FAMILY NAME, FIRST NAME) <br> MIDDLE NAME & QUALIFIER (if any)</th>
                    <th>NEWLY ASSIGNED</th>
                    <th>DATE <br> POSTED</th>
                    <th>PREVIOUS EMPLOYER/ AGENCY</th>
                    <th>ADDRESS & CONTACT NO. OF <br> PREVIOUS EMPLOYER/ AGENCY</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gains as $index => $gain)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $gain->last_name }}, {{ $gain->first_name }} {{ $gain->middle_name }}</td>
                        <td>{{ $gain->location_name ?? '' }}</td>
                        <td>{{ Carbon::parse($gain->hire_date)->format('m/d/Y') }}</td>
                        <td>{{ $gain->previous_employer ?? 'N/A' }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p style="font-size: 10px; font-weight: bold;">B. LOSSES</p>
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAME OF GUARDS (FAMILY NAME, FIRST NAME) <br> MIDDLE NAME & QUALIFIER (if any)</th>
                    <th>LAST POSTING <br> PLACE</th>
                    <th>DATE <br> TERMINATED</th>
                    <th>CAUSE(S) OF TERMINATION</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($losses as $index => $loss)
                    @php
                        $statuses = [
                            0 => 'Inactive',
                            1 => 'Active',
                            2 => 'On Leave',
                            3 => 'Resigned',
                            4 => 'Retired',
                            5 => 'Terminated',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $loss->last_name }}, {{ $loss->first_name }} {{ $loss->middle_name }}</td>
                        <td>{{ $loss->location_name ?? '' }}</td>
                        <td>{{ Carbon::parse($loss->termination_date)->format('m/d/Y') }}</td>
                        <td>{{ $statuses[$loss->status] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <br>
    <br>
    <p style ="font-size: 10px;">
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; I HEREBY CERTIFY the correctness of
        the disposition report for the month of
        {{ $dateRangeLabels == 'custom' ? Carbon::parse($pFromDate)->format('F Y') : $dateRangeLabels }}.
    </p>

    <div class="footer">
        <div class="signature">
            PREPARED BY: <br><br>

            _________________________________________<br>
            <strong>ARTHUR G. AQUINO, LLB, CSP, CMPP</strong> <br>
            OPERATIONS MANAGER
        </div>
        <div class="signature" style="float:right;">
            APPROVED BY: <br><br>

            _________________________________________<br>
            <strong>SHIELLA MARIE P. LIPANA - BALTAZAR</strong><br>
            VICE PRESIDENT FOR ADMIN AND FINANCE
        </div>
    </div>

    <br>
    <br>
    <p style ="font-size: 10px;">
        SUBSCRIBED AND SWORN to before me this ____ day of ____________, 20___, affiant exhibiting to me his/her
        ___________ with No. __________________ issued on ______________ at __________________.
    </p>
    <br>
    <p style ="font-size: 10px;">
        (NOTARY PUBLIC) <br><br>
        Doc. No. ______; <br>
        Page No. ______; <br>
        Book No. ______; <br>
        Series of 20___.
    </p>

    <script type="text/php">
        if (isset($pdf)) {
            // Use the page_script method to run this on every page
            $pdf->page_script(function ($pageNumber, $pageCount, $pdf, $fontMetrics) {
                $font = $fontMetrics->get_font('Arial, Helvetica, sans-serif', 'normal');
                $size = 8;                
                
                $pageText = "Page " . $pageNumber . " of " . $pageCount;
                
                // Position for the page number (bottom left)
                $x = 15;
                $y = $pdf->get_height() - 20;
                
                // Add the page number text to the current page
                $pdf->text($x, $y, $pageText, $font, $size);
            });
        }
    </script>
</body>

</html>
