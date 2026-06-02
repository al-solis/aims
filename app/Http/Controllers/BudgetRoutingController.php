<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use App\Models\budget_routing;

class BudgetRoutingController extends Controller
{
    public function index()
    {
        $locations = Location::where('status', 1)->orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        return view('setup.budget-routing.index', compact('locations'));
    }

    public function viewRouting()
    {
        $data = budget_routing::with('location')
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
        if (budget_routing::where('order', $request->order)->exists()) {
            return response()->json(['error' => 'Order already exists']);
        }

        budget_routing::create([
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
            budget_routing::where('order', $request->order)
                ->where('id', '!=', $id)->exists()
        ) {
            return response()->json(['error' => 'Order already exists']);
        }

        $data = budget_routing::findOrFail($id);
        $data->update([
            'location_id' => $request->location_id,
            'order' => $request->order,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        budget_routing::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
