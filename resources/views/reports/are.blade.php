<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Accountability Form</title>

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

        .no-border-table {
            border: none;
        }

        .no-border-table th,
        .no-border-table td {
            border: none !important;
            padding: 4px 6px;
        }

        .no-border-table {
            margin-top: 0;
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

        .footer {
            width: 100%;
            margin-top: 40px;
            text-align: center;
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
    {{-- <p style="text-align: justify">This accountability form does not automatically imply salary deduction in the event
        of
        damaged or malfunctioning equipment. In cases where an item is reported as defective or broken. An incident
        report or letter of report must first be conducted and submitted to properly assess the cause of the damage. The
        management will review the findings of the report to determine responsibility, ensuring that any action taken is
        fair, justified, and based on verified facts rather than assumptions.</p> --}}

    <div class="section">
        {{-- <div class="section-title">Accountability Information</div> --}}
        <table class="no-border-table">
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
                    {{ $employee->location?->name ? $employee->location?->name . ' / ' . $employee->location?->address : '' }}
                </td>
            </tr>
        </table>
    </div>
    {{-- <div>
        <p style="text-align: center">{{ $employee->location->name ?? 'N/A' }}<br>
            {{ $employee->location->description ?? '' }}</p>
    </div> --}}
    <p></p>


    {{-- CLEARANCE DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Accountability Details</div> --}}

        <table class="no-border-table">
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
    <p></p>
    <p style="text-align: justify">I hereby acknowledge receipt of and accept accountability of the above asset issued
        to me in accordance
        with the company policies and procedures.
    </p>
    <p style="text-align: justify">
        I further understand the the issued Asset is for Company use only and failure to comply with the company
        policies and procedures
        will be subject to disciplinary action, and any damanges due to my negligence will be automatically charged
        and/or
        deducted to my salary.
    </p>

    {{-- SIGNATURES --}}
    {{-- <div class="footer">
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

    </div> --}}

    <div class="footer">
        {{-- RELEASED BY --}}
        <div class="signature">
            <div class="signature-label">
                Released by:
            </div>
            <div class="signature-area">
                {{-- LINE --}}
                <div class="signature-line"></div>
                @php
                    $file = $printedBy?->employee_code . '.png';
                    $signaturePath = public_path('images/' . $file);
                @endphp
                <img src="{{ file_exists($signaturePath) ? $signaturePath : '' }}" class="signature-image">

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
                        @if ($employee)
                            {{ strtoupper(
                                $employee->first_name . ' ' . substr($employee->middle_name ?? '', 0, 1) . '. ' . $employee->last_name,
                            ) }}
                        @endif
                    </strong>
                </div>
            </div>

            <div class="signature-position">
                {{ $employee?->position ?? '' }}
            </div>
        </div>

    </div>

</body>

</html>
