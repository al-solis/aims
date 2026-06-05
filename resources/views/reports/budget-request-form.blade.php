<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Equipment Request Form</title>

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
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">EQUIPMENT REQUEST</div>
        <div class="sub-title">Request No: {{ $budget->apv_no }}@if ($budget->status == '4')
                <span style="color: red; font-weight: bold;">(Cancelled)</span>
            @endif
        </div>
    </div>

    {{-- EMPLOYEE INFO --}}
    <div class="section">
        <table>
            <tr>
                <td width="20%"><strong>Requestor's Name:</strong></td>
                <td width="30%">
                    {{ $budget->requester->lname }},
                    {{ $budget->requester->fname }}
                    {{ $budget->requester->mname }}
                </td>
                <td width="20%"><strong>Date:</strong></td>
                <td width="30%">
                    {{ $budget->requested_at->format('F d, Y') }}
                </td>
            </tr>
            <td><strong>Location/ Post:</strong></td>
            <td colspan="3">
                {{ $budget->location->name ?? 'N/A' }}
            </td>

            <tr>
                <td><strong>Purpose:</strong></td>
                <td colspan="3">
                    {{ $budget->purpose ?? '' }}
                </td>
            </tr>

            <tr>
                <td><strong>Remarks:</strong></td>
                <td colspan="3">
                    {{ $budget->remarks ?? '' }}
                </td>
            </tr>

            {{-- <tr>
                <td><strong>Reference:</strong></td>
                <td colspan="3">
                    {{ $budget->latestApproval->approver->lname ?? '' }},
                    {{ $budget->latestApproval->approver->fname ?? '' }}
                    {{ $budget->latestApproval->approver->mname ?? '' }} |
                    {{ $budget->latestApproval->approved == '1' ? 'Approved' : 'Rejected' }} |
                    {{ $budget->latestApproval->created_at->format('F d, Y h:i A') ?? '' }}
                </td>
            </tr> --}}
        </table>
    </div>

    {{-- CLEARANCE DETAILS --}}
    <div class="section">
        <div class="section-title">Item(s)</div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>UOM</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($budget->budgetDetails as $detail)
                    @php
                        $grandTotal += $detail->total_price;
                    @endphp

                    <tr>
                        <td>{{ $detail->item_code ?? '' }}</td>
                        <td>{{ $detail->item_description ?? '' }}</td>
                        <td>{{ $detail->unit->name ?? '' }}</td>
                        <td class="text-right">{{ number_format($detail->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($detail->total_price, 2) }}</td>
                    </tr>
                @endforeach

                @if ($budget->budgetDetails->isEmpty())
                    <tr>
                        <td colspan="6" style="text-align: center; font-size: 11px; color: #555">No items found.</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="5" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>


    </div>
    {{-- <p style="text-align: center; font-size: 11px; color:#555">Nothing follows</p> --}}

    {{-- SIGNATURES --}}
    <div class="footer">
        <div class="signature">
            {{ $budget->latestApproval->approved == '1' ? 'Approved' : 'Rejected' }} by: <br>
            <br>
            <br>
            <span style="text-decoration: underline;">
                {{ $budget->latestApproval->approver->fname ?? '' }}
                {{ $budget->latestApproval->approver->mname ?? '' }}
                {{ $budget->latestApproval->approver->lname ?? '' }}
                |
                {{ $budget->latestApproval->created_at->format('F d, Y h:i A') ?? '' }}

            </span><br>

            {{ $budget->latestApproval->approver->employee->position ?? '' }}
            <br>
        </div>
        <div class="signature" style="float:right;">
            Checked by: <br>
            <br>
            <br>
            ___________________________<br>
            Authorized Officer
        </div>
    </div>

    <div class="page-number">
        Generated on {{ now()->format('F d, Y') }}
    </div>
</body>

</html>
