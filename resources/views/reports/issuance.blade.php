<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Supplies Issuance Form</title>

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

        .page-number {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: left;
            font-size: 8px;
            color: #666;
            padding: 5px 0;
            border-top: 1px solid #ddd;
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
            <div class="sub-title" style="font-weight: bolder; font-size: 15px">SUPPLIES ISSUANCE FORM</div>
            <div class="sub-title">Issuance No: {{ $issuances->issuance_number }} @if ($issuances->status == 0)
                    <span style="color: red; font-weight: bold;">(Voided)</span>
                @endif
            </div>
            <div class="sub-title">Date: {{ Carbon::parse($issuances->issuance_date)->format('F j, Y') }}</div>
    </div>

    {{-- TRANSFER INFO --}}
    <div class="section">
        <div class="section-title">Issuance Information</div>
        <table>
            <tr>
                <td width="25%"><strong>Issued To:</strong></td>
                <td width="75%">
                    {{ $issuances->issuedTo->last_name ?? '' }}, {{ $issuances->issuedTo->first_name ?? '' }}
                    {{ $issuances->issuedTo->middle_name ?? '' }}
                </td>

                <td width="25%"><strong>Location:</strong></td>
                <td width="75%">
                    {{ $issuances->Location->name ?? '' }}
                </td>
            </tr>
            <tr>
                <td><strong>Purpose:</strong></td>
                <td colspan="3">{{ $issuances->purpose ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>Additional Information:</strong></td>
                <td colspan="3">{{ $issuances->remarks ?? '' }}</td>
            </tr>
        </table>
    </div>

    {{-- TRANSFER DETAILS --}}
    <div class="section">
        <div class="section-title">Issuance Details</div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>UOM</th>
                    <th class="text-right">Purchase Cost</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($issuances->details as $detail)
                    @php
                        $grandTotal += $detail->total_cost;
                    @endphp

                    <tr>
                        <td>{{ $detail->supply->code ?? '' }}</td>
                        <td>{{ $detail->supply->name ?? '' }}</td>
                        <td class="text-right">{{ number_format($detail->quantity, 2) }}</td>
                        <td>{{ $detail->uom->name ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($detail->unit_cost, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->total_cost, 2) }}</td>
                    </tr>
                @endforeach

                @if ($issuances->details->isEmpty())
                    <tr>
                        <td colspan="6" style="text-align: center; font-size: 11px; color: #555">No issuance
                            details found.</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="5" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- <p style="text-align: center; font-size: 11px; color:#555">This clearance certificate is valid only when properly
        signed
        and dated.</p> --}}
    <div class="page-number">
        Generated on {{ now()->format('F d, Y') }}
    </div>
</body>

</html>
