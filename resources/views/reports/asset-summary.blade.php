<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8">
    <title>Asset Summary Report</title>

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
        <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
        <br>
        <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">ASSET SUMMARY REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Category: {{ $pCategory }}</div>
        <div class="sub-title">Location: {{ $pLocation }}</div>
        <div class="sub-title">Status: {{ $pStatus }}</div>
        <div class="sub-title">Sort: {{ ucfirst($sortField) }}</div>
    </div>

    {{-- DETAILS --}}
    <div class="section">
        {{-- <div class="section-title">Report Details</div> --}}
        <table>
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Purchase Date</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @foreach ($assets as $asset)
                    @php
                        $grandTotal += $asset->cost;

                        $statuses = [
                            1 => 'Available',
                            2 => 'Active',
                            3 => 'Assigned',
                            4 => 'Maintenance',
                            5 => 'Retired',
                            6 => 'Lost',
                            7 => 'Damaged',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $asset->asset_code }}</td>
                        <td>{{ $asset->name }}</td>
                        <td>{{ $asset->category->name ?? 'N/A' }}</td>
                        <td>{{ $asset->location->name ?? 'N/A' }}</td>
                        <td>{{ $asset->assigned_user ? $asset->assigned_user->last_name . ', ' . $asset->assigned_user->first_name . ' ' . $asset->assigned_user->middle_name : '' }}
                        </td>
                        <td>{{ $statuses[$asset->status] ?? 'Unknown' }}</td>
                        <td>{{ $asset->purchase_date ? Carbon::parse($asset->purchase_date)->format('m/d/Y') : 'N/A' }}
                        </td>
                        <td class="text-right">{{ $asset->cost ? number_format($asset->cost, 2) : 'N/A' }}</td>

                    </tr>
                @endforeach

                @if ($assets->isEmpty())
                    <tr>
                        <td colspan="8" style="text-align: center; font-size: 11px; color: #555">No assets found.
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="7" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 0) }}</strong></td>
                </tr>
            </tbody>
        </table>
        <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p>

        {{-- <div class="page-number">
            Page {PAGE_NUM} of {PAGE_COUNT} | Generated on {{ now()->format('F d, Y') }}
        </div> --}}
</body>

</html>
