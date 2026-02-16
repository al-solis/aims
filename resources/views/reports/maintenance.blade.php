<!DOCTYPE html>
<html>
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Maintenance Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 10px;
            line-height: 1.4;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #333;
        }

        .header p {
            margin: 0;
            color: #666;
            font-size: 11px;
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

        .filter-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 10px;
        }

        .filter-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .filter-info td {
            padding: 3px 5px;
        }

        .filter-label {
            font-weight: bold;
            width: 120px;
        }

        .summary-stats {
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }

        .stat-box {
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
            background-color: #fff;
        }

        .stat-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .breakdown-section {
            margin-bottom: 20px;
        }

        .breakdown-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid #999;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .breakdown-item {
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .breakdown-item .label {
            font-size: 8px;
            color: #666;
        }

        .breakdown-item .value {
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #ddd;
        }

        td {
            padding: 4px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .bg-light {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">{{ env('APP_COMPANY_NAME') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_ADDRESS') }}</div>
        <div class="sub-title">{{ env('APP_COMPANY_CONTACT') }}</div>
        <br>
        <br>
        <div class="sub-title" style="font-weight: bolder; font-size: 15px">MAINTENANCE REPORT</div>
        <div class="sub-title">Generated on: {{ $generated_at }}</div>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td class="filter-label">Date Range:</td>
                <td>{{ $pDateRange }} @if ($pDateRange == 'Custom Range' && $pFromDate && $pToDate)
                        ({{ $pFromDate }} to {{ $pToDate }})
                    @endif
                </td>
                <td class="filter-label">Maintenance Type:</td>
                <td>{{ $pType }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="breakdown-title">SUMMARY STATISTICS</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total Maintenances</div>
                <div class="stat-value">{{ $statistics['total_maintenances'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Cost</div>
                <div class="stat-value">₱{{ number_format($statistics['total_cost'], 2) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Average Cost</div>
                <div class="stat-value">₱{{ number_format($statistics['average_cost'], 2) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Unique Assets</div>
                <div class="stat-value">{{ $statistics['unique_assets'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Completion Rate</div>
                <div class="stat-value">
                    @php
                        $completed = $statistics['completed_count'];
                        $total = $statistics['total_maintenances'];
                        $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                    @endphp
                    {{ $rate }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="breakdown-section">
        <div class="breakdown-title">STATUS BREAKDOWN</div>
        <div class="breakdown-grid">
            <div class="breakdown-item">
                <div class="label">Pending</div>
                <div class="value">{{ $statistics['pending_count'] }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">In Progress</div>
                <div class="value">{{ $statistics['in_progress_count'] }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">Completed</div>
                <div class="value">{{ $statistics['completed_count'] }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">Cancelled</div>
                <div class="value">{{ $statistics['cancelled_count'] }}</div>
            </div>
        </div>
    </div>

    <!-- Type Breakdown -->
    <div class="breakdown-section">
        <div class="breakdown-title">MAINTENANCE TYPE BREAKDOWN</div>
        <div class="breakdown-grid">
            <div class="breakdown-item">
                <div class="label">Preventive</div>
                <div class="value">{{ $statistics['by_type']['1'] ?? 0 }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">Corrective</div>
                <div class="value">{{ $statistics['by_type']['2'] ?? 0 }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">Emergency</div>
                <div class="value">{{ $statistics['by_type']['3'] ?? 0 }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">Inspection</div>
                <div class="value">{{ $statistics['by_type']['4'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Detailed Maintenance Records -->
    <div class="breakdown-title">DETAILED MAINTENANCE RECORDS</div>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Date</th>
                <th>Asset</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Description</th>
                <th>Technician</th>
                <th>Status</th>
                @if ($include_costs)
                    <th class="text-right">Cost</th>
                @endif
                @if ($include_technicians)
                    <th>Notes</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $m)
                <tr>
                    <td>{{ $m->maintenance_code ?? 'N/A' }}</td>
                    <td>{{ $m->scheduled_date ? date('Y-m-d', strtotime($m->scheduled_date)) : 'N/A' }}</td>
                    <td>{{ $m->asset->name ?? 'N/A' }} ({{ $m->asset->asset_code ?? 'N/A' }})</td>
                    <td>{{ $typeLabels[$m->type] ?? 'Unknown' }}</td>
                    <td>{{ $priorityLabels[$m->priority] ?? ucfirst($m->priority) }}</td>
                    <td>{{ Str::limit($m->description, 30) }}</td>
                    <td>{{ $m->technician ?? 'N/A' }}</td>
                    <td>{{ $statusLabels[$m->status] ?? ucfirst($m->status) }}</td>
                    @if ($include_costs)
                        <td class="text-right">₱{{ number_format($m->cost ?? 0, 2) }}</td>
                    @endif
                    @if ($include_technicians)
                        <td>{{ Str::limit($m->notes, 20) ?? 'N/A' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 8 + ($include_costs ? 1 : 0) + ($include_technicians ? 1 : 0) }}"
                        class="text-center">
                        No maintenance records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($include_costs && $maintenances->count() > 0)
            <tfoot>
                <tr class="font-bold bg-light">
                    <td colspan="{{ 8 + ($include_technicians ? 1 : 0) }}" class="text-right">TOTAL:</td>
                    <td class="text-right">₱{{ number_format($statistics['total_cost'], 2) }}</td>
                    @if ($include_technicians)
                        <td></td>
                    @endif
                </tr>
            </tfoot>
        @endif
    </table>
</body>

</html>
