<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use App\Models\Location;
use App\Models\category;


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


        return view('main', compact(
            'totalAssets',
            'activeAssets',
            'assignedAssets',
            'maintenanceAssets',
            'retiredAssets',
            'locationLabels',
            'assetData',
            'categories',
        ));
    }
}