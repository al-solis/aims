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
use App\Models\ddo_header;
use App\Models\ddo_detail;
use App\Models\numseq;
use App\Models\budget_header;
use App\Models\budget_detail;
use App\Models\User;
use App\Models\budget_routing;
use App\Models\budget_approval;

class ReportController extends Controller
{
    // ReportController.php
    public function index()
    {
        // Load data for dropdowns
        $categories = Category::all();
        $suppliesCategories = SuppliesCategory::all();
        $locations = Location::orderByRaw('LTRIM(RTRIM(name))')->get();
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
        $pDateRange = $request->date_range ?? 'this_month';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';
        $pType = $request->reptype ?? 'summary';
        $pCategory = Category::find($request->category)->name ?? 'All Categories';
        $pLocation = Location::find($request->location)->name ?? 'All Locations';
        $empcode = Auth::user()->employee_code;
        $preparedBy = Employee::where('employee_code', $empcode)->first();
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

        $dateRangeLabels = [
            'this_month' => Carbon::now()->format('F Y'),
            'last_month' => Carbon::now()->subMonth()->format('F Y'),
            'this_quarter' => Carbon::now()->startOfQuarter()->format('F Y') . ' - ' . Carbon::now()->endOfQuarter()->format('F Y'),
            'this_year' => Carbon::now()->format('Y'),
            'custom' => 'custom',
        ];

        $query = Asset::with(['category', 'location', 'assigned_user']);

        if ($request->input('reptype') == 'summary') {
            switch ($pDateRange) {
                case 'this_month':
                    $query->whereMonth('purchase_date', Carbon::now()->month)
                        ->whereYear('purchase_date', Carbon::now()->year);
                    break;

                case 'last_month':
                    $query->whereMonth('purchase_date', Carbon::now()->subMonth()->month)
                        ->whereYear('purchase_date', Carbon::now()->subMonth()->year);
                    break;

                case 'this_quarter':
                    $query->whereBetween('purchase_date', [
                        Carbon::now()->startOfQuarter()->format('Y-m-d'),
                        Carbon::now()->endOfQuarter()->format('Y-m-d')
                    ]);
                    break;

                case 'this_year':
                    $query->whereYear('purchase_date', Carbon::now()->year);
                    break;

                case 'custom':
                    if ($pFromDate && $pToDate) {
                        $query->whereBetween('purchase_date', [$pFromDate, $pToDate]);
                    }
                    break;
            }
        } else {
            $latestIds = DB::table('employee_ids')
                ->select(
                    'employee_id',
                    DB::raw('MAX(expiry_date) as max_expiry_date')
                )
                ->where('id_type_id', 8)
                ->groupBy('employee_id');

            $queryDDO = DB::table('ddo_headers as a')
                ->join('ddo_details as b', 'a.id', '=', 'b.ddo_header_id')
                ->join('employees as c', 'b.employee_id', '=', 'c.id')

                ->leftJoinSub($latestIds, 'x', function ($join) {
                    $join->on('c.id', '=', 'x.employee_id');
                })

                ->leftJoin('employee_ids as d', function ($join) {
                    $join->on('c.id', '=', 'd.employee_id')
                        ->on('d.expiry_date', '=', 'x.max_expiry_date')
                        ->where('d.id_type_id', 8);
                })

                ->where('a.location_id', $request->location)

                ->select(
                    'c.last_name',
                    'c.first_name',
                    'c.middle_name',
                    'c.mobile',
                    'd.id_number',
                    DB::raw("FORMAT(d.expiry_date, 'MMMM d, yyyy') as license_expiry")
                )

                ->groupBy(
                    'c.last_name',
                    'c.first_name',
                    'c.middle_name',
                    'c.mobile',
                    'd.id_number',
                    'd.expiry_date'
                );
            if ($queryDDO->exists()) {
                $employeeLists = $queryDDO
                    ->orderByRaw('LTRIM(RTRIM(last_name))')
                    ->orderByRaw('LTRIM(RTRIM(first_name))')
                    ->orderByRaw('LTRIM(RTRIM(middle_name))')
                    ->get();
            } else {
                $latestIds = DB::table('employee_ids')
                    ->select(
                        'employee_id',
                        DB::raw('MAX(expiry_date) as max_expiry_date')
                    )
                    ->where('id_type_id', 8)
                    ->groupBy('employee_id');

                $employeeLists = Employee::from('employees as c')
                    ->leftJoinSub($latestIds, 'x', function ($join) {
                        $join->on('c.id', '=', 'x.employee_id');
                    })

                    ->leftJoin('employee_ids as d', function ($join) {
                        $join->on('c.id', '=', 'd.employee_id')
                            ->on('d.expiry_date', '=', 'x.max_expiry_date')
                            ->where('d.id_type_id', 8);
                    })

                    ->where('c.location_id', $request->location)

                    ->select(
                        'c.last_name',
                        'c.first_name',
                        'c.middle_name',
                        'c.mobile',
                        'd.id_number',
                        DB::raw("FORMAT(d.expiry_date, 'MMMM d, yyyy') as license_expiry")
                    )

                    ->orderByRaw('LTRIM(RTRIM(c.last_name))')
                    ->orderByRaw('LTRIM(RTRIM(c.first_name))')
                    ->orderByRaw('LTRIM(RTRIM(c.middle_name))')
                    ->get();
            }
        }

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
        $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';

        if ($request->input('reptype') == 'summary') {
            $pdf = PDF::loadView('reports.asset-summary', compact('assets', 'pCategory', 'pStatus', 'pLocation', 'sortField', 'pDateRange', 'dateRangeLabels', 'pFromDate', 'pToDate', 'preparedBy'))
                ->setPaper('letter', 'portrait');
            return $pdf->stream('asset-summary.pdf');
        } else {
            $pdf = PDF::loadView('reports.asset-inventory', compact('assets', 'employeeLists', 'pCategory', 'pStatus', 'pLocation', 'sortField', 'pDateRange', 'dateRangeLabels', 'pFromDate', 'pToDate', 'preparedBy'))
                ->setPaper('letter', 'portrait');
            return $pdf->stream('asset-inventory.pdf');
        }
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
        // dd($request->all());
        $reportType = $request->input('report_type', '1'); // Default to 1 if not provided
        $reportFile = $reportType == '1' ? 'employee' : ($reportType == '2' ? 'employee-disposition' : 'employee-lesp-expiry');
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
            3 => 'Resigned',
            4 => 'Retired',
            5 => 'Terminated',
        ];

