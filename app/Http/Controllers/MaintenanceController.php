<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Maintenance;
use App\Models\asset;


class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $searchType = $request->input('searchType'); // Default to 'code' if not provided

        $scheduledMaintenances = Maintenance::where('status', 1)->count();
        $inprogressMaintenances = Maintenance::where('status', 2)->count();
        $completedThisMonthMaintenances = Maintenance::where('status', 3)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $overdueMaintenances = Maintenance::where('status', 1)
            ->where('scheduled_date', '<', now())
            ->count();

        $assets = asset::whereNotIn('status', [4, 5])
            ->orderBy('name')
            ->get();

        $maintenances = Maintenance::with('asset');

        if ($search) {
            $maintenances->whereHas('asset', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('maintenance_code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('technician', 'like', '%' . $search . '%');
            });
        }
        if ($status) {
            $maintenances->where('priority', $status);
        }

        if ($searchType) {
            $maintenances->where('type', $searchType);
        }

        $maintenances = $maintenances
            ->orderBy('created_at', 'desc')
            ->paginate(config('app.pagination_limit'))
            ->appends(request()->query());

        return view(
            'maintenance.index',
            compact(
                'maintenances',
                'scheduledMaintenances',
                'inprogressMaintenances',
                'completedThisMonthMaintenances',
                'overdueMaintenances',
                'assets'
            )
        );
    }

    public function store(Request $request)
    {
        $year = Carbon::now()->year;
        $count = Maintenance::whereYear('created_at', $year)->count() + 1;
        $maintenanceCode = 'MT-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:1,2,3,4',
            'type' => 'required|in:1,2,3,4,5',
            'description' => 'nullable|string|max:255',
            'technician' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        Maintenance::create([
            'maintenance_code' => $maintenanceCode,
            'asset_id' => $request->input('asset_id'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'scheduled_date' => $request->input('scheduled_date'),
            'priority' => $request->input('priority'),
            'technician' => $request->input('technician'),
            'cost' => $request->input('cost') ?? 0,
            'notes' => $request->input('notes'),
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        Asset::where('id', $request->input('asset_id'))->update(['status' => 4]);

        return redirect()->route('maintenance.index')->with('success', 'Maintenance scheduled successfully.');
    }

    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $request->validate([
            'edit_asset_id' => 'required|exists:assets,id',
            'edit_scheduled_date' => 'required|date|after_or_equal:' . $maintenance->created_at->format('Y-m-d'),
            'edit_priority' => 'required|in:1,2,3,4',
            'edit_type' => 'required|in:1,2,3,4,5',
            'edit_description' => 'nullable|string|max:255',
            'edit_technician' => 'nullable|string|max:255',
            'edit_cost' => 'nullable|numeric|min:0',
            'edit_notes' => 'nullable|string|max:500',
        ]);

        $maintenance->update([
            'asset_id' => $request->input('edit_asset_id'),
            'type' => $request->input('edit_type'),
            'description' => $request->input('edit_description'),
            'scheduled_date' => $request->input('edit_scheduled_date'),
            'priority' => $request->input('edit_priority'),
            'technician' => $request->input('edit_technician'),
            'cost' => $request->input('edit_cost') ?? 0,
            'notes' => $request->input('edit_notes'),
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Maintenance updated successfully.');
    }

    public function markAsInProgress($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => 2,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        Asset::where('id', $maintenance->asset_id)->update(['status' => 4]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance marked as in progress successfully.'
        ]);
    }

    public function markAsComplete($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => 3,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        Asset::where('id', $maintenance->asset_id)->update(['status' => 2]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance marked as complete successfully.'
        ]);
    }

    public function voidMaintenance($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => 4,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        Asset::where('id', $maintenance->asset_id)->update(['status' => 2]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance voided successfully.'
        ]);
    }
}
