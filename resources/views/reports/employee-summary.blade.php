<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Summary Report</title>

    <style>
        @page {
            margin-top: 0.1in;
            margin-right: 0.5in;
            margin-bottom: 0.4in;
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

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 4px;
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
            font-size: 10px;
        }

        table th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
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
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">EMPLOYEE SUMMARY REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Location: {{ $pLocationName }}</div>
        <div class="sub-title">Status: {{ $statusLabel }}</div>
        <div class="sub-title">Date Hired:
            {{ $pDateRange == 'Custom Range' ? $pFromDate . ' to ' . $pToDate : $pDateRange }}</div>
    </div>

    {{-- SUMMARY PER STATUS --}}
    <div class="section">
        <div class="section-title">Employees per Status</div>
        <table>
            <thead>
                <tr>
                    <th width="8%" class="text-center">No.</th>
                    <th>Status</th>
                    <th width="20%" class="text-right">No. of Employees</th>
                    <th width="15%" class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($statusCounts as $status => $count)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $statusLabels[$status] ?? 'Unknown (' . $status . ')' }}</td>
                        <td class="text-right">{{ number_format($count) }}</td>
                        <td class="text-right">
                            {{ $grandTotal > 0 ? number_format(($count / $grandTotal) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="font-size: 11px; color: #555">
                            No employee records found.
                        </td>
                    </tr>
                @endforelse

                @if ($statusCounts->isNotEmpty())
                    <tr class="total-row">
                        <td colspan="2" class="text-right">TOTAL</td>
                        <td class="text-right">{{ number_format($grandTotal) }}</td>
                        <td class="text-right">100.0%</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- LOCATION x STATUS --}}
    @if ($matrix->isNotEmpty())
        <div class="section">
            <div class="section-title">Employees per Location / Department</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No.</th>
                        <th>Location / Department</th>
                        @foreach ($statusColumns as $s)
                            <th class="text-right">{{ $statusLabels[$s] ?? $s }}</th>
                        @endforeach
                        @if ($statusColumns->count() > 1)
                            <th class="text-right">Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($matrix as $locationName => $counts)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $locationName }}</td>
                            @foreach ($statusColumns as $s)
                                <td class="text-right">{{ $counts[$s] ?? 0 ?: '-' }}</td>
                            @endforeach
                            @if ($statusColumns->count() > 1)
                                <td class="text-right"><strong>{{ number_format($counts->sum()) }}</strong></td>
                            @endif
                        </tr>
                    @endforeach

                    <tr class="total-row">
                        <td colspan="2" class="text-right">TOTAL</td>
                        @foreach ($statusColumns as $s)
                            <td class="text-right">{{ number_format($columnTotals[$s]) }}</td>
                        @endforeach
                        @if ($statusColumns->count() > 1)
                            <td class="text-right">{{ number_format($grandTotal) }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p>

</body>

</html>