        $dateRangeLabels = [
            'this_month' => Carbon::now()->format('F Y'),
            'last_month' => Carbon::now()->subMonth()->format('F Y'),
            'this_quarter' => Carbon::now()->startOfQuarter()->format('F Y') . ' - ' . Carbon::now()->endOfQuarter()->format('F Y'),
            'this_year' => Carbon::now()->format('Y'),
            'custom' => 'custom',
        ];

        $statusLabel = $statusLabel[$pStatus] ?? 'All Statuses';

        //dd($query->toSql());
        if ($reportType == '1') {
            $query = Employee::query()->with('location');

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

            if ($request->filled('location')) {
                $query->where('location_id', $request->location);
            }
            if ($pStatus != null && $pStatus != '') {
                $query->where('status', $pStatus);
            }

            $sortField = $request->get('sort', 'hire_date');
            $sortDirection = $request->get('direction', 'desc');

            $employees = $query->orderBy($sortField, $sortDirection)->get();
            // Generate PDF
            $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';

            $pdf = Pdf::loadView(
                'reports.' . $reportFile,
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
            )->setPaper('letter', $reportType == '2' ? 'landscape' : 'portrait');

        } elseif ($reportType == '2') {
            $noOfClients = location::where('status', 1)->count();
            $noOfGuards = Employee::whereRaw('LOWER(position) LIKE ?', ['%guard%'])->get();
            $noOfSecurityOfficers = Employee::whereRaw('LOWER(position) LIKE ?', ['%security officer%'])->get();
            $noOfPrivateDetectives = Employee::whereRaw('LOWER(position) LIKE ?', ['%private detective%'])->get();
            $noOfSecurityConsultants = Employee::whereRaw('LOWER(position) LIKE ?', ['%security consultant%'])->count();
            $noOfSPAs = Employee::whereRaw('LOWER(position) LIKE ?', ['%special protection agent%'])->count();
            $noOfTrainingDirectors = Employee::whereRaw('LOWER(position) LIKE ?', ['%training director%'])->count();
            $noOfTrainingOfficers = Employee::whereRaw('LOWER(position) LIKE ?', ['%training officer%'])->count();
            $totalSecEmployees = Employee::whereRaw('LOWER(position) LIKE ?', ['%guard%'])
                ->orWhereRaw('LOWER(position) LIKE ?', ['%security officer%'])
                ->orWhereRaw('LOWER(position) LIKE ?', ['%private detective%'])
                ->orWhereRaw('LOWER(position) LIKE ?', ['%security consultant%'])
                ->orWhereRaw('LOWER(position) LIKE ?', ['%special protection agent%'])
                ->orWhereRaw('LOWER(position) LIKE ?', ['%training director%'])
                ->orWhereRaw('LOWER(position) LIKE ?', ['%training officer%'])
                ->count();

            $noOfFirearms = Asset::where('category_id', 1)
                ->whereIn('status', [1, 2, 3, 8])
                ->get();

            $query = DB::table('employees as e')
                ->join('locations as l', 'e.location_id', '=', 'l.id')
                ->leftJoin('assets as a', function ($join) {
                    $join->on('a.assigned_to', '=', 'e.id')
                        ->where('a.category_id', 1); // firearms only
                })
                ->leftJoin('employee_ids as ids', function ($join) {
                    $join->on('e.id', '=', 'ids.employee_id')
                        ->where('ids.id_type_id', 8); // LESP ID
                })
                ->leftJoin('employee_ids as ids2', function ($join) {
                    $join->on('e.id', '=', 'ids2.employee_id')
                        ->where('ids2.id_type_id', 1); // SSS Id
                })
                ->where('e.status', 1) // Only active employees                
                ->select(
                    'l.name as location_name',
                    'l.address as location_address',
                    'ids.id_number as license_no',
                    'ids.expiry_date as license_expiry',
                    'ids2.id_number as sss_no',
                    'e.id as employee_id',
                    'e.first_name',
                    'e.middle_name',
                    'e.last_name',
                    'e.gender',
                    'e.highest_education',
                    'e.position',
                    'e.sss_rate',
                    'a.manufacturer as asset_kind',
                    'a.model as asset_make',
                    'a.serial as asset_serial',
                    DB::raw("FORMAT(MAX(ids.expiry_date), 'MMMM d, yyyy') as id_expiration")
                );

            if ($request->filled('location')) {
                $query->where('e.location_id', $request->location);
            }

            $employees = $query
                ->groupBy(
                    'l.name',
                    'l.address',
                    'ids.id_number',
                    'ids.expiry_date',
                    'ids2.id_number',
                    'e.id',
                    'e.first_name',
                    'e.middle_name',
                    'e.last_name',
                    'e.gender',
                    'e.highest_education',
                    'e.position',
                    'e.sss_rate',
                    'a.manufacturer',
                    'a.model',
                    'a.serial'
                )
                ->get()
                ->groupBy('location_name') // FIX: use location_name, not location_id
                ->map(function ($locGroup) {
                    return $locGroup->groupBy('employee_id');
                });

            $sortField = $request->get('sort', 'hire_date');
            $sortDirection = $request->get('direction', 'desc');

            //employee gains
            $queryGains = DB::table('employees as e')
                ->join('locations as l', 'e.location_id', '=', 'l.id')

                ->leftJoinSub(
                    DB::table('employee_history')
                        ->select('employee_id', DB::raw('MAX(end_date) as max_end_date'))
                        ->groupBy('employee_id'),
                    'h_max',
                    'e.id',
                    '=',
                    'h_max.employee_id'
                )

                ->leftJoin('employee_history as h', function ($join) {
                    $join->on('e.id', '=', 'h.employee_id')
                        ->on('h.end_date', '=', 'h_max.max_end_date');
                })

                ->where('e.status', 1);

            $queryLosses = DB::table('employees as e')
                ->join('locations as l', 'e.location_id', '=', 'l.id')
                ->whereIn('e.status', [3, 4, 5]) // resigned, retired, terminated
                ->whereNotNull('e.termination_date'); // Ensure termination_date is not null

            switch ($pDateRange) {
                case 'this_month':
                    $queryGains->whereMonth('hire_date', Carbon::now()->month)
                        ->whereYear('hire_date', Carbon::now()->year);

                    $queryLosses->whereMonth('termination_date', Carbon::now()->month)
                        ->whereYear('termination_date', Carbon::now()->year);
                    break;

                case 'last_month':
                    $queryGains->whereMonth('hire_date', Carbon::now()->subMonth()->month)
                        ->whereYear('hire_date', Carbon::now()->subMonth()->year);

                    $queryLosses->whereMonth('termination_date', Carbon::now()->subMonth()->month)
                        ->whereYear('termination_date', Carbon::now()->subMonth()->year);
                    break;

                case 'this_quarter':
                    $queryGains->whereBetween('hire_date', [
                        Carbon::now()->startOfQuarter()->format('Y-m-d'),
                        Carbon::now()->endOfQuarter()->format('Y-m-d')
                    ]);

                    $queryLosses->whereBetween('termination_date', [
                        Carbon::now()->startOfQuarter()->format('Y-m-d'),
                        Carbon::now()->endOfQuarter()->format('Y-m-d')
                    ]);
                    break;

                case 'this_year':
                    $queryGains->whereYear('hire_date', Carbon::now()->year);
                    $queryLosses->whereYear('termination_date', Carbon::now()->year);
                    break;

                case 'custom':
                    if ($pFromDate && $pToDate) {
                        $queryGains->whereBetween('hire_date', [$pFromDate, $pToDate]);

                        $queryLosses->whereBetween('termination_date', [$pFromDate, $pToDate]);
                    }
                    break;
            }

            $queryGains = $queryGains->select(
                'l.name as location_name',
                DB::raw("FORMAT(MAX(h.end_date), 'MMMM d, yyyy') as end_date"),
                'e.first_name',
                'e.middle_name',
                'e.last_name',
                'e.position',
                'e.hire_date',
                'h.company as previous_employer',
            );

            $queryLosses = $queryLosses->select(
                'l.name as location_name',
                DB::raw("FORMAT(MAX(e.termination_date), 'MMMM d, yyyy') as end_date"),
                'e.first_name',
                'e.middle_name',
                'e.last_name',
                'e.position',
                'e.termination_date',
                'e.status'
            );

            $gains = $queryGains->groupBy(
                'l.name',
                'e.first_name',
                'e.middle_name',
                'e.last_name',
                'e.position',
                'e.hire_date',
                'h.company'
            )->get();

            $losses = $queryLosses->groupBy(
                'l.name',
                'e.first_name',
                'e.middle_name',
                'e.last_name',
                'e.position',
                'e.termination_date',
                'e.status'
            )->get();

            $dateRangeLabels = $dateRangeLabels[$pDateRange] ?? 'custom';

            $pdf = Pdf::loadView(
                'reports.' . $reportFile,
                compact(
                    'employees',
                    'pDateRange',
                    'dateRangeLabels',
                    'pFromDate',
                    'pToDate',
                    'pLocationName',
                    'statusLabel',
                    'sortField',
                    'sortDirection',
                    'gains',
                    'losses',
                    'noOfClients',
                    'noOfGuards',
                    'noOfSecurityOfficers',
                    'noOfPrivateDetectives',
                    'noOfSecurityConsultants',
                    'noOfSPAs',
                    'noOfTrainingDirectors',
                    'noOfTrainingOfficers',
                    'totalSecEmployees',
                    'noOfFirearms'
                )
            )->setPaper('letter', $reportType == '2' ? 'landscape' : 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isPhpEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'chroot' => public_path(),
                ]);
        } elseif ($reportType == '3') {
            $query = Employee::query()->with('location');

            switch ($pDateRange) {
                case 'this_month':
                    $query->whereHas('employeeIds', function ($q) {
                        $q->where('id_type_id', 8) // LESP ID
                            ->whereMonth('expiry_date', Carbon::now()->month)
                            ->whereYear('expiry_date', Carbon::now()->year);
                    });
                    break;

                case 'last_month':
                    $query->whereHas('employeeIds', function ($q) {
                        $q->where('id_type_id', 8) // LESP ID
                            ->whereMonth('expiry_date', Carbon::now()->subMonth()->month)
                            ->whereYear('expiry_date', Carbon::now()->subMonth()->year);
                    });
                    break;

                case 'this_quarter':
                    $query->whereHas('employeeIds', function ($q) {
                        $q->where('id_type_id', 8) // LESP ID
                            ->whereBetween('expiry_date', [
                                Carbon::now()->startOfQuarter()->format('Y-m-d'),
                                Carbon::now()->endOfQuarter()->format('Y-m-d')
                            ]);
                    });
                    break;

                case 'this_year':
                    $query->whereHas('employeeIds', function ($q) {
                        $q->where('id_type_id', 8) // LESP ID
                            ->whereYear('expiry_date', Carbon::now()->year);
                    });
                    break;

                case 'custom':
                    if ($pFromDate && $pToDate) {
                        $query->whereHas('employeeIds', function ($q) use ($pFromDate, $pToDate) {
                            $q->where('id_type_id', 8) // LESP ID
                                ->whereBetween('expiry_date', [$pFromDate, $pToDate]);
                        });
                    }
                    break;
            }
            if ($request->filled('location')) {
                $query->where('location_id', $request->location);
            }
            if ($pStatus != null && $pStatus != '') {
                $query->where('status', $pStatus);
            }

            $employees = $query->get()->sortBy('employeeIds.0.expiry_date');
            ;

            $dateRangeLabels = [
                'this_month' => Carbon::now()->format('F Y'),
                'last_month' => Carbon::now()->subMonth()->format('F Y'),
                'this_quarter' => Carbon::now()->startOfQuarter()->format('F Y') . ' - ' . Carbon::now()->endOfQuarter()->format('F Y'),
                'this_year' => Carbon::now()->format('Y'),
                'custom' => 'custom',
            ];

            $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';

            // Generate PDF
            $pdf = Pdf::loadView(
                'reports.' . $reportFile,
                compact(
                    'employees',
                    'pDateRange',
                    'pFromDate',
                    'pToDate',
                    'pLocationName',
                    'statusLabel',
                    'pDateRange',
                )
            )->setPaper('letter', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isPhpEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'chroot' => public_path(),
                ]);
        } else {
            abort(400, 'Invalid report type');
        }

        return $pdf->stream('employee-report.pdf');
    }

    public function dutyDetailOrderReport(Request $request)
    {
        // dd($request->all());
        $pLocation = Location::find($request->location)->name ?? 'All Locations';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';

        $withDDOSetup = ddo_header::where('location_id', $request->location)
            ->exists();

        if ($withDDOSetup) {
            // DB::statement("SET SESSION group_concat_max_len = 1000000");

            $query = DB::table('ddo_headers as a')
                ->join('ddo_details as b', 'a.id', '=', 'b.ddo_header_id')
                ->join('employees as c', 'b.employee_id', '=', 'c.id')
                ->leftJoin('locations as l', 'a.location_id', '=', 'l.id')

                ->leftJoin('assets as d', function ($join) {
                    $join->on('d.assigned_to', '=', 'c.id')
                        ->where('d.category_id', 1);
                })

                ->leftJoin('asset_licenses as e', 'e.asset_id', '=', 'd.id')

                ->where('c.status', 1)
                ->where('a.location_id', $request->location)

                ->select(
                    'c.id as employee_id',
                    'c.last_name',
                    'c.first_name',
                    'c.middle_name',
                    'c.position',

                    'l.name as location_name',
                    'l.address as location_address',

                    'd.id as asset_id',
                    'd.subcategory',
                    'd.caliber',
                    'd.manufacturer',
                    'd.model',
                    'd.name as asset_name',
                    'd.serial',

                    DB::raw("
            FORMAT(MAX(e.expiration_date), 'MMMM d, yyyy') as expiration_date
        ")
                )

                ->groupBy(
                    'c.id',
                    'c.last_name',
                    'c.first_name',
                    'c.middle_name',
                    'c.position',
                    'l.name',
                    'l.address',
                    'd.id',
                    'd.subcategory',
                    'd.caliber',
                    'd.model',
                    'd.manufacturer',
                    'd.name',
                    'd.serial'
                );
        } else {
            $query = DB::table('employees as c')
                ->join('locations as l', 'c.location_id', '=', 'l.id')
                ->leftJoin('assets as d', function ($join) {
                    $join->on('d.assigned_to', '=', 'c.id')
                        ->where('d.category_id', 1); // firearm filter HERE
                })
                ->leftJoin('asset_licenses as e', 'e.asset_id', '=', 'd.id')
                ->where('c.status', 1)
                ->where('c.location_id', $request->location)

                ->select(
                    'c.id as employee_id',
                    'c.last_name',
                    'c.first_name',
                    'c.middle_name',
                    'c.position',
                    'l.name as location_name',
                    'l.address as location_address',
                    'd.id as asset_id',
                    'd.subcategory',
                    'd.manufacturer',
                    'd.caliber',
                    'd.model',
                    'd.name as asset_name',
                    'd.serial',

                    DB::raw("FORMAT(MAX(e.expiration_date), 'MMMM d, yyyy') as expiration_date")
                )
                ->groupBy(
                    'c.id',
                    'c.last_name',
                    'c.first_name',
                    'c.middle_name',
                    'c.position',
                    'l.name',
                    'l.address',
                    'd.id',
                    'd.subcategory',
                    'd.caliber',
                    'd.model',
                    'd.manufacturer',
                    'd.name',
                    'd.serial'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | UNION QUERY
        |--------------------------------------------------------------------------
        */

        $unionQuery = DB::table('employees as c')
            ->leftJoin('assets as d', function ($join) {
                $join->on('d.assigned_to', '=', 'c.id')
                    ->where('d.category_id', 1);
            })
            ->leftJoin('asset_licenses as e', 'e.asset_id', '=', 'd.id')
            ->leftJoin('locations as l', 'd.location_id', '=', 'l.id')

            ->where('c.status', 1)
            ->where('c.employee_code', 99999)
            ->where('d.location_id', $request->location)

            ->select(
                'c.id as employee_id',
                DB::raw("'' as last_name"),
                DB::raw("'' as first_name"),
                DB::raw("'' as middle_name"),
                'c.position',

                'l.name as location_name',
                'l.address as location_address',

                'd.id as asset_id',
                'd.subcategory',
                'd.caliber',
                'd.manufacturer',
                'd.model',
                'd.name as asset_name',
                'd.serial',

                DB::raw("FORMAT(MAX(e.expiration_date), 'MMMM d, yyyy') as expiration_date")
            )

            ->groupBy(
                'c.id',
                'c.last_name',
                'c.first_name',
                'c.middle_name',
                'c.position',
                'l.name',
                'l.address',
                'd.id',
                'd.subcategory',
                'd.caliber',
                'd.model',
                'd.manufacturer',
                'd.name',
                'd.serial'
            );

        // APPLY UNION
        $query = $query
            ->union($unionQuery)
            ->orderBy('last_name')
            ->orderBy('asset_name')
            ->get();

        // $employees = $query->groupBy('employee_id');       
        $employees = $query
            ->groupBy('location_name')
            ->map(function ($locGroup) {
                return $locGroup->groupBy('employee_id');
            });

        // dd($employees);

        if ($request->draft) {
            $ddoFormatted = 'DRAFT - ' . Carbon::now()->format('F Y');
        } else {
            $ddoseq = numseq::where('name', 'ddo')
                ->where('month', Carbon::now()->month)
                ->where('year', Carbon::now()->year)
                ->first();

            if ($ddoseq) {
                $ddoseq->current_number += 1;
                $ddoseq->save();
            } else {
                $ddoseq = numseq::create([
                    'name' => 'ddo',
                    'month' => Carbon::now()->month,
                    'year' => Carbon::now()->year,
                    'current_number' => 1,
                ]);
            }

            $monthYear = Carbon::now()->format('F Y'); // April 2026
            $sequence = str_pad($ddoseq->current_number, 3, '0', STR_PAD_LEFT); // 004

            $ddoFormatted = $monthYear . '-' . $sequence;
        }

        $pdf = Pdf::loadView(
            'reports.duty-detail-order',
            compact(
                'employees',
                'pFromDate',
                'pToDate',
                'pLocation',
                'ddoFormatted'
            )
        )->setPaper('letter', 'portrait');

        return $pdf->stream('duty-detail-order.pdf');
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
        //dd($request->all());
        $pDateRange = $request->date_range ?? 'this_month';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';
        $pType = $request->reptype ?? 'summary';
        $pSupplier = Supplier::find($request->supplier)->name ?? 'All Suppliers';
        $pEmployee = Employee::find($request->employee)->last_name ?? 'All Employees';

        $dateRangeLabels = [
            'this_month' => Carbon::now()->format('F Y'),
            'last_month' => Carbon::now()->subMonth()->format('F Y'),
            'this_quarter' => Carbon::now()->startOfQuarter()->format('F Y') . ' - ' . Carbon::now()->endOfQuarter()->format('F Y'),
            'this_year' => Carbon::now()->format('Y'),
            'custom' => 'custom',
        ];

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
        $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';

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
            'this_month' => Carbon::now()->format('F Y'),
            'last_month' => Carbon::now()->subMonth()->format('F Y'),
            'this_quarter' => Carbon::now()->startOfQuarter()->format('F Y') . ' - ' . Carbon::now()->endOfQuarter()->format('F Y'),
            'this_year' => Carbon::now()->format('Y'),
            'custom' => 'custom',
        ];

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
        $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';

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

    public function budgetRequestReport(Request $request)
    {
        // dd($request->all());
        $pDateRange = $request->date_range ?? 'this_month';
        $pFromDate = $request->from_date ?? '';
        $pToDate = $request->to_date ?? '';
        $pType = $request->reptype ?? 'summary';
        $pStatus = $request->status !== null ? (int) $request->status : null;
        $pLocation = $request->location ? Location::find($request->location)->name ?? 'All Locations' : 'All Locations';
        $pEmployee = $request->employee ? Employee::find($request->employee)?->last_name . ', ' . Employee::find($request->employee)?->first_name . ' ' . Employee::find($request->employee)?->middle_name ?? 'All Employees' : 'All Employees';

        $dateRangeLabels = [
            'this_month' => Carbon::now()->format('F Y'),
            'last_month' => Carbon::now()->subMonth()->format('F Y'),
            'this_quarter' => Carbon::now()->startOfQuarter()->format('F Y') . ' - ' . Carbon::now()->endOfQuarter()->format('F Y'),
            'this_year' => Carbon::now()->format('Y'),
            'custom' => 'custom',
        ];

        // $orientation = $pType == 'summary' ? 'portrait' : 'landscape';
        $orientation = 'portrait';
        $query = budget_header::query()
            ->with([
                'budgetDetails',
                'location',
                'requester',
                'latestApproval'
            ]);

        switch ($pDateRange) {
            case 'this_month':
                $query->whereMonth('requested_at', Carbon::now()->month)
                    ->whereYear('requested_at', Carbon::now()->year);
                break;

            case 'last_month':
                $query->whereMonth('requested_at', Carbon::now()->subMonth()->month)
                    ->whereYear('requested_at', Carbon::now()->subMonth()->year);
                break;

            case 'this_quarter':
                $query->whereBetween('requested_at', [
                    Carbon::now()->startOfQuarter()->format('Y-m-d'),
                    Carbon::now()->endOfQuarter()->format('Y-m-d')
                ]);
                break;

            case 'this_year':
                $query->whereYear('requested_at', Carbon::now()->year);
                break;

            case 'custom':
                if ($pFromDate && $pToDate) {
                    $query->whereBetween('requested_at', [$pFromDate, $pToDate]);
                }
                break;
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        if ($request->filled('employee')) {
            $empCode = Employee::find($request->employee)->employee_code ?? null;
            $user = User::where('employee_code', $empCode)->first();
            $query->where('requested_by', $user->id);
        }

        if ($pStatus !== null) {

            // Pending / Submitted / Voided
            if (in_array($pStatus, [0, 1, 4])) {
                $query->where('status', $pStatus);
            }

            // Approved
            elseif ($pStatus == 2) {
                $query->whereHas('latestApproval', function ($q) {
                    $q->where('approved', 1);
                });
            }

            // Disapproved
            elseif ($pStatus == 3) {
                $query->whereHas('latestApproval', function ($q) {
                    $q->where('approved', 0);
                });
            }
        }

        $status = [
            0 => 'Pending',
            1 => 'Submitted',
            2 => 'Approved',
            3 => 'Rejected',
            4 => 'Voided',
        ];

        $pStatus = $status[$request->status] ?? 'All Statuses';
        $requests = $query->orderBy('requested_at')->get();
        // Generate PDF
        $pDateRange = $dateRangeLabels[$pDateRange] ?? 'Custom Range';
        $pdf = Pdf::loadView(
            'reports.budget-request-' . strtolower($pType),
            compact(
                'requests',
                'pDateRange',
                'pFromDate',
                'pToDate',
                'pLocation',
                'pEmployee',
                'pType',
                'pStatus'
            )
        )->setPaper('letter', $orientation);
        return $pdf->stream('budget-request-report.pdf');
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
            'this_month' => Carbon::now()->format('F Y'),
            'last_month' => Carbon::now()->subMonth()->format('F Y'),
            'this_quarter' => Carbon::now()->startOfQuarter()->format('F Y') . ' - ' . Carbon::now()->endOfQuarter()->format('F Y'),
            'this_year' => Carbon::now()->format('Y'),
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
