<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\clearance_routing;
use App\Models\location as Location;


class ClearanceRoutingController extends Controller
{
    public function index()
    {
        $locations = Location::where('status', 1)->orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        return view('setup.clearance-routing.index', compact('locations'));
    }

    public function viewRouting()
    {
        $data = clearance_routing::with('location')
            ->orderBy('order', 'asc')
            ->get();

        return response()->json(['record' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'order' => 'required|integer'
        ]);

        // prevent duplicate
        if (clearance_routing::where('order', $request->order)->exists()) {
            return response()->json(['error' => 'Order already exists']);
        }

        clearance_routing::create([
            'location_id' => $request->location_id,
            'order' => $request->order,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required',
            'order' => 'required|integer'
        ]);

        if (
            clearance_routing::where('order', $request->order)
                ->where('id', '!=', $id)->exists()
        ) {
            return response()->json(['error' => 'Order already exists']);
        }

        $data = clearance_routing::findOrFail($id);
        $data->update([
            'location_id' => $request->location_id,
            'order' => $request->order,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        clearance_routing::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}