<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Odometer;
use App\Models\Asset;
use App\Models\Employee;

class OdometerController extends Controller
{
    public function show($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        $employees = Employee::where('status', '!=', '0')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->get();

        $odometerReadings = Odometer::where('asset_id', $assetId)
            ->orderBy('date', 'desc')
            ->paginate(config('app.paginate', 10));

        return view('asset.odometer.show', compact('asset', 'odometerReadings', 'employees'));
    }

    public function getOdometerReadings($assetId)
    {
        $odometerReadings = Odometer::with('employee:id,first_name,middle_name,last_name')
            ->where('asset_id', $assetId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'odometer' => $odometerReadings
        ]);
    }

    public function getReading($id)
    {
        $odometer = Odometer::with('employee:id,first_name,middle_name,last_name')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'odometer' => $odometer
        ]);
    }

    public function store(Request $request)
    {
        if ($request->reading_to < $request->reading_from) {
            return response()->json([
                'errors' => [
                    'reading_to' => ['Reading To cannot be lower than Reading From.']
                ]
            ], 422);
        }

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'reading_from' => 'required|numeric',
            'reading_to' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        $odometer = Odometer::create([
            'asset_id' => $request->asset_id,
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'from_reading' => $request->reading_from,
            'to_reading' => $request->reading_to,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        $odometer->load('employee:id,first_name,middle_name,last_name');

        return response()->json([
            'message' => 'Odometer reading added successfully',
            'odometer' => $odometer
        ]);
    }

    public function update(Request $request, $id)
    {
        $odometer = Odometer::findOrFail($id);

        if ($request->reading_to < $request->reading_from) {
            return response()->json([
                'errors' => [
                    'reading_to' => ['Reading To cannot be lower than Reading From.']
                ]
            ], 422);
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'reading_from' => 'required|numeric',
            'reading_to' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        $odometer->update([
            'date' => $request->date,
            'employee_id' => $request->employee_id,
            'from_reading' => $request->reading_from,
            'to_reading' => $request->reading_to,
            'remarks' => $request->remarks,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        $odometer->load('employee:id,first_name,middle_name,last_name');

        return response()->json(
            ['odometer' => $odometer]
        );
    }

    public function destroy($id)
    {
        Odometer::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
