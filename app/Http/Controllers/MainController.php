<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use App\Models\Location;
use App\Models\category;
use Carbon\Carbon;
use App\Models\Maintenance;
use App\Models\asset_license as AssetLicense;
use App\Models\clearance_header as ClearanceHeader;
use App\Models\clearance_detail as ClearanceDetail;
use App\Models\employee;
use App\Models\setting;


class MainController extends Controller
{
    public function index()
    {
        // Get stats counts
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 2)->count();
        $assignedAssets = Asset::where('status', 3)->count();
        $maintenanceAssets = Asset::where('status', 4)->count();
        $retiredAssets = Asset::where('status', 5)->count();

        // Get chart data
        $locations = Location::pluck('name', 'id');
        $assetCounts = Asset::select('location_id', DB::raw('count(*) as total'))
            ->groupBy('location_id')
            ->pluck('total', 'location_id');

        // Prepare chart data
        $locationLabels = $locations->values(); // location names
        $assetData = $locations->map(function ($name, $id) use ($assetCounts) {
            return $assetCounts[$id] ?? 0;
        })->values();

        //asset per category
        $categories = Asset::with('category')->get()
            ->groupBy('category.name')
            ->map(function ($group) {
                return $group->count();
            });

        // $today = Carbon::today();
        // $alertDays = (int) setting::where('key', 'license_expiring_days')->first()->value ?? 30;

        // $licenseExpirations = AssetLicense::with('asset')
        //     ->whereDate('expiration_date', '<=', $today->copy()->addDays($alertDays))
        //     ->get();

        // $clearanceExpirations = ClearanceHeader::with('employee')
        //     ->whereIn('status', [0, 1, 3])
        //     ->whereDate('expected_date', '<=', $today->copy()->addDays($alertDays))
        //     ->get();

        // $maintenanceAlerts = Maintenance::with('asset')
        //     ->whereIn('status', [1, 2])
        //     ->whereDate('scheduled_date', '<=', $today->copy()->addDays($alertDays))
        //     ->get();

        $today = Carbon::today();
        $alertDays = (int) (setting::where('key', 'license_expiring_days')->first()->value ?? 30);

        $alerts = collect();

        /* ================= LICENSE ALERTS ================= */
        $licenseExpirations = AssetLicense::with('asset')
            ->whereDate('expiration_date', '<=', $today->copy()->addDays($alertDays))
            ->get();

        foreach ($licenseExpirations as $license) {

            $daysLeft = $today->diffInDays($license->expiration_date, false);

            $dayLabel = $daysLeft == 1 ? " day" : " days";

            $alerts->push([
                'type' => 'License Expiry',
                'message' => $license->asset->asset_code .
                    ($daysLeft < 0 ? " expired" : " expires in {$daysLeft} {$dayLabel}"),
                'date' => $license->expiration_date,
                'severity' => $daysLeft < 0 ? 4 : ($daysLeft <= 7 ? 3 : 2),
                'icon' => 'bi-credit-card-2-front',
                'color' => $daysLeft < 0 ? 'red' : 'yellow',
                'url' => route('licenses.index', $license->asset->id ?? 0)
            ]);
        }

        /* ================= CLEARANCE ALERTS ================= */
        $clearanceExpirations = ClearanceHeader::with('employee')
            ->whereIn('status', [0, 1, 3])
            ->whereDate('expected_date', '<=', $today->copy()->addDays($alertDays))
            ->get();

        foreach ($clearanceExpirations as $clearance) {

            $daysLeft = $today->diffInDays($clearance->expected_date, false);

            if ($daysLeft < 0) {
                $overdueDays = abs($daysLeft);
                $dayLabel = $overdueDays == 1 ? "day" : "days";
                $message = $clearance->employee->first_name .
                    " clearance overdue by {$overdueDays} {$dayLabel}";
            } else {
                $dayLabel = $daysLeft == 1 ? "day" : "days";
                $message = $clearance->employee->first_name .
                    " clearance due in {$daysLeft} {$dayLabel}";
            }

            $alerts->push([
                'type' => 'Clearance Due',
                'message' => $message,
                'date' => $clearance->expected_date,
                'severity' => $daysLeft <= 3 ? 3 : 2,
                'icon' => 'bi-person-bounding-box',
                'color' => 'blue',
                'url' => route('clearance.index')
            ]);
        }

        /* ================= MAINTENANCE ALERTS ================= */
        $maintenanceAlerts = Maintenance::with('asset')
            ->whereIn('status', [1, 2])
            ->whereDate('scheduled_date', '<=', $today->copy()->addDays($alertDays))
            ->get();

        foreach ($maintenanceAlerts as $maintenance) {

            $daysLeft = $today->diffInDays($maintenance->scheduled_date, false);

            if ($daysLeft < 0) {
                $overdueDays = abs($daysLeft);
                $dayLabel = $overdueDays == 1 ? "day" : "days";
                $message = $clearance->employee->first_name .
                    " clearance overdue by {$overdueDays} {$dayLabel}";
            } else {
                $dayLabel = $daysLeft == 1 ? "day" : "days";
                $message = $clearance->employee->first_name .
                    " clearance due in {$daysLeft} {$dayLabel}";
            }

            $alerts->push([
                'type' => 'Maintenance Due',
                'message' => $message,
                'date' => $maintenance->scheduled_date,
                'severity' => $daysLeft <= 3 ? 3 : 2,
                'icon' => 'bi-tools',
                'color' => 'blue',
                'url' => route('maintenance.index')
            ]);
        }

        /* ================= SORT + LIMIT ================= */
        $alerts = $alerts
            ->sortBy('date')
            ->take(5)
            ->values();


        return view('main', compact(
            'totalAssets',
            'activeAssets',
            'assignedAssets',
            'maintenanceAssets',
            'retiredAssets',
            'locationLabels',
            'assetData',
            'categories',
            'alerts'
        ));
    }
}