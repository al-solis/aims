<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubLocation;

class SublocationController extends Controller
{
    public function index(Request $request)
    {
        $locationId = $request->route('location');
        // Fetch sub-locations based on the location ID
        $sublocations = SubLocation::where('location_id', $locationId)->get();
        $totalSubLocations = $sublocations->count();
        $activeSubLocations = $sublocations->where('status', '1')->count();

        return view('location.sublocation.index', [
            'locationId' => $locationId,
            'sublocations' => $sublocations,
            'totalSubLocations' => $totalSubLocations,
            'activeSubLocations' => $activeSubLocations,
        ]);
    }
}
