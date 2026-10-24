<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\location as Location;
use App\Models\sublocation as SubLocation;
use App\Models\cluster;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $cluster = $request->query('cluster');

        $clusters = cluster::where('is_active', 1)->get();

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

        if ($cluster !== null && $cluster !== '') {
            $query->where('cluster_id', $cluster);
        }

        // Pins for the overview map (respects the current filters, ignores pagination)
        $mapLocations = (clone $query)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'code', 'name', 'address', 'latitude', 'longitude', 'employee_count', 'status')
            ->withCount('employees')
            ->get();

        $locations = $query->orderBy('name')
            ->withCount(['sublocations', 'employees'])
            ->paginate(config('app.paginate'));

        return view('setup.location.index', compact(
            'locations',
            'clusters',
            'totalLocations',
            'activeLocations',
            'totalSubLocations',
            'search',
            'status',
            'mapLocations'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cluster' => 'required|exists:clusters,id',
            'code' => 'required|string|max:15|unique:locations,code',
            'name' => 'required|string|max:60',
            'description' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'employee_count' => 'nullable|integer|min:0',
            'status' => 'required|integer|in:0,1,2',
        ]);

        Location::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'employee_count' => $request->employee_count ?? 0,
            'status' => $request->status,
            'cluster_id' => $request->cluster,
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
            'edit_description' => 'nullable|string|max:150',
            'edit_address' => 'nullable|string|max:255',
            'edit_contact_number' => 'nullable|string|max:20',
            'edit_latitude' => 'nullable|numeric|between:-90,90|required_with:edit_longitude',
            'edit_longitude' => 'nullable|numeric|between:-180,180|required_with:edit_latitude',
            'edit_employee_count' => 'nullable|integer|min:0',
            'edit_cluster' => 'required|exists:clusters,id',
            'edit_status' => 'required|integer|in:0,1,2',
        ]);

        $location->update([
            'cluster_id' => $request->edit_cluster,
            'code' => $request->edit_code,
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'address' => $request->edit_address,
            'contact_number' => $request->edit_contact_number,
            'latitude' => $request->edit_latitude,
            'longitude' => $request->edit_longitude,
            'employee_count' => $request->edit_employee_count ?? 0,
            'status' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('location.index')->with('success', 'Location updated successfully.');
    }

    public function getSublocations($locationId)
    {
        $sublocations = SubLocation::where('location_id', $locationId)->get();
        return response()->json($sublocations);
    }
}