<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Odometer Report</title>

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
        <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
        <br>
        <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">ODOMETER REPORT</div>
        <div class="sub-title">Asset: {{ $asset->asset_code }} : {{ $asset->name }}</div>
        <div class="sub-title">Date Range: {{ Carbon::parse($fromDate)->format('F d, Y') }} -
            {{ Carbon::parse($toDate)->format('F d, Y') }}</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
    </div>

    {{-- DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}

        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reading</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($odometerReadings as $reading)
                    @php
                        $grandTotal += $reading->to_reading - $reading->from_reading;
                    @endphp

                    <tr>
                        <td>{{ $reading->employee->last_name ?? 'N/A' }}, {{ $reading->employee->first_name ?? 'N/A' }}
                            {{ $reading->employee->middle_name ?? 'N/A' }}</td>
                        <td>{{ Carbon::parse($reading->date)->format('m/d/Y') }}</td>
                        <td class="text-right">{{ number_format($reading->from_reading, 0) }}</td>
                        <td class="text-right">{{ number_format($reading->to_reading, 0) }}</td>
                        <td class="text-right">{{ number_format($reading->to_reading - $reading->from_reading, 0) }}
                        </td>
                        <td>{{ $reading->remarks ?? '' }}</td>
                    </tr>
                @endforeach

                @if ($odometerReadings->isEmpty())
                    <tr>
                        <td colspan="6" style="text-align: center; font-size: 11px; color: #555">No odometer readings
                            found.</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 0) }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p>

        {{-- SIGNATURES --}}
        {{-- <div class="footer">
            <div class="signature">
                ___________________________<br>
                Employee Signature
            </div>
            <div class="signature" style="float:right;">
                ___________________________<br>
                Authorized Officer
            </div>
        </div> --}}

</body>

</html>
