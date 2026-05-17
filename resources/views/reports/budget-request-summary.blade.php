<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Budget Request Summary Report</title>

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
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">BUDGET REQUEST SUMMARY REPORT</div>
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
                    <th>Request Date</th>
                    <th>APV No</th>
                    <th>Purpose</th>
                    <th>Remarks</th>
                    <th>Location</th>
                    <th>Requested By</th>
                    <th>Amount</th>
                    <th>Status</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $grandTotalQty = 0;
                @endphp

                @foreach ($requests as $request)
                    @php
                        $grandTotal += $request->budgetDetails->sum('total_price');

                        $statuses = [
                            0 => 'Pending',
                            1 => 'Submitted',
                            2 => 'Approved',
                            3 => 'Rejected',
                            4 => 'Voided',
                        ];
                    @endphp
                    <tr>
                        <td>{{ Carbon::parse($request->requested_at)->format('Y-m-d') }}</td>
                        <td>{{ $request->apv_no }}</td>
                        <td>{{ $request->purpose }}</td>
                        <td>{{ $request->remarks }}</td>
                        <td>{{ $request->location->name ?? '' }}</td>
                        <td>{{ $request->requester->lname . ', ' . $request->requester->fname . ' ' . $request->requester->mname }}
                        </td>
                        @php $detailTotal = 0; @endphp
                        @foreach ($request->budgetDetails as $detail)
                            @php
                                $detailTotal += $detail->total_price;
                            @endphp
                        @endforeach
                        <td class="text-right">{{ number_format($detailTotal, 2) }}</td>
                        <td>{{ $statuses[$request->status] ?? 'Unknown' }}</td>

                    </tr>
                @endforeach

                @if ($requests->isEmpty())
                    <tr>
                        <td colspan="8" style="text-align: center; font-size: 11px; color: #555">No budget requests
                            found.
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="6" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        {{-- <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p> --}}

        <div class="page-number">
            Generated on {{ now()->format('F d, Y') }}
        </div>
</body>

</html>
