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
                    <img src="{{ public_path('images/logo.png') }}" style="width:80px;">
                </td>
                <td width="93%" style="border:0; text-align:center;">
                    <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
                    <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
                </td>
            </tr>
        </table>
        <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">EMPLOYEE MILESTONE REPORT</div>
        <div class="sub-title">Generated on: {{ now()->format('F d, Y') }}</div>
        <div class="sub-title">Status: {{ $statusLabel }}</div>
    </div>

    {{-- EMPLOYEE MILESTONE --}}
    <div class="section">
        <div class="section-title">Employee Milestone</div>
        @forelse ($groups as $milestone => $list)
            <h3>{{ $milestone }} Year Milestone ({{ $list->count() }})</h3>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Position</th>
                        <th>Location</th>
                        <th>Date Hired</th>
                        <th>Years of Service</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $emp)
                        <tr>
                            <td>{{ $emp->last_name }}, {{ $emp->first_name }} {{ $emp->middle_name }}</td>
                            <td>{{ $emp->position }}</td>
                            <td>{{ $emp->location->name ?? '' }}</td>
                            <td style="width: 80px;">{{ \Carbon\Carbon::parse($emp->hire_date)->format('M d, Y') }}
                            </td>
                            <td>{{ $emp->years_of_service }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @empty
            <p>No employees found.</p>
        @endforelse
    </div>
    <p style="text-align: center; font-size: 8px; color:#555"><i>***Nothing Follows***</i></p>
</body>

</html>
