<!DOCTYPE html>
<html>

@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $employee = $transmittal->transmittedTo;
@endphp

<head>
    <meta charset="utf-8">

    <title>
        Asset Transmittal Form
    </title>

    <style>
        @page {
            margin-top: 0.1in;
            margin-right: 0.5in;
            margin-bottom: 0.2in;
            margin-left: 0.5in;
        }

        body {
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
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">

        <table width="100%" style="border:0;">

            <tr>

                <td width="12%" style="border:0;">

                    <img src="{{ public_path('images/logo.PNG') }}" style="width:70px;">
                </td>

                <td width="88%" style="border:0; text-align:center;">

                    <div class="title">
                        {{ env('APP_COMPANY_NAME') }}
                    </div>

                    <div class="sub-title">
                        {{ env('APP_COMPANY_ADDRESS') }}
                    </div>

                    <div class="sub-title">
                        {{ env('APP_COMPANY_CONTACT') }}
                    </div>
                </td>
            </tr>
        </table>

        <br>

        <div style="font-weight:bold; font-size:16px;">
            ASSET TRANSMITTAL FORM
        </div>

        <div style="margin-top:5px; font-size:13px;">
            TRANSMITTAL NO.
            <strong>
                {{ Str::substr($transmittal->transmittal_number, -14) }}
                @if ($transmittal->status == 0)
                    <span style="color: red; font-weight: bold;">(Voided)</span>
                @endif
            </strong>
        </div>
    </div>

    {{-- INFORMATION --}}
    <div class="section">

        <table>

            <tr>

                <td width="20%">
                    <strong>Transmitted To:</strong>
                </td>

                <td width="30%">
                    @if ($employee)
                        {{ $employee?->last_name }}, {{ $employee?->first_name }} {{ $employee?->middle_name }}
                    @endif
                </td>

                <td width="20%">
                    <strong>Date:</strong>
                </td>

                <td width="30%">
                    {{ Carbon::parse($transmittal->transmittal_date)->format('F d, Y') }}
                </td>
            </tr>

            <tr>

                <td>
                    <strong>Location:</strong>
                </td>

                <td colspan="3">

                    {{ $transmittal->location->name ?? '' }}

                    @if ($transmittal->location->description)
                        /
                        {{ $transmittal->location->description }}
                    @endif

                </td>
            </tr>

            <tr>

                <td>
                    <strong>Remarks:</strong>
                </td>

                <td colspan="3">
                    {{ $transmittal->remarks ?? '' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ASSET DETAILS --}}
    <div class="section">

        <table>

            <thead>

                <tr>
                    <th width="8%">Qty</th>
                    <th width="18%">Property No.</th>
                    <th width="36%">Asset Description</th>
                    <th width="18%">Serial No.</th>
                    <th width="20%">Brand/Model</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($transmittal->details as $detail)
                    <tr>

                        <td class="text-right">
                            {{ $detail->quantity }}
                        </td>
                        <td>
                            {{ $detail->asset->asset_code ?? '' }}
                        </td>
                        <td>
                            {{ $detail->asset->name ?? '' }}
                        </td>
                        <td>
                            {{ $detail->asset->serial ?? '' }}
                        </td>
                        <td>
                            {{ $detail->asset->manufacturer ?? '' }} {{ $detail->asset->model ?? '' }}
                        </td>
                    </tr>
                @endforeach

                @if ($transmittal->details->isEmpty())
                    <tr>

                        <td colspan="5" style="text-align:center; color:#777;">

                            No asset details found.
                        </td>
                    </tr>
                @endif

            </tbody>
        </table>
    </div>

    {{-- SIGNATURES --}}
    <div class="footer">

        <div class="signature">

            Released by:
            <br><br><br>

            <strong>
                <u>
                    {{ strtoupper($preparedBy->first_name . ' ' . $preparedBy->last_name) }}
                </u>
            </strong>

            <br>

            {{ $preparedBy->position ?? '' }}
        </div>

        <div class="signature" style="float:right;">

            Received by:
            <br><br><br>

            <strong>
                <u>
                    @if ($employee)
                        {{ strtoupper(
                            $employee?->first_name . ' ' . substr($employee?->middle_name ?? '', 0, 1) . '. ' . $employee?->last_name,
                        ) }}
                    @endif
                </u>
            </strong>

            <br>

            {{ $employee?->position ?? '' }}
        </div>
    </div>

</body>

</html>
