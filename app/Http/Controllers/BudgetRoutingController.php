<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\location as Location;
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
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['record' => $data]);
    }

    // New steps always go to the end of the sequence.
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        DB::transaction(function () use ($request) {
            $next = (int) budget_routing::max('order') + 1;

            budget_routing::create([
                'location_id' => $request->location_id,
                'order' => $next,
                'created_by' => Auth::id(),
            ]);
        });

        return response()->json(['success' => true]);
    }

    // Edit only changes the department; position is changed with move().
    public function update(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        budget_routing::findOrFail($id)->update([
            'location_id' => $request->location_id,
        ]);

        return response()->json(['success' => true]);
    }

    // Swap a step with its neighbour (direction: up | down).
    public function move(Request $request, $id)
    {
        $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        DB::transaction(function () use ($request, $id) {
            $this->resequence(); // make sure orders are 1..n with no gaps/duplicates

            $row = budget_routing::findOrFail($id);
            $targetOrder = $request->direction === 'up' ? $row->order - 1 : $row->order + 1;
            $neighbour = budget_routing::where('order', $targetOrder)->first();

            if ($neighbour) {
                $neighbour->update(['order' => $row->order]);
                $row->update(['order' => $targetOrder]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            budget_routing::findOrFail($id)->delete();
            $this->resequence(); // close the gap
        });

        return response()->json(['success' => true]);
    }

    private function resequence(): void
    {
        budget_routing::orderBy('order')->orderBy('id')->get()
            ->each(function ($row, $i) {
                if ((int) $row->order !== $i + 1) {
                    $row->update(['order' => $i + 1]);
                }
            });
    }


    // public function index()
    // {
    //     $locations = Location::where('status', 1)->orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
    //     return view('setup.budget-routing.index', compact('locations'));
    // }

    // public function viewRouting()
    // {
    //     $data = budget_routing::with('location')
    //         ->orderBy('order', 'asc')
    //         ->get();

    //     return response()->json(['record' => $data]);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'location_id' => 'required',
    //         'order' => 'required|integer'
    //     ]);

    //     // prevent duplicate
    //     if (budget_routing::where('order', $request->order)->exists()) {
    //         return response()->json(['error' => 'Order already exists']);
    //     }

    //     budget_routing::create([
    //         'location_id' => $request->location_id,
    //         'order' => $request->order,
    //         'created_by' => Auth::id(),
    //     ]);

    //     return response()->json(['success' => true]);
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'location_id' => 'required',
    //         'order' => 'required|integer'
    //     ]);

    //     if (
    //         budget_routing::where('order', $request->order)
    //             ->where('id', '!=', $id)->exists()
    //     ) {
    //         return response()->json(['error' => 'Order already exists']);
    //     }

    //     $data = budget_routing::findOrFail($id);
    //     $data->update([
    //         'location_id' => $request->location_id,
    //         'order' => $request->order,
    //         'created_by' => Auth::id(),
    //     ]);

    //     return response()->json(['success' => true]);
    // }

    // public function destroy($id)
    // {
    //     budget_routing::findOrFail($id)->delete();
    //     return response()->json(['success' => true]);
    // }
}
