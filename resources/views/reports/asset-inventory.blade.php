<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

<head>
    <meta charset="utf-8">
    <title>Asset Inventory Report</title>

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
            text-align: center;
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
        {{-- <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">MONTHLY INVENTORY REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Range: {{ $pDateRange != 'custom' ? $pDateRange : $pFromDate . ' to ' . $pToDate }}</div>
        <div class="sub-title">Category: {{ $pCategory }}</div>
        <div class="sub-title">Location: {{ $pLocation }}</div>
        <div class="sub-title">Status: {{ $pStatus }}</div>
        <div class="sub-title">Sort: {{ ucfirst($sortField) }}</div> --}}
    </div>

    <span><strong>FOR
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;THE
            MANAGEMENT<br></strong></span>
    <span><strong>THRU &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {{ $preparedBy?->first_name }} {{ $preparedBy?->last_name }}<br></strong></span>
    <span><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {{ $preparedBy?->position }}<br></strong></span>
    <span><strong>SUBJECT &nbsp;&nbsp;&nbsp;
            MONTHLY INVENTORY REPORT<br></strong></span>
    <span><strong>DATE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {{ strtoupper(now()->format('F d, Y')) }}<br></strong></span>
    <span><strong>POST &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {{ $pLocation }}<br></strong></span>
    <br>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;THE SECURITY DETACHMENT POSTED HERE AT
        <strong>{{ $pLocation }}</strong>
        IS HEREBY SUBMITTED THE MONTHLY INVENTORY REPORT
        OF THIS DETACHMENT FOR THE MONTH OF <strong>{{ strtoupper($pDateRange) }}</strong> HERE AS FOLLOWS<br></span>

    {{-- DETAILS --}}
    {{-- DETAILS --}}
    <div class="section">

        @php
            $statuses = [
                1 => 'Available',
                2 => 'Active',
                3 => 'Assigned',
                4 => 'Maintenance',
                5 => 'Retired',
                6 => 'Lost',
                7 => 'Damaged',
            ];

            // Group assets by category name
            $groupedAssets = $assets->groupBy(function ($asset) {
                return $asset->category->name ?? 'Uncategorized';
            });

            $overallGrandTotal = 0;
        @endphp

        @forelse ($groupedAssets as $categoryName => $categoryAssets)

            @php
                $categoryTotal = 0;
            @endphp

            {{-- CATEGORY TITLE --}}
            <div class="section-title" style="margin-top:20px;">
                {{ strtoupper($categoryName) }}
            </div>

            {{-- CATEGORY TABLE --}}
            <table>
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Name</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Expiry Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($categoryAssets as $asset)
                        @php
                            $categoryTotal += $asset->cost;
                            $overallGrandTotal += $asset->cost;
                        @endphp

                        <tr>
                            <td>{{ $asset->asset_code }}</td>

                            <td>{{ $asset->name }}</td>

                            <td>{{ $asset->model ? $asset->model : $asset->manufacturer }}</td>

                            {{-- <td>
                                {{ $asset->assigned_user
                                    ? $asset->assigned_user->last_name .
                                        ', ' .
                                        $asset->assigned_user->first_name .
                                        ' ' .
                                        $asset->assigned_user->middle_name
                                    : '' }}
                            </td> --}}

                            <td>{{ $asset->serial }}</td>

                            <td>
                                {{-- {{ $asset->purchase_date ? Carbon::parse($asset->purchase_date)->format('m/d/Y') : '' }} --}}
                            </td>

                            {{-- <td>{{ $statuses[$asset->status] ?? 'Unknown' }}</td> --}}
                            <td>{{ $asset->remarks }}</td>

                            {{-- <td class="text-right">
                                {{ $asset->cost ? number_format($asset->cost, 2) : '' }}
                            </td> --}}
                        </tr>
                    @endforeach

                    {{-- CATEGORY TOTAL --}}
                    {{-- <tr>
                        <td colspan="6" class="text-right">
                            <strong>{{ $categoryName }} Total</strong>
                        </td>

                        <td class="text-right">
                            <strong>{{ number_format($categoryTotal, 2) }}</strong>
                        </td>
                    </tr> --}}
                </tbody>
            </table>

        @empty

            <table>
                <tr>
                    <td style="text-align:center;">
                        No assets found.
                    </td>
                </tr>
            </table>

        @endforelse

        {{-- OVERALL GRAND TOTAL --}}
        {{-- <table style="margin-top:20px;">
            <tr>
                <td width="85%" class="text-right">
                    <strong>OVERALL GRAND TOTAL</strong>
                </td>

                <td width="15%" class="text-right">
                    <strong>{{ number_format($overallGrandTotal, 2) }}</strong>
                </td>
            </tr>
        </table> --}}

        {{-- <p style="text-align: center; font-size: 8px; color:#555">
            <i>***Nothing Follows***</i>
        </p> --}}

    </div>
    <div class="section">
        <div class="section-title" style="margin-top:20px;">
            NAME OF SECURITY PERSONNEL, LIC., EXPIRATION DATE AND CONTACT NUMBER
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>License Expiry Date</th>
                    <th>License Number</th>
                    <th>Contact Number</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($employeeLists as $employee)
                    <tr>
                        <td>{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}</td>
                        <td>{{ $employee->license_expiry }}</td>
                        <td>{{ $employee->id_number }}</td>
                        <td>{{ $employee->mobile }}</td>
                    </tr>
                @endforeach

                @if ($employeeLists->isEmpty())
                    <tr>
                        <td colspan="4" style="text-align: center; font-size: 11px; color: #555">No personnel found.
                        </td>
                    </tr>
                @endif
            </tbody>
    </div>

    <div section="footer">
        <div class="signature" style="text-align: left;">
            Prepared By:<br><br>
            <br>
            {{-- <strong><u>{{ env('ARE_PREPARED_BY') }}</u></strong><br> --}}
            {{-- {{ env('ARE_PREPARED_BY_POSITION') }} --}}
            @if ($preparedBy)
                <strong><u>{{ $preparedBy->first_name ?? '' }} {{ ucfirst($preparedBy->middle_name) ?? '' }}.
                        {{ $preparedBy->last_name ?? '' }}</u></strong><br>
                {{ $preparedBy->position ?? '' }}
            @else
                <strong><u>{{ env('ARE_PREPARED_BY') }}</u></strong><br>
                {{ env('ARE_PREPARED_BY_POSITION') }}
            @endif

        </div>
    </div>
</body>

</html>
