<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Accountability Form</title>

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
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">ACCOUNTABILITY FORM</div>
    </div>

    <br>
    <p style="text-align: justify">This accountability form does not automatically imply salary deduction in the event
        of
        damaged or malfunctioning equipment. In cases where an item is reported as defective or broken. An incident
        report or letter of report must first be conducted and submitted to properly assess the cause of the damage. The
        management will review the findings of the report to determine responsibility, ensuring that any action taken is
        fair, justified, and based on verified facts rather than assumptions.</p>

    <div class="section">
        {{-- <div class="section-title">Accountability Information</div> --}}
        <table>
            <tr>
                <td width="30%"><strong>Issued To:</strong></td>
                <td width="70%">
                    {{ $employee->last_name ?? '' }}, {{ $employee->first_name ?? '' }}
                    {{ $employee->middle_name ?? '' }}
                </td>

                <td width="30%"><strong>Date:</strong></td>
                <td width="70%">
                    {{ today()->format('j F Y') }}
                </td>
            </tr>
            <tr>
                <td><strong>Location/ Post:</strong></td>
                <td colspan="3">
                    {{ $employee->location->name ? $employee->location->name . ' / ' . $employee->location->description : '' }}
                </td>
            </tr>
        </table>
    </div>
    {{-- <div>
        <p style="text-align: center">{{ $employee->location->name ?? 'N/A' }}<br>
            {{ $employee->location->description ?? '' }}</p>
    </div> --}}


    {{-- CLEARANCE DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Accountability Details</div> --}}

        <table>
            <thead>
                <tr>
                    <th>Qty</th>
                    <th>Property No.</th>
                    <th>Serial No.</th>
                    <th>Asset Description</th>
                    <th class="text-right">Unit Price</th>
                    <th>Acquisition Date</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($assets as $asset)
                    @php
                        $grandTotal += $asset->cost;
                    @endphp

                    <tr>
                        <td class="text-right">1</td>
                        <td>{{ $asset->asset_code ?? '' }}</td>
                        <td>{{ $asset->serial ?? '' }}</td>
                        <td>{{ $asset->name ?? '' }}</td>
                        <td class="text-right">{{ number_format($asset->cost, 2) }}</td>
                        <td>{{ Carbon::parse($asset->purchase_date)->format('m/d/Y') }}</td>
                    </tr>
                @endforeach

                @if ($assets->isEmpty())
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

    {{-- SIGNATURES --}}
    <div class="footer">
        {{-- <div>. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
            . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
            .</div> --}}
        <div class="signature">
            Released by:<br><br>
            <br>
            <strong><u>{{ env('ARE_PREPARED_BY') }}</u></strong><br>
            {{ env('ARE_PREPARED_BY_POSITION') }}
        </div>
        <div class="signature" style="float:right;">
            Received by:<br><br>
            <br>
            <strong><u>{{ $employee->first_name . ' ' . substr($employee->middle_name, 0, 1) . '. ' . $employee->last_name }}</u></strong><br>
            {{ $employee->position ?? '' }}
        </div>

    </div>

</body>

</html>
