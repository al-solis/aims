<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\location as Location;
use App\Models\sublocation as SubLocation;
class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Location::query();
        $totalLocations = Location::count();
        $activeLocations = Location::where('status', 1)->count();
        $totalSubLocations = SubLocation::count();

        if ($status !== null && $status !== '') {
            $search = '';
        }

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('code', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
                $q->orWhere('address', 'like', '%' . $search . '%');
                $q->orWhere('contact_number', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $locations = $query->withCount('sublocations')->paginate(config('app.paginate'));

        return view('setup.location.index', compact('locations', 'totalLocations', 'activeLocations', 'totalSubLocations', 'search', 'status'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:15|unique:locations,code',
            'name' => 'required|string|max:60',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'status' => 'required|integer|in:0,1,2',
        ]);

        Location::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('location.index')->with('success', 'Location created successfully.');
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $request->validate([
            'edit_code' => 'required|string|max:15|unique:locations,code,' . $location->id,
            'edit_name' => 'required|string|max:60',
            'edit_description' => 'nullable|string',
            'edit_address' => 'nullable|string',
            'edit_contact_number' => 'nullable|string|max:20',
            'edit_status' => 'required|integer|in:0,1,2',
        ]);

        $location->update([
            'code' => $request->edit_code,
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'address' => $request->edit_address,
            'contact_number' => $request->edit_contact_number,
            'status' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('location.index')->with('success', 'Location updated successfully.');
    }

    public function getSublocations($locationId)
    {
        $sublocations = SubLocation::where('location_id', $locationId)
            // ->whereNot('status', 0)
            ->get();
        return response()->json($sublocations);
    }
}
