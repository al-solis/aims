<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\asset as Asset;
use App\Models\location as Location;
use App\Models\category;
use Carbon\Carbon;
use App\Models\maintenance as Maintenance;
use App\Models\asset_license as AssetLicense;
use App\Models\clearance_header as ClearanceHeader;
use App\Models\employee as Employee;
use App\Models\setting;
use App\Models\supplies as Supplies;
use App\Models\cluster as Cluster;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // ================= FILTERS =================
        $filterClusterId = $request->input('searchcluster') ?: null;
        $filterLocationId = $request->input('searchloc') ?: null;

        // FIX: reconcile the two filters. Previously a location from a different
        // cluster (or a stale location left in the form after changing the cluster)
        // was AND-ed with the cluster filter and produced an empty dashboard.
        $selectedLocation = $filterLocationId ? Location::find($filterLocationId) : null;
        if ($filterLocationId && !$selectedLocation) {
            $filterLocationId = null; // unknown location id
        } elseif (
            $selectedLocation && $filterClusterId
            && (int) $selectedLocation->cluster_id !== (int) $filterClusterId
        ) {
            $filterLocationId = null; // location does not belong to the chosen cluster -> cluster wins
            $selectedLocation = null;
        }

        $filtersActive = (bool) ($filterClusterId || $filterLocationId);

        $locationQuery = Location::query();
        if ($filterClusterId) {
            $locationQuery->where('cluster_id', $filterClusterId);
        }
        if ($filterLocationId) {
            $locationQuery->where('id', $filterLocationId);
        }

        $activeLocations = $locationQuery->pluck('name', 'id');
        $locationIds = $activeLocations->keys();

        // FIX: only scope by location when a filter is active. Before, the "no filter"
        // view still did whereIn(all locations), which silently dropped every asset /
        // employee with a NULL location_id from the totals (while the "Unassigned
        // Employees" card counted them separately).
        $scopeByLocation = function ($query) use ($locationIds, $filtersActive) {
            if (!$filtersActive) {
                return $query;
            }
            return $locationIds->isNotEmpty()
                ? $query->whereIn('location_id', $locationIds)
                : $query->whereRaw('1 = 0');
        };

        // Same idea for models that reach location through a relation (asset / employee)
        $scopeByRelation = function ($query, string $relation) use ($locationIds, $filtersActive) {
            if (!$filtersActive) {
                return $query;
            }
            return $locationIds->isNotEmpty()
                ? $query->whereHas($relation, fn($q) => $q->whereIn('location_id', $locationIds))
                : $query->whereRaw('1 = 0');
        };

        // ================= ASSET STATS =================
        $assetQuery = $scopeByLocation(Asset::query());

        $totalAssets = (clone $assetQuery)->count();
        $activeAssets = (clone $assetQuery)->where('status', 2)->count();
        $assignedAssets = (clone $assetQuery)->where('status', 3)->count();
        $maintenanceAssets = (clone $assetQuery)->where('status', 4)->count();
        $retiredAssets = (clone $assetQuery)->where('status', 5)->count();

        // ---- NEW: financial / utilization KPIs
        $totalAssetValue = (float) (clone $assetQuery)->whereNotIn('status', [5])->sum('cost'); // excludes retired
        $usableAssets = $activeAssets + $assignedAssets;
        $utilizationRate = $usableAssets > 0 ? round($assignedAssets / $usableAssets * 100, 1) : 0;
        $retiredRate = $totalAssets > 0 ? round($retiredAssets / $totalAssets * 100, 1) : 0;

        // Data-quality: marked "assigned" but nobody is actually assigned
        $assignedNoOwner = (clone $assetQuery)->where('status', 3)->whereNull('assigned_to')->count();

        // ================= EMPLOYEE STATS =================
        $employeeQuery = $scopeByLocation(Employee::query());

        $totalEmployees = (clone $employeeQuery)->count();
        $activeEmployees = (clone $employeeQuery)->where('status', 1)->count();

        $unassignedEmployees = !$filtersActive
            ? Employee::whereNull('location_id')->count()
            : 0;

        $totalClusters = $filterClusterId ? 1 : Cluster::where('is_active', 1)->count();

        // ---- NEW: workforce KPIs
        $assetsPerEmployee = $activeEmployees > 0 ? round($assignedAssets / $activeEmployees, 2) : 0;
        $newHires30 = (clone $employeeQuery)->whereNotNull('hire_date')
            ->whereDate('hire_date', '>=', $today->copy()->subDays(30)->toDateString())->count();
        $separations30 = (clone $employeeQuery)->whereNotNull('termination_date')
            ->whereDate('termination_date', '>=', $today->copy()->subDays(30)->toDateString())->count();

        $employmentTypeCounts = (clone $employeeQuery)->where('status', 1)
            ->select('employment_type', DB::raw('count(*) as total'))
            ->groupBy('employment_type')
            ->pluck('total', 'employment_type');
        $employmentTypeLabels = $employmentTypeCounts->keys()
            ->map(fn($k) => ($k === null || $k === '') ? 'Not specified' : (string) $k)->values();
        $employmentTypeData = $employmentTypeCounts->values();

        // ================= CHART DATA: ASSETS BY LOCATION =================
        $assetCounts = $locationIds->isEmpty() ? collect() : (clone $assetQuery)
            ->select('location_id', DB::raw('count(*) as total'))
            ->groupBy('location_id')
            ->pluck('total', 'location_id');

        $locationLabels = $activeLocations->values();
        $assetData = $activeLocations->map(fn($name, $id) => $assetCounts[$id] ?? 0)->values();

        // ================= CHART DATA: EMPLOYEES BY LOCATION =================
        $employeeCounts = $locationIds->isEmpty() ? collect() : (clone $employeeQuery)
            ->select('location_id', DB::raw('count(*) as total'))
            ->whereNotNull('location_id')
            ->groupBy('location_id')
            ->pluck('total', 'location_id');

        $employeeData = $activeLocations->map(fn($name, $id) => $employeeCounts[$id] ?? 0)->values();

        // ================= CHART DATA: ASSETS BY CLUSTER =================
        $clusterQuery = Cluster::where('is_active', 1);
        if ($filterClusterId) {
            $clusterQuery->where('id', $filterClusterId);
        } elseif ($selectedLocation && $selectedLocation->cluster_id) {
            $clusterQuery->where('id', $selectedLocation->cluster_id);
        }

        $clusters = $clusterQuery->pluck('name', 'id');
        $clusterIds = $clusters->keys();

        $assetClusterCounts = $clusterIds->isEmpty() ? collect() : Asset::join('locations', 'assets.location_id', '=', 'locations.id')
            ->select('locations.cluster_id', DB::raw('count(assets.id) as total'))
            ->whereIn('locations.cluster_id', $clusterIds)
            ->when($filterLocationId, fn($q) => $q->where('assets.location_id', $filterLocationId))
            ->groupBy('locations.cluster_id')
            ->pluck('total', 'locations.cluster_id');

        $clusterLabels = $clusters->values();
        $assetClusterData = $clusters->map(fn($name, $id) => $assetClusterCounts[$id] ?? 0)->values();

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

        // NEW: asset value by category (top 6)
        $categoryValue = $categoryRows
            ->groupBy(fn($r) => optional($r->category)->name ?? 'Uncategorized')
            ->map(fn($g) => (float) $g->sum('total_value'))
            ->sortDesc()
            ->take(6);

        // ================= NEW: CONDITION BREAKDOWN =================
        $conditionCounts = (clone $assetQuery)
            ->select('condition', DB::raw('count(*) as total'))
            ->groupBy('condition')
            ->pluck('total', 'condition');
        $conditionLabels = $conditionCounts->keys()
            ->map(fn($k) => ($k === null || $k === '') ? 'Not specified' : (string) $k)->values();
        $conditionData = $conditionCounts->values();

        // ================= NEW: TOP LOCATIONS BY ASSET VALUE =================
        $topLocations = (clone $assetQuery)
            ->whereNotNull('location_id')
            ->select('location_id', DB::raw('count(*) as total'), DB::raw('sum(cost) as total_value'))
            ->groupBy('location_id')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'name' => $activeLocations->get($r->location_id, 'Unknown'),
                'count' => (int) $r->total,
                'value' => (float) $r->total_value,
            ]);

        // ================= NEW: ACQUISITION TREND (last 12 months) =================
        $months = collect(range(11, 0))->map(fn($i) => $today->copy()->startOfMonth()->subMonths($i));

        $purchases = (clone $assetQuery)
            ->whereNotNull('purchase_date')
            ->whereDate('purchase_date', '>=', $months->first()->toDateString())
            ->get(['purchase_date', 'cost'])
            ->groupBy(fn($a) => Carbon::parse($a->purchase_date)->format('Y-m'));

        $acquisitionLabels = $months->map(fn($m) => $m->format('M Y'))->values();
        $acquisitionCount = $months->map(fn($m) => $purchases->get($m->format('Y-m'), collect())->count())->values();
        $acquisitionSpend = $months->map(fn($m) => round((float) $purchases->get($m->format('Y-m'), collect())->sum('cost'), 2))->values();

        // ================= ALERTS =================
        $alertDays = (int) (setting::where('key', 'license_expiring_days')->first()->value ?? 30);
        $windowEnd = $today->copy()->addDays($alertDays);

        $alerts = collect();

        // NEW: operational health counters shown as tiles
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
            ->whereDate('expiration_date', '<=', $windowEnd->toDateString())
            ->get();

        foreach ($licenseExpirations as $license) {
            $daysLeft = (int) $today->diffInDays(Carbon::parse($license->expiration_date), false);
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
            ->whereDate('expected_date', '<=', $windowEnd->toDateString())
            ->get();

        foreach ($clearanceExpirations as $clearance) {
            $daysLeft = (int) $today->diffInDays(Carbon::parse($clearance->expected_date), false);
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
            ->whereDate('scheduled_date', '<=', $windowEnd->toDateString())
            ->get();

        foreach ($maintenanceAlerts as $maintenance) {
            $daysLeft = (int) $today->diffInDays(Carbon::parse($maintenance->scheduled_date), false);
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

        // Most severe first, then oldest first (previously: oldest only, which could hide critical items)
        $totalAlerts = $alerts->count();
        $alerts = $alerts->sortBy([['severity', 'desc'], ['date', 'asc']])->take(10)->values();

        // ================= DROPDOWNS =================
        $allClusters = Cluster::where('is_active', 1)->orderBy('name')->get();
        // Location dropdown follows the selected cluster so mismatched combos can't be chosen
        $allLocations = Location::where('status', 1)
            ->when($filterClusterId, fn($q) => $q->where('cluster_id', $filterClusterId))
            ->orderBy('name')
            ->get();

        // dd([
        //     'searchloc' => $request->input('searchloc'),
        //     'filterLocationId' => $filterLocationId,
        //     'filterClusterId' => $filterClusterId,
        //     'locationIds' => $locationIds,
        //     'assets_by_location_id' => Asset::where('location_id', $filterLocationId)->count(),
        //     'assets_by_subloc_id' => Asset::where('subloc_id', $filterLocationId)->count(),
        //     'employees_by_location' => Employee::where('location_id', $filterLocationId)->count(),
        //     'asset_sql' => (clone $assetQuery)->toSql(),
        //     'asset_bindings' => (clone $assetQuery)->getBindings(),
        //     'totalAssets' => $totalAssets,
        // ]);

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