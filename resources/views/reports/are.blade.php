<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Acknowledgement Receipt for Equipment</title>

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
        <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
        <br>
        <br>
        <br>
        <div class="sub-title mb-4" style="font-weight: bolder; font-size: 15px">ACKNOWLEDGEMENT RECEIPT FOR EQUIPMENT
        </div>

        <p style="text-align: right">Date: <strong><u>{{ today()->format('j F Y') }}</u></strong></p>
    </div>
    <br>
    <br>
    <p style="text-align: justify">I acknowledge to receive from PROPERTY SECTION, the following property for which I am
        responsible,
        subject to the provision of the Accounting Law and which will be used in the office of the</p>

    <div>
        <p style="text-align: center">{{ $employee->location->name ?? 'N/A' }}<br>
            {{ $employee->location->description ?? '' }}</p>
    </div>


    {{-- CLEARANCE DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Accountability Details</div> --}}

        <table>
            <thead>
                <tr>
                    <th>Property No.</th>
                    <th>Asset Description</th>
                    <th>Serial No.</th>
                    <th>Acquisition Date</th>
                    <th class="text-right">Cost</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($assets as $asset)
                    @php
                        $grandTotal += $asset->cost;
                    @endphp

                    <tr>
                        <td>{{ $asset->asset_code ?? 'N/A' }}</td>
                        <td>{{ $asset->name ?? 'N/A' }}</td>
                        <td>{{ $asset->serial ?? 'N/A' }}</td>
                        <td>{{ Carbon::parse($asset->purchase_date)->format('m/d/Y') }}</td>
                        <td class="text-right">{{ number_format($asset->cost, 2) }}</td>
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
                </tr>
            </tbody>
        </table>


    </div>

    {{-- SIGNATURES --}}
    <div class="footer">
        <div>. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
            . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
            .</div>
        <div class="signature">
            Received By:<br><br>
            <br>
            <u>{{ $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name }}</u><br>
            Signature Over Printed Name
        </div>
        <div class="signature" style="float:right;">
            Received From:<br><br>
            <br>
            ___________________________<br>
            Signature Over Printed Name
        </div>
    </div>

</body>

</html>
