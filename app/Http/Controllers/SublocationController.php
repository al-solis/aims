<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubLocation;
use App\Models\Location;

class SublocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $active = $request->query('active');

        $locationId = $request->route('location');
        $location = Location::findOrFail($locationId);
        // Fetch sub-locations based on the location ID
        $sublocations = SubLocation::where('location_id', $locationId)->get();
        $totalSubLocations = $sublocations->count();
        $activeSubLocations = $sublocations->where('status', '1')->count();

        $query = SubLocation::query();

        if ($active !== null && $active !== '') {
            $search = '';
        }

        if ($search) {
            $query->where('location_id', $locationId)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
        } else {
            $query->where('location_id', $locationId);
        }

        if ($active !== null && $active !== '') {
            $query->where('status', $active);
        }

        $sublocations = $query->paginate(config('app.paginate'));

        return view('setup.location.sublocation.index', compact('sublocations', 'location', 'locationId', 'totalSubLocations', 'activeSubLocations', 'search', 'active'));
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

    public function update(Request $request, $id)
    {
        $sublocation = SubLocation::findOrFail($id);

        $request->validate([
            'edit_code' => 'required|string|max:15|unique:sublocations,code,' . $sublocation->id,
            'edit_name' => 'required|string|max:60',
            'edit_description' => 'nullable|string',
            'edit_status' => 'required|integer|in:0,1,2',
        ]);

        $sublocation->update([
            'code' => $request->edit_code,
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'status' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('location.sublocation.index', ['location' => $sublocation->location_id])->with('success', 'Sub-Location updated successfully.');
    }
}
