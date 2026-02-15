<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Asset Transfer Report</title>

    <style>
        body {
            /* font-family: DejaVu Sans, sans-serif; */
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
            width: 30%;
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
        <br\>
            <div class="sub-title" style="font-weight: bolder; font-size: 15px">ASSET TRANSFER FORM</div>
            <div class="sub-title">Request No: {{ $transfer->code }} @if ($transfer->cancelled)
                    <span style="color: red; font-weight: bold;">(Cancelled)</span>
                @endif
            </div>
            <div class="sub-title">Date: {{ $transfer->date }}</div>
    </div>

    {{-- TRANSFER INFO --}}
    <div class="section">
        <div class="section-title">Transfer Information</div>
        <table>
            <tr>
                <td width="25%"><strong>From:</strong></td>
                <td width="75%">
                    {{ $transfer->transferDetails->first()->fromEmployee->last_name ?? 'N/A' }},
                    {{ $transfer->transferDetails->first()->fromEmployee->first_name ?? 'N/A' }}
                    {{ $transfer->transferDetails->first()->fromEmployee->middle_name ?? '' }}
                </td>

                <td width="25%"><strong>To:</strong></td>
                <td width="75%">
                    {{ $transfer->transferDetails->first()->toEmployee->last_name ?? 'N/A' }},
                    {{ $transfer->transferDetails->first()->toEmployee->first_name ?? 'N/A' }}
                    {{ $transfer->transferDetails->first()->toEmployee->middle_name ?? '' }}
                </td>
            </tr>
            <tr>
                <td><strong>Department:</strong></td>
                <td>{{ $transfer->transferDetails->first()->fromLocation->description ?? 'N/A' }}</td>

                <td><strong>Department:</strong></td>
                <td>{{ $transfer->transferDetails->first()->toLocation->description ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- TRANSFER DETAILS --}}
    <div class="section">
        <div class="section-title">Transfer Details</div>

        <table>
            <thead>
                <tr>
                    <th>Qty</th>
                    <th>Aquired</th>
                    <th>Property No.</th>
                    <th>Description</th>
                    <th class="text-right">Purchase Cost</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($transfer->transferDetails as $detail)
                    @php
                        $grandTotal += $detail->asset->cost * 1;
                    @endphp

                    <tr>
                        <td class="text-right">{{ number_format(1, 2) }}</td>
                        <td>{{ Carbon::parse($detail->asset->purchase_date)->format('Y-m-d') ?? 'N/A' }}</td>
                        <td>{{ $detail->asset->asset_code ?? 'N/A' }}</td>
                        <td>{{ $detail->asset->name ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($detail->asset->cost, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->asset->cost, 2) }}</td>
                    </tr>
                @endforeach

                @if ($transfer->transferDetails->isEmpty())
                    <tr>
                        <td colspan="5" style="text-align: center; font-size: 11px; color: #555">No asset
                            details found.</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                    <td></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- REASON --}}
    <br>
    <div class="section-title">Reason</div>
    <div>{{ $transfer->description ?? 'N/A' }}</div>

    {{-- <p style="text-align: center; font-size: 11px; color:#555">This clearance certificate is valid only when properly
        signed
        and dated.</p> --}}

    {{-- SIGNATURES --}}
    <div class="footer">
        <div class="signature">
            Approved By:<br><br>

            ___________________________<br>
            <br>
            <br>
            Authorized Officer

        </div>
        <div class="signature">
            Released/ Issued By:
            <br><br>
            ___________________________<br>
            <span style="color:#555;">{{ $transfer->transferDetails->first()->fromEmployee->last_name ?? 'N/A' }},
                {{ $transfer->transferDetails->first()->fromEmployee->first_name ?? 'N/A' }}
                {{ $transfer->transferDetails->first()->fromEmployee->middle_name ?? '' }}</span><br>
            <span
                style="color:#555;">{{ $transfer->transferDetails->first()->fromEmployee->position ?? '' }}</span><br>
            Employee Signature
        </div>
        <div class="signature">
            Received By:<br><br>

            ___________________________<br>
            <span style="color:#555;"> {{ $transfer->transferDetails->first()->toEmployee->last_name ?? 'N/A' }},
                {{ $transfer->transferDetails->first()->toEmployee->first_name ?? 'N/A' }}
                {{ $transfer->transferDetails->first()->toEmployee->middle_name ?? '' }} </span><br>
            <span style="color:#555;">{{ $transfer->transferDetails->first()->toEmployee->position ?? '' }}</span><br>
            Employee Signature
        </div>
    </div>

</body>

</html>
