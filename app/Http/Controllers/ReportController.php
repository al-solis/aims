<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Supplies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Odometer;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\SuppliesCategory;
use App\Models\Supplier;
use App\Models\receiving_header;
use App\Models\receiving_detail;
use App\Models\issuance_header;
use App\Models\issuance_detail;

class ReportController extends Controller
{
    // ReportController.php
    public function index()
    {
        // Load data for dropdowns
        $categories = Category::all();
        $suppliesCategories = SuppliesCategory::all();
        $locations = Location::orderBy('name')->get();
        $vehicles = Asset::whereIn('category_id', [2])->get();
        $suppliers = Supplier::orderBy('name')->get();
        $employees = Employee::orderBy('last_name')->get();

        return view(
            'reports.index',
            compact('categories', 'suppliesCategories', 'locations', 'vehicles', 'suppliers', 'employees')
        );
    }

    public function assetSummary(Request $request)
    {
        $pCategory = Category::find($request->category)->name ?? 'All Categories';
        $pLocation = Location::find($request->location)->name ?? 'All Locations';
        $statuses = [
            1 => 'Available',
            2 => 'Active',
            3 => 'Assigned',
            4 => 'Maintenance',
            5 => 'Retired',
            6 => 'Lost',
            7 => 'Damaged',
        ];

        $pStatus = $statuses[$request->status] ?? 'All Statuses';


        $query = Asset::with(['category', 'location', 'assigned_user']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        $sortField = $request->get('sort', 'name');
        $assets = $query->orderBy($sortField)->get();

        // Generate PDF or Excel
        // if ($request->format === 'excel') {
        //     return $this->exportAssetSummaryExcel($assets);
        // }

        $pdf = PDF::loadView('reports.asset-summary', compact('assets', 'pCategory', 'pStatus', 'pLocation', 'sortField'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream('asset-summary.pdf');
    }

    public function odometerReport(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $asset = Asset::findOrFail($request->asset_id);
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Odometer::with('employee:id,first_name,middle_name,last_name')
            ->where('asset_id', $asset->id);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        $odometerReadings = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView(
            'reports.odometer',
            compact(
                'asset',
                'odometerReadings',
                'fromDate',
                'toDate'
            )
        )
            ->setPaper('letter', 'portrait');

        return $pdf->stream('odometer-readings-' . $asset->asset_number . '.pdf');
    }

    public function employeeReport(Request $request)
    {
        $pDateRange = $request->date_range ?? 'this_month';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';
        $pStatus = $request->status ?? 1;
        $pLocation = $request->location ? Location::where('id', $request->location)->first() : null;
        $pLocationName = $pLocation ? $pLocation->name : 'All Locations';

        $statusLabel = [
            0 => 'Inactive',
            1 => 'Active',
            2 => 'On Leave',
        ];

        $statusLabel = $statusLabel[$pStatus] ?? 'All Statuses';

        $query = Employee::query()->with('location');
        //dd($query->toSql());
        switch ($pDateRange) {
            case 'this_month':
                $query->whereMonth('hire_date', Carbon::now()->month)
                    ->whereYear('hire_date', Carbon::now()->year);
                break;

            case 'last_month':
                $query->whereMonth('hire_date', Carbon::now()->subMonth()->month)
                    ->whereYear('hire_date', Carbon::now()->subMonth()->year);
                break;

            case 'this_quarter':
                $query->whereBetween('hire_date', [
                    Carbon::now()->startOfQuarter()->format('Y-m-d'),
                    Carbon::now()->endOfQuarter()->format('Y-m-d')
                ]);
                break;

            case 'this_year':
                $query->whereYear('hire_date', Carbon::now()->year);
                break;

            case 'custom':
                if ($pFromDate && $pToDate) {
                    $query->whereBetween('hire_date', [$pFromDate, $pToDate]);
                }
                break;
        }

        if ($pStatus != null && $pStatus != '') {
            $query->where('status', $pStatus);
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        $sortField = $request->get('sort', 'hire_date');
        $sortDirection = $request->get('direction', 'desc');

        $employees = $query->orderBy($sortField, $sortDirection)->get();
        // Generate PDF

        $pdf = Pdf::loadView(
            'reports.employee',
            compact(
                'employees',
                'pDateRange',
                'pFromDate',
                'pToDate',
                'pLocationName',
                'statusLabel',
                'sortField',
                'sortDirection'
            )
        )->setPaper('letter', 'portrait');

        return $pdf->stream('employee-report.pdf');
    }

    public function suppliesReport(Request $request)
    {
        // dd($request->all());
        $pCategory = SuppliesCategory::find($request->category)->name ?? 'All Categories';
        $pSupplier = Supplier::find($request->supplier)->name ?? 'All Suppliers';
        $statuses = [
            0 => 'Inactive',
            1 => 'Active',
        ];

        $balanceLabel = [
            'balance' => 'With Balance',
            'zero_balance' => 'Zero Balance',
            'reorder' => 'At or Below Reorder Level',
        ];

        $pBalance = $balanceLabel[$request->balance] ?? '';
        $pStatus = $statuses[$request->status] ?? 'All Statuses';

        $query = Supplies::with(['category', 'uom', 'supplier']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        if ($request->filled('balance')) {
            if ($request->balance == 'balance') {
                $query->where('available_stock', '>', 0);
            } elseif ($request->balance == 'zero_balance') {
                $query->where('available_stock', '=', 0);
            } elseif ($request->balance == 'reorder') {
                $query->whereColumn('available_stock', '<=', 'reorder_quantity');
            }
        }

        $sortField = $request->get('sort', 'name');
        $supplies = $query->orderBy($sortField)->get();

        // Generate PDF or Excel
        // if ($request->format === 'excel') {
        //     return $this->exportAssetSummaryExcel($assets);
        // }

        $pdf = PDF::loadView(
            'reports.supplies-summary',
            compact('supplies', 'pCategory', 'pStatus', 'pSupplier', 'sortField', 'pBalance')
        )
            ->setPaper('letter', 'landscape');
        return $pdf->stream('supplies-summary.pdf');
    }

    public function suppliesReceivingReport(Request $request)
    {
        // dd($request->all());
        $pDateRange = $request->date_range ?? 'this_month';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';
        $pType = $request->reptype ?? 'summary';
        $pSupplier = Supplier::find($request->supplier)->name ?? 'All Suppliers';
        $pEmployee = Employee::find($request->employee)->last_name ?? 'All Employees';

        $dateRangeLabels = [
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'custom' => 'custom',
        ];
        $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';
        $orientation = $pType == 'summary' ? 'portrait' : 'landscape';

        $query = receiving_header::query()->with('details.product', 'supplier', 'receiver', 'details.uom');

        switch ($pDateRange) {
            case 'this_month':
                $query->whereMonth('received_date', Carbon::now()->month)
                    ->whereYear('received_date', Carbon::now()->year);
                break;

            case 'last_month':
                $query->whereMonth('received_date', Carbon::now()->subMonth()->month)
                    ->whereYear('received_date', Carbon::now()->subMonth()->year);
                break;

            case 'this_quarter':
                $query->whereBetween('received_date', [
                    Carbon::now()->startOfQuarter()->format('Y-m-d'),
                    Carbon::now()->endOfQuarter()->format('Y-m-d')
                ]);
                break;

            case 'this_year':
                $query->whereYear('received_date', Carbon::now()->year);
                break;

            case 'custom':
                if ($pFromDate && $pToDate) {
                    $query->whereBetween('received_date', [$pFromDate, $pToDate]);
                }
                break;
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        if ($request->filled('employee')) {
            $query->where('received_by', $request->employee);
        }

        $query->where('status', 1);

        $receiving = $query->orderBy('received_date', 'desc')->get();
        // Generate PDF

        $pdf = Pdf::loadView(
            'reports.supplies-receiving-' . strtolower($pType),
            compact(
                'receiving',
                'pDateRange',
                'pFromDate',
                'pToDate',
                'pSupplier',
                'pEmployee',
                'pType'
            )
        )->setPaper('letter', $orientation);

        return $pdf->stream('supplies-receiving-report.pdf');
    }

    public function suppliesIssuanceReport(Request $request)
    {
        // dd($request->all());
        $pDateRange = $request->date_range ?? 'this_month';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';
        $pType = $request->reptype ?? 'summary';
        $pSupplier = Supplier::find($request->supplier)->name ?? 'All Suppliers';
        $pLocation = Location::find($request->location)->name ?? 'All Locations';
        $pEmployee = Employee::find($request->employee)->last_name ?? 'All Employees';

        $dateRangeLabels = [
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'custom' => 'custom',
        ];
        $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';
        $orientation = $pType == 'summary' ? 'portrait' : 'landscape';

        $query = issuance_header::query()->with('details.supply', 'location', 'issuedTo', 'details.uom');

        switch ($pDateRange) {
            case 'this_month':
                $query->whereMonth('issuance_date', Carbon::now()->month)
                    ->whereYear('issuance_date', Carbon::now()->year);
                break;

            case 'last_month':
                $query->whereMonth('issuance_date', Carbon::now()->subMonth()->month)
                    ->whereYear('issuance_date', Carbon::now()->subMonth()->year);
                break;

            case 'this_quarter':
                $query->whereBetween('issuance_date', [
                    Carbon::now()->startOfQuarter()->format('Y-m-d'),
                    Carbon::now()->endOfQuarter()->format('Y-m-d')
                ]);
                break;

            case 'this_year':
                $query->whereYear('issuance_date', Carbon::now()->year);
                break;

            case 'custom':
                if ($pFromDate && $pToDate) {
                    $query->whereBetween('issuance_date', [$pFromDate, $pToDate]);
                }
                break;
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        if ($request->filled('employee')) {
            $query->where('issued_to', $request->employee);
        }

        $query->where('status', 1);

        $issued = $query->orderBy('issuance_date', 'desc')->get();
        // Generate PDF

        $pdf = Pdf::loadView(
            'reports.supplies-issuance-' . strtolower($pType),
            compact(
                'issued',
                'pDateRange',
                'pFromDate',
                'pToDate',
                'pLocation',
                'pEmployee',
                'pType'
            )
        )->setPaper('letter', $orientation);

        return $pdf->stream('supplies-issuance-report.pdf');
    }

    public function maintenanceReport(Request $request)
    {
        // Validate request
        // dd($request->all());
        $rules = [
            'date_range' => 'required|in:this_month,last_month,this_quarter,this_year,custom',
            'type' => 'nullable|in:0,1,2,3,4',
            'include_costs' => 'nullable|boolean',
            'include_technicians' => 'nullable|boolean',
        ];

        if ($request->date_range === 'custom') {
            $rules['from_date'] = 'required|date';
            $rules['to_date'] = 'required|date|after_or_equal:from_date';
        }
        //dd($request->all());
        // $request->validate($rules); //DISABLED

        // Get parameter labels for display
        $dateRangeLabels = [
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'custom' => 'Custom Range',
        ];

        $typeLabels = [
            '0' => 'All Types',
            '1' => 'Preventive',
            '2' => 'Corrective',
            '3' => 'Emergency',
            '4' => 'Inspection',
        ];

        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $priorityLabels = [
            '1' => 'Low',
            '2' => 'Medium',
            '3' => 'High',
            '4' => 'Critical',
        ];

        // Build query
        $query = Maintenance::with('asset');

        // Apply date filter based on scheduled_date
        $this->applyMaintenanceDateFilter($query, $request);

        // Apply maintenance type filter
        if ($request->filled('type')) {
            if ($request->type != '0' && $request->type != '' && $request->type != null) {
                $query->where('type', $request->type);
            }
        }

        // Get results
        $maintenances = $query->orderBy('scheduled_date', 'desc')->get();

        // Calculate statistics
        $statistics = $this->calculateMaintenanceStatistics($maintenances);

        // Get parameter display values
        $pDateRange = $dateRangeLabels[$request->date_range] ?? 'Custom Range';
        $pType = $request->filled('type') ? ($typeLabels[$request->type] ?? 'All Types') : 'All Types';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';

        // Prepare data for view
        $data = [
            'maintenances' => $maintenances,
            'statistics' => $statistics,
            'typeLabels' => $typeLabels,
            'statusLabels' => $statusLabels,
            'priorityLabels' => $priorityLabels,
            'pDateRange' => $pDateRange,
            'pType' => $pType,
            'pFromDate' => $pFromDate,
            'pToDate' => $pToDate,
            'include_costs' => $request->boolean('include_costs'),
            'include_technicians' => $request->boolean('include_technicians'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        // Check format and return appropriate response
        // if ($request->format === 'excel') {
        //     // For now, just return a message since Excel export not implemented
        //     return back()->with('info', 'Excel export coming soon. Using PDF instead.');
        // }
        // return view('reports.maintenance', $data);

        $pdf = Pdf::loadView('reports.maintenance', $data);
        $pdf->setPaper('letter', 'landscape');

        $filename = 'maintenance-report-' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($filename);

        // Generate PDF
        try {
            $pdf = Pdf::loadView('reports.maintenance', $data);
            $pdf->setPaper('letter', 'landscape');

            $filename = 'maintenance-report-' . Carbon::now()->format('Y-m-d') . '.pdf';

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
        }
    }

    private function applyMaintenanceDateFilter($query, Request $request)
    {
        switch ($request->date_range) {
            case 'this_month':
                $query->whereMonth('scheduled_date', Carbon::now()->month)
                    ->whereYear('scheduled_date', Carbon::now()->year);
                break;

            case 'last_month':
                $query->whereMonth('scheduled_date', Carbon::now()->subMonth()->month)
                    ->whereYear('scheduled_date', Carbon::now()->subMonth()->year);
                break;

            case 'this_quarter':
                $query->whereBetween('scheduled_date', [
                    Carbon::now()->startOfQuarter()->format('Y-m-d'),
                    Carbon::now()->endOfQuarter()->format('Y-m-d')
                ]);
                break;

            case 'this_year':
                $query->whereYear('scheduled_date', Carbon::now()->year);
                break;

            case 'custom':
                $query->whereBetween('scheduled_date', [
                    $request->from_date,
                    $request->to_date
                ]);
                break;
        }
    }

    private function calculateMaintenanceStatistics($maintenances)
    {
        $totalCost = 0;
        $pendingCount = 0;
        $inProgressCount = 0;
        $completedCount = 0;
        $cancelledCount = 0;

        $typeCount = [
            '1' => 0, // Preventive
            '2' => 0, // Corrective
            '3' => 0, // Emergency
            '4' => 0, // Inspection
        ];

        $priorityCount = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'critical' => 0,
        ];

        foreach ($maintenances as $maintenance) {
            // Sum costs
            $totalCost += $maintenance->cost ?? 0;

            // Count by status
            switch ($maintenance->status) {
                case 1:
                    $pendingCount++;
                    break;
                case 2:
                    $inProgressCount++;
                    break;
                case 3:
                    $completedCount++;
                    break;
                case 4:
                    $cancelledCount++;
                    break;
            }

            // Count by type
            if (isset($typeCount[$maintenance->type])) {
                $typeCount[$maintenance->type]++;
            }

            // Count by priority
            if (isset($priorityCount[$maintenance->priority])) {
                $priorityCount[$maintenance->priority]++;
            }
        }

        // Calculate average cost
        $averageCost = $maintenances->count() > 0 ? $totalCost / $maintenances->count() : 0;

        // Get unique assets count
        $uniqueAssets = $maintenances->pluck('asset_id')->unique()->count();

        return [
            'total_maintenances' => $maintenances->count(),
            'total_cost' => $totalCost,
            'average_cost' => $averageCost,
            'unique_assets' => $uniqueAssets,
            'pending_count' => $pendingCount,
            'in_progress_count' => $inProgressCount,
            'completed_count' => $completedCount,
            'cancelled_count' => $cancelledCount,
            'by_type' => $typeCount,
            'by_priority' => $priorityCount,
        ];
    }


}
