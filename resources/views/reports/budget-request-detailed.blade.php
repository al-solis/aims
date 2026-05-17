<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Budget Request Detailed Report</title>

    <style>
        @page {
            margin-top: 0.1in;
            margin-right: 0.5in;
            margin-bottom: 0.2in;
            margin-left: 0.5in;
        }

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
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">BUDGET REQUEST DETAILED REPORT</div>
        <div class="sub-title">Range: {{ $pDateRange != 'custom' ? $pDateRange : $pFromDate . ' to ' . $pToDate }}</div>
        <div class="sub-title">Location: {{ $pLocation }}</div>
        <div class="sub-title">Requested By: {{ $pEmployee }}</div>
        <div class="sub-title">Status: {{ $pStatus !== null ? $pStatus : 'All' }}</div>
    </div>

    {{-- DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;

                    $statuses = [
                        0 => 'Pending',
                        1 => 'Submitted',
                        2 => 'Approved',
                        3 => 'Rejected',
                        4 => 'Voided',
                    ];
                @endphp

                @forelse ($requests as $request)

                    {{-- HEADER ROW (GROUP HEADER) --}}
                    <tr style="background:#f2f2f2; font-weight:bold;">
                        <td colspan="6">
                            APV: {{ $request->apv_no }} |
                            Date: {{ Carbon::parse($request->requested_at)->format('Y-m-d') }} |
                            Location: {{ $request->location->name ?? '' }} |
                            Requested By: {{ trim($request->requester->lname . ', ' . $request->requester->fname) }} |
                            Status: {{ $statuses[$request->status] ?? 'Unknown' }}
                        </td>
                    </tr>

                    @php $subTotal = 0; @endphp

                    {{-- DETAILS --}}
                    @foreach ($request->budgetDetails as $detail)
                        @php
                            $subTotal += $detail->total_price;
                            $grandTotal += $detail->total_price;
                        @endphp

                        <tr>
                            <td>{{ $detail->item_code }}</td>
                            <td>{{ $detail->item_description }}</td>
                            <td>{{ $detail->unit->name ?? '' }}</td>

                            <td class="text-right">{{ number_format($detail->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($detail->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($detail->total_price, 2) }}</td>
                        </tr>
                    @endforeach

                    {{-- SUBTOTAL --}}
                    <tr style="font-weight:bold;">
                        <td colspan="5" class="text-right">Subtotal</td>
                        <td class="text-right">{{ number_format($subTotal, 2) }}</td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; font-size:11px; color:#555;">
                            No budget requests found.
                        </td>
                    </tr>
                @endforelse

                {{-- GRAND TOTAL --}}
                <tr style="font-weight:bold; background:#e5e5e5;">
                    <td colspan="5" class="text-right">Grand Total</td>
                    <td class="text-right">{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
        {{-- <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p> --}}

        <div class="page-number">
            Generated on {{ now()->format('F d, Y') }}
        </div>
</body>

</html>
