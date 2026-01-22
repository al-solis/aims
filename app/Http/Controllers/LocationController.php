<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\SubLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('sublocations')->get();

        return view('location.index', [
            'locations' => $locations,
            'totalLocations' => Location::count(),
            'activeLocations' => Location::where('is_active', '1')->count(),
            'totalSubLocations' => SubLocation::count(),
        ]);
    }

    public function create()
    {
        return view('location.create');
    }
}
