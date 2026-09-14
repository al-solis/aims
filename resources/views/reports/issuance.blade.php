<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Supplies Issuance Form</title>

    <style>
        @page {
            margin-top: 0.1in;
            margin-right: 0.5in;
            margin-bottom: 0.2in;
            margin-left: 0.5in;
        }

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
            position: relative;
            width: 45%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .signature-label {
            margin-bottom: 5px;
            text-align: left;
        }

        .signature-area {
            position: relative;
            width: 260px;
            height: 75px;
            margin: 0 auto;
        }

        .signature-line {
            position: absolute;
            width: 260px;
            left: 0;
            bottom: 25px;
            border-bottom: 1px solid #000;
            z-index: 1;
        }

        .signature-image {
            position: absolute;
            width: 80px;
            height: auto;
            left: 50%;
            bottom: 10px;
            transform: translateX(-50%);
            z-index: 3;
        }

        .signature-name {
            position: absolute;
            width: 100%;
            left: 0;
            bottom: 0;
            text-align: center;
            z-index: 2;
        }

        .signature-position {
            margin-top: 2px;
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
    <div class="footer">
        {{-- Issued BY --}}
        <div class="signature">
            <div class="signature-label">
                Issued by:
            </div>
            <div class="signature-area">
                {{-- LINE --}}
                <div class="signature-line"></div>

                {{-- SIGNATURE --}}
                <img src="{{ public_path('images/SUP.png') }}" class="signature-image">

                {{-- NAME --}}
                <div class="signature-name">
                    @if ($printedBy)
                        <strong>
                            {{ strtoupper($printedBy?->first_name . ' ' . $printedBy?->middle_name . ' ' . $printedBy?->last_name) }}
                        </strong>
                    @endif
                </div>
            </div>
            <div class="signature-position">
                {{ $printedBy?->position ?? '' }}
            </div>
        </div>

        {{-- RECEIVED BY --}}
        <div class="signature">
            <div class="signature-label">
                Received by:
            </div>

            <div class="signature-area">
                {{-- LINE --}}
                <div class="signature-line"></div>

                {{-- NAME --}}
                <div class="signature-name">
                    <strong>
                        @if ($issuances->issuedTo)
                            {{ strtoupper(
                                $issuances->issuedTo->first_name .
                                    ' ' .
                                    substr($issuances->issuedTo->middle_name ?? '', 0, 1) .
                                    '. ' .
                                    $issuances->issuedTo->last_name,
                            ) }}
                        @endif
                    </strong>
                </div>
            </div>

            <div class="signature-position">
                {{ $issuances->issuedTo?->position ?? '' }}
            </div>
        </div>

    </div>


    <div class="page-number">
        Generated on {{ now()->format('F d, Y') }}
    </div>
</body>

</html>
