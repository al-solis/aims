<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\asset as Asset;
use App\Models\location as Location;
use Carbon\Carbon;
use App\Models\maintenance as Maintenance;
use App\Models\asset_license as AssetLicense;
use App\Models\clearance_header as ClearanceHeader;
use App\Models\employee as Employee;
use App\Models\setting;
use App\Models\supplies as Supplies;
use App\Models\cluster as Cluster;

/**
 * Dashboard controller - written to run on both Microsoft SQL Server and PostgreSQL.
 *
 * Portability rules used in this file:
 *  - No big "WHERE id IN (?, ?, ...)" lists: SQL Server rejects queries with > 2100 bound
 *    parameters. Location filters are expressed as a sub-select instead.
 *  - No whereDate(): it becomes CAST(col AS date) / col::date, which is slow (not index friendly).
 *    Plain range comparisons on the raw column are used instead.
 *  - Every non-aggregated SELECT column is in GROUP BY (PostgreSQL is strict about this).
 *  - Request IDs are cast to integers (PostgreSQL throws on "abc" for a bigint column).
 *  - No vendor functions (FORMAT, to_char, DATEPART ...). Month bucketing is done in PHP.
 */
class MainController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // ================= FILTERS =================
        // Accept only positive integers; anything else is ignored.
        $toId = fn($v) => (is_scalar($v) && ctype_digit((string) $v) && (int) $v > 0) ? (int) $v : null;

        $filterClusterId = $toId($request->input('searchcluster'));
        $filterLocationId = $toId($request->input('searchloc'));

        // Reconcile the two filters: a location outside the chosen cluster is dropped (cluster wins).
        $selectedLocation = $filterLocationId ? Location::find($filterLocationId) : null;
        if ($filterLocationId && !$selectedLocation) {
            $filterLocationId = null; // unknown location id
        } elseif (
            $selectedLocation && $filterClusterId
            && (int) $selectedLocation->cluster_id !== (int) $filterClusterId
        ) {
            $filterLocationId = null;
            $selectedLocation = null;
        }

        $filtersActive = (bool) ($filterClusterId || $filterLocationId);

        // Locations used for chart labels (names + ids)
        $activeLocations = Location::query()
            ->when($filterClusterId, fn($q) => $q->where('cluster_id', $filterClusterId))
            ->when($filterLocationId, fn($q) => $q->where('id', $filterLocationId))
            ->orderBy('name')
            ->pluck('name', 'id');

        // Only scope when a filter is active, so records with NULL location_id still count in the unfiltered totals.
        $scopeByLocation = fn($query) => $filtersActive
            ? $query->whereIn('location_id', function ($sub) use ($filterClusterId, $filterLocationId) {
                $sub->from('locations')->select('id')
                    ->when($filterClusterId, fn($q) => $q->where('cluster_id', $filterClusterId))
                    ->when($filterLocationId, fn($q) => $q->where('id', $filterLocationId));
            })
            : $query;

        // For models that reach a location through a relation (asset / employee)
        $scopeByRelation = fn($query, string $relation) => $filtersActive
            ? $query->whereHas($relation, fn($q) => $q->whereIn('location_id', function ($sub) use ($filterClusterId, $filterLocationId) {
                $sub->from('locations')->select('id')
                    ->when($filterClusterId, fn($q) => $q->where('cluster_id', $filterClusterId))
                    ->when($filterLocationId, fn($q) => $q->where('id', $filterLocationId));
            }))
            : $query;

        // ================= ASSET STATS =================
        $assetQuery = $scopeByLocation(Asset::query());

        $totalAssets = (clone $assetQuery)->count();
        $activeAssets = (clone $assetQuery)->where('status', 2)->count();
        $assignedAssets = (clone $assetQuery)->where('status', 3)->count();
        $maintenanceAssets = (clone $assetQuery)->where('status', 4)->count();
        $retiredAssets = (clone $assetQuery)->where('status', 5)->count();

        $totalAssetValue = (float) (clone $assetQuery)->where('status', '<>', 5)->sum('cost'); // excludes retired
        $usableAssets = $activeAssets + $assignedAssets;
        $utilizationRate = $usableAssets > 0 ? round($assignedAssets / $usableAssets * 100, 1) : 0;
        $retiredRate = $totalAssets > 0 ? round($retiredAssets / $totalAssets * 100, 1) : 0;

        // Data quality: marked "assigned" but nobody is assigned
        $assignedNoOwner = (clone $assetQuery)->where('status', 3)->whereNull('assigned_to')->count();

        // ================= EMPLOYEE STATS =================
        $employeeQuery = $scopeByLocation(Employee::query());

        $totalEmployees = (clone $employeeQuery)->count();
        $activeEmployees = (clone $employeeQuery)->where('status', 1)->count();

        $unassignedEmployees = !$filtersActive
            ? Employee::whereNull('location_id')->count()
            : 0;

        $totalClusters = $filterClusterId ? 1 : Cluster::where('is_active', 1)->count();

        $assetsPerEmployee = $activeEmployees > 0 ? round($assignedAssets / $activeEmployees, 2) : 0;

        $since30 = $today->copy()->subDays(30)->toDateString();
        $newHires30 = (clone $employeeQuery)->where('hire_date', '>=', $since30)->count();
        $separations30 = (clone $employeeQuery)->where('termination_date', '>=', $since30)->count();

        $employmentTypeCounts = (clone $employeeQuery)->where('status', 1)
            ->select('employment_type', DB::raw('count(*) as total'))
            ->groupBy('employment_type')
            ->pluck('total', 'employment_type');
        $employmentTypeLabels = $employmentTypeCounts->keys()
            ->map(fn($k) => ($k === null || $k === '') ? 'Not specified' : (string) $k)->values();
        $employmentTypeData = $employmentTypeCounts->values();

        // ================= CHART DATA: ASSETS BY LOCATION =================
        $assetCounts = (clone $assetQuery)
            ->whereNotNull('location_id')
            ->select('location_id', DB::raw('count(*) as total'))
            ->groupBy('location_id')
            ->pluck('total', 'location_id');

        $locationLabels = $activeLocations->values();
        $assetData = $activeLocations->map(fn($name, $id) => (int) ($assetCounts[$id] ?? 0))->values();

        // ================= CHART DATA: EMPLOYEES BY LOCATION =================
        $employeeCounts = (clone $employeeQuery)
            ->whereNotNull('location_id')
            ->select('location_id', DB::raw('count(*) as total'))
            ->groupBy('location_id')
            ->pluck('total', 'location_id');

        $employeeData = $activeLocations->map(fn($name, $id) => (int) ($employeeCounts[$id] ?? 0))->values();

        // ================= CHART DATA: ASSETS BY CLUSTER =================
        $clusterQuery = Cluster::where('is_active', 1);
        if ($filterClusterId) {
            $clusterQuery->where('id', $filterClusterId);
        } elseif ($selectedLocation && $selectedLocation->cluster_id) {
            $clusterQuery->where('id', $selectedLocation->cluster_id);
        }

        $clusters = $clusterQuery->orderBy('name')->pluck('name', 'id');

        $assetClusterCounts = $clusters->isEmpty() ? collect() : Asset::join('locations', 'assets.location_id', '=', 'locations.id')
            ->whereIn('locations.cluster_id', $clusters->keys()->all())
            ->when($filterLocationId, fn($q) => $q->where('assets.location_id', $filterLocationId))
            ->select('locations.cluster_id', DB::raw('count(assets.id) as total'))
            ->groupBy('locations.cluster_id')
            ->pluck('total', 'cluster_id');

        $clusterLabels = $clusters->values();
        $assetClusterData = $clusters->map(fn($name, $id) => (int) ($assetClusterCounts[$id] ?? 0))->values();

        // ================= CATEGORY STATS (count + value, aggregated in SQL) =================
        $categoryRows = (clone $assetQuery)
            ->with('category:id,name')
            ->select('category_id', DB::raw('count(*) as total'), DB::raw('sum(cost) as total_value'))
            ->groupBy('category_id')
            ->get();

        $categories = $categoryRows
            ->groupBy(fn($r) => optional($r->category)->name ?? 'Uncategorized')
            ->map(fn($g) => (int) $g->sum('total'))
            ->sortDesc();

        $categoryValue = $categoryRows
            ->groupBy(fn($r) => optional($r->category)->name ?? 'Uncategorized')
            ->map(fn($g) => (float) $g->sum('total_value'))
            ->sortDesc()
            ->take(6);

        // ================= CONDITION BREAKDOWN =================
        $conditionCounts = (clone $assetQuery)
            ->select('condition', DB::raw('count(*) as total'))
            ->groupBy('condition')
            ->pluck('total', 'condition');
        $conditionLabels = $conditionCounts->keys()
            ->map(fn($k) => ($k === null || $k === '') ? 'Not specified' : (string) $k)->values();
        $conditionData = $conditionCounts->values();

        // ================= TOP LOCATIONS BY ASSET VALUE =================
        // Location names are looked up for just these 5 ids (unaffected by the chart-label list).
        $topRows = (clone $assetQuery)
            ->whereNotNull('location_id')
            ->select('location_id', DB::raw('count(*) as total'), DB::raw('sum(cost) as total_value'))
            ->groupBy('location_id')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get();

        $topNames = Location::whereIn('id', $topRows->pluck('location_id')->all())->pluck('name', 'id');

        $topLocations = $topRows->map(fn($r) => [
            'name' => $topNames->get($r->location_id, 'Unknown'),
            'count' => (int) $r->total,
            'value' => (float) $r->total_value,
        ]);

        // ================= ACQUISITION TREND (last 12 months) =================
        // Month bucketing is done in PHP so no vendor-specific date function is needed.
        $months = collect(range(11, 0))->map(fn($i) => $today->copy()->startOfMonth()->subMonths($i));

        $purchases = (clone $assetQuery)
            ->whereNotNull('purchase_date')
            ->where('purchase_date', '>=', $months->first()->toDateString())
            ->get(['purchase_date', 'cost'])
            ->groupBy(fn($a) => Carbon::parse($a->purchase_date)->format('Y-m'));

        $acquisitionLabels = $months->map(fn($m) => $m->format('M Y'))->values();
        $acquisitionCount = $months->map(fn($m) => $purchases->get($m->format('Y-m'), collect())->count())->values();
        $acquisitionSpend = $months->map(fn($m) => round((float) $purchases->get($m->format('Y-m'), collect())->sum('cost'), 2))->values();

        // ================= ALERTS =================
        $alertDays = (int) (setting::where('key', 'license_expiring_days')->value('value') ?? 30);
        // Exclusive upper bound = "up to and including the last day of the window", valid for date and datetime columns
        $windowEnd = $today->copy()->addDays($alertDays + 1)->toDateString();

        // Whole days from today until $date (negative = already past)
        $daysUntil = fn($date) => (int) $today->diffInDays(Carbon::parse($date)->startOfDay(), false);

        $alerts = collect();

        $ops = [
            'license_expired' => 0,
            'license_expiring' => 0,
            'clearance_overdue' => 0,
            'clearance_due' => 0,
            'maint_overdue' => 0,
            'maint_due' => 0,
            'supplies_out' => 0,
            'supplies_low' => 0,
        ];

        /* LICENSE ALERTS */
        $licenseExpirations = $scopeByRelation(AssetLicense::with('asset'), 'asset')
            ->where('expiration_date', '<', $windowEnd)
            ->get();

        foreach ($licenseExpirations as $license) {
            $daysLeft = $daysUntil($license->expiration_date);
            $absDays = abs($daysLeft);
            $dayLabel = $absDays == 1 ? 'day' : 'days';
            $assetCode = optional($license->asset)->asset_code ?? 'Unknown asset';

            $daysLeft < 0 ? $ops['license_expired']++ : $ops['license_expiring']++;

            $alerts->push([
                'type' => 'License Expiry',
                'message' => $daysLeft < 0
                    ? "{$assetCode} license expired {$absDays} {$dayLabel} ago"
                    : "{$assetCode} license expires in {$daysLeft} {$dayLabel}",
                'date' => Carbon::parse($license->expiration_date),
                'severity' => $daysLeft < 0 ? 4 : ($daysLeft <= 7 ? 3 : 2),
                'icon' => 'bi-credit-card-2-front',
                'url' => route('asset-licenses.index', optional($license->asset)->id ?? 0),
            ]);
        }

        /* CLEARANCE ALERTS */
        $clearanceExpirations = $scopeByRelation(ClearanceHeader::with('employee'), 'employee')
            ->whereIn('status', [0, 1, 3])
            ->where('expected_date', '<', $windowEnd)
            ->get();

        foreach ($clearanceExpirations as $clearance) {
            $daysLeft = $daysUntil($clearance->expected_date);
            $name = optional($clearance->employee)->first_name ?? 'Employee';

            if ($daysLeft < 0) {
                $overdueDays = abs($daysLeft);
                $dayLabel = $overdueDays == 1 ? 'day' : 'days';
                $message = "{$name} clearance overdue by {$overdueDays} {$dayLabel}";
                $ops['clearance_overdue']++;
            } else {
                $dayLabel = $daysLeft == 1 ? 'day' : 'days';
                $message = "{$name} clearance due in {$daysLeft} {$dayLabel}";
                $ops['clearance_due']++;
            }

            $alerts->push([
                'type' => 'Clearance Due',
                'message' => $message,
                'date' => Carbon::parse($clearance->expected_date),
                'severity' => $daysLeft < 0 ? 4 : ($daysLeft <= 3 ? 3 : 2),
                'icon' => 'bi-person-bounding-box',
                'url' => route('clearance.index'),
            ]);
        }

        /* MAINTENANCE ALERTS */
        $maintenanceAlerts = $scopeByRelation(Maintenance::with('asset'), 'asset')
            ->whereIn('status', [1, 2])
            ->where('scheduled_date', '<', $windowEnd)
            ->get();

        foreach ($maintenanceAlerts as $maintenance) {
            $daysLeft = $daysUntil($maintenance->scheduled_date);
            $assetName = optional($maintenance->asset)->name ?? 'Unknown asset';

            if ($daysLeft < 0) {
                $overdueDays = abs($daysLeft);
                $dayLabel = $overdueDays == 1 ? 'day' : 'days';
                $message = "{$assetName} maintenance overdue by {$overdueDays} {$dayLabel}";
                $ops['maint_overdue']++;
            } elseif ($daysLeft == 0) {
                $message = "{$assetName} maintenance is due today";
                $ops['maint_due']++;
            } else {
                $dayLabel = $daysLeft == 1 ? 'day' : 'days';
                $message = "{$assetName} maintenance due in {$daysLeft} {$dayLabel}";
                $ops['maint_due']++;
            }

            $alerts->push([
                'type' => 'Maintenance Due',
                'message' => $message,
                'date' => Carbon::parse($maintenance->scheduled_date),
                'severity' => $daysLeft < 0 ? 4 : ($daysLeft <= 3 ? 3 : 2),
                'icon' => 'bi-tools',
                'url' => route('maintenance.index'),
            ]);
        }

        /* SUPPLIES ALERTS */
        $suppliesAlert = Supplies::where('status', 1)
            ->whereColumn('available_stock', '<=', 'reorder_quantity')
            ->get();

        foreach ($suppliesAlert as $supplies) {
            $isOut = $supplies->available_stock == 0;
            $isOut ? $ops['supplies_out']++ : $ops['supplies_low']++;

            $alerts->push([
                'type' => 'Supplies Alert',
                'message' => $isOut ? $supplies->name . ' is OUT OF STOCK' : $supplies->name . ' is below reorder level',
                'date' => Carbon::parse($supplies->updated_at),
                'severity' => $isOut ? 4 : 3,
                'icon' => 'bi-cart-x',
                'url' => route('supplies.index'),
            ]);
        }

        // Most severe first, then oldest first
        $totalAlerts = $alerts->count();
        $alerts = $alerts->sortBy([['severity', 'desc'], ['date', 'asc']])->take(10)->values();

        // ================= DROPDOWNS =================
        $allClusters = Cluster::where('is_active', 1)->orderBy('name')->get();
        $allLocations = Location::where('status', 1)
            ->when($filterClusterId, fn($q) => $q->where('cluster_id', $filterClusterId))
            ->orderBy('name')
            ->get();

        return view('main', compact(
            'totalAssets',
            'activeAssets',
            'assignedAssets',
            'maintenanceAssets',
            'retiredAssets',
            'totalAssetValue',
            'utilizationRate',
            'retiredRate',
            'assignedNoOwner',
            'totalEmployees',
            'activeEmployees',
            'unassignedEmployees',
            'assetsPerEmployee',
            'newHires30',
            'separations30',
            'employmentTypeLabels',
            'employmentTypeData',
            'totalClusters',
            'locationLabels',
            'assetData',
            'employeeData',
            'clusterLabels',
            'assetClusterData',
            'categories',
            'categoryValue',
            'conditionLabels',
            'conditionData',
            'topLocations',
            'acquisitionLabels',
            'acquisitionCount',
            'acquisitionSpend',
            'ops',
            'alerts',
            'totalAlerts',
            'allClusters',
            'allLocations',
            'filterLocationId',
            'filterClusterId'
        ));
    }
}