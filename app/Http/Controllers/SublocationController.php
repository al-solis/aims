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

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'code' => 'required|string|max:15|unique:sublocations,code',
            'name' => 'required|string|max:60',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1,2',
        ]);

        SubLocation::create([
            'location_id' => $request->location_id,
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('location.sublocation.index', ['location' => $request->location_id])->with('success', 'Sub-Location created successfully.');
    }
}
