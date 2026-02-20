<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Supplies Issuance Summary Report</title>

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
        <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
        <br>
        <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">SUPPLIES ISSUANCE SUMMARY REPORT</div>
        <div class="sub-title">Range: {{ $pDateRange != 'custom' ? $pDateRange : $pFromDate . ' to ' . $pToDate }}</div>
        <div class="sub-title">Location: {{ $pLocation }}</div>
        <div class="sub-title">Issued To: {{ $pEmployee }}</div>
    </div>

    {{-- DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}
        <table>
            <thead>
                <tr>
                    <th>Date Issued</th>
                    <th>Trx No</th>
                    <th>Purpose</th>
                    <th>Issued To</th>
                    <th>Location</th>
                    <th>Remarks</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                $grandTotalQty = 0; @endphp

                @foreach ($issued as $issue)
                    @php
                        $grandTotal += $issue->details->sum('total_cost');

                        $statuses = [
                            1 => 'Active',
                            2 => 'Inactive',
                        ];
                    @endphp
                    <tr>
                        <td>{{ Carbon::parse($issue->issuance_date)->format('Y-m-d') }}</td>
                        <td>{{ $issue->issuance_number }}</td>
                        <td>{{ $issue->purpose }}</td>
                        <td>{{ $issue->issued_to ? $issue->issuedTo->last_name . ', ' . $issue->issuedTo->first_name : '' }}
                        </td>
                        <td>{{ $issue->location_id ? $issue->location->name : '' }}</td>
                        <td>{{ $issue->remarks }}</td>
                        @php $detailTotal = 0; @endphp
                        @foreach ($issue->details as $detail)
                            @php
                                $detailTotal += $detail->total_cost;
                            @endphp
                        @endforeach
                        <td class="text-right">{{ number_format($detailTotal, 2) }}</td>
                    </tr>
                @endforeach

                @if ($issued->isEmpty())
                    <tr>
                        <td colspan="7" style="text-align: center; font-size: 11px; color: #555">No issued supplies
                            found.
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="6" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p>

        <div class="page-number">
            Generated on {{ now()->format('F d, Y') }}
        </div>
</body>

</html>
