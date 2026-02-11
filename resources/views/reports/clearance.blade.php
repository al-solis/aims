<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Clearance Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
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
        <div class="sub-title">EMPLOYEE CLEARANCE REPORT</div>
        <div class="sub-title">Request No: {{ $clearance->request_number }}@if ($clearance->status == '4')
                <span style="color: red; font-weight: bold;">(Cancelled)</span>
            @endif
        </div>
        <div class="sub-title">Date: {{ $clearance->created_at->format('F d, Y') }}</div>
    </div>

    {{-- EMPLOYEE INFO --}}
    <div class="section">
        <div class="section-title">Employee Information</div>
        <table>
            <tr>
                <td width="25%"><strong>Name:</strong></td>
                <td width="75%">
                    {{ $clearance->employee->last_name }},
                    {{ $clearance->employee->first_name }}
                    {{ $clearance->employee->middle_name }}
                </td>
            </tr>
            <tr>
                <td><strong>Department:</strong></td>
                <td>{{ $clearance->employee->location->description ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- CLEARANCE DETAILS --}}
    <div class="section">
        <div class="section-title">Accountability Details</div>

        <table>
            <thead>
                <tr>
                    <th>Asset</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Purchase Cost</th>
                    <th class="text-right">Actual Cost</th>
                    <th class="text-right">Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($clearance->clearance_details as $detail)
                    @php
                        $grandTotal += $detail->total;

                        $statusText = match ($detail->status) {
                            '1' => 'Returned',
                            '2' => 'Damaged',
                            '3' => 'Lost',
                            default => 'Pending',
                        };

                        $statusClass = match ($detail->status) {
                            '1' => 'status-returned',
                            '2' => 'status-damaged',
                            '3' => 'status-lost',
                            default => 'status-pending',
                        };
                    @endphp

                    <tr>
                        <td>{{ $detail->asset->name ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($detail->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->purchase_cost, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->actual_cost, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->total, 2) }}</td>
                        <td class="{{ $statusClass }}">{{ $statusText }}</td>
                    </tr>
                @endforeach

                @if ($clearance->clearance_details->isEmpty())
                    <tr>
                        <td colspan="6" style="text-align: center; font-size: 11px; color: #555">No asset
                            details found.</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>


    </div>
    <p style="text-align: center; font-size: 11px; color:#555">This clearance certificate is valid only when properly
        signed
        and dated.</p>

    {{-- SIGNATURES --}}
    <div class="footer">
        <div class="signature">
            ___________________________<br>
            Employee Signature
        </div>
        <div class="signature" style="float:right;">
            ___________________________<br>
            Authorized Officer
        </div>
    </div>

</body>

</html>
