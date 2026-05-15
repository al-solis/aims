<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ddo_header;
use App\Models\ddo_detail;
use App\Models\Employee;
use App\Models\Location;
use Carbon\Carbon;


class DdoHeaderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchlocation = $request->input('searchlocation');
        $status = $request->input('status');

        $totalSetups = ddo_header::count();
        $activeSetups = ddo_header::where('status', 1)->count();
        $totalInactiveSetups = ddo_header::where('status', 0)->count();
        $locations = Location::orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        $employees = Employee::where('status', 1)
            ->orderBy('last_name')->get();

        $createdSetups = ddo_header::get()->pluck('location_id')->toArray();

        $destination = Location::where('status', 1)
            ->whereNotIn('id', $createdSetups)
            ->orderBy('name')->get();

        $query = ddo_header::with('location', 'ddoDetails.employee');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('location', function ($subq) use ($search) {
                    $subq->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $setups = $query->orderBy('created_at', 'desc')
            ->paginate(config('app.paginate'))
            ->appends($request->only('search', 'status'));

        return view('setup.ddo.index', compact('totalSetups', 'activeSetups', 'totalInactiveSetups', 'locations', 'employees', 'setups', 'destination'));
    }

    public function create(Request $request, $id = null)
    {
        $locations = DB::table('locations')
            ->where('status', 1)
            ->whereNotIn('id', function ($query) {
                $query->select('location_id')
                    ->from('ddo_headers')
                    ->where('status', 1);
            })
            ->orderBy('name')
            ->get();

        $employees = Employee::where('status', 1)
            ->orderBy('last_name')->get();

        $ddo = ddo_header::with('location')
            ->where('id', $id)
            ->first();

        return view('setup.ddo.create', compact('locations', 'employees', 'ddo'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'location_id_display' => 'required|exists:locations,id',
            'employees' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            $ddoHeader = ddo_header::create([
                'location_id' => $request->location_id_display,
                'remarks' => $request->remarks ?? null,
                'status' => 1,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);

            foreach ($request->employees as $emp) {
                ddo_detail::create([
                    'ddo_header_id' => $ddoHeader->id,
                    'employee_id' => $emp['employee_id'],
                    'type' => $emp['type'],
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return redirect()->route('ddo.index')
                ->with('success', 'DDO setup created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->withErrors(['error' => 'Error saving DDO setup']);
        }
    }

    public function getEmployeesByLocation(Request $request)
    {
        $locationId = $request->location_id;

        $employeeInLocation = Employee::where('status', 1)
            ->where('location_id', $locationId)
            ->get();

        // Get employees already assigned in this location
        $existingEmployeeIds = ddo_detail::whereHas('ddoHeader', function ($q) use ($locationId) {
            $q->where('location_id', $locationId);
        })->pluck('employee_id');

        // Get employees NOT yet assigned
        $employees = Employee::where('status', 1)
            ->whereNotIn('id', $existingEmployeeIds)
            ->orderBy('last_name')
            ->get();

        return response()->json([
            'employees' => $employees,
            'employeeInLocation' => $employeeInLocation
        ]);
    }

    public function edit(Request $request, ddo_header $ddo)
    {
        $locations = Location::where('status', 1)
            ->orderBy('name')->get();

        $employees = Employee::where('status', 1)
            ->orderBy('last_name')->get();

        $ddo = ddo_header::with(['location', 'ddoDetails.employee'])
            ->where('id', $ddo->id)
            ->first();


        return view('setup.ddo.create', compact('locations', 'employees', 'ddo'));

    }

    public function update(Request $request, $id)
    {
        //  dd($request->all());
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'employees' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            // dd($request->all());
            $ddoHeader = ddo_header::findOrFail($id);

            $ddoHeader->update([
                'remarks' => $request->remarks,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            ddo_detail::where('ddo_header_id', $ddoHeader->id)->delete();

            foreach ($request->employees as $emp) {
                ddo_detail::create([
                    'ddo_header_id' => $ddoHeader->id,
                    'employee_id' => $emp['employee_id'],
                    'type' => $emp['type'],
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return redirect()->route('ddo.index')
                ->with('success', 'DDO updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->withErrors(['error' => 'Error updating DDO: ' . $e->getMessage()]);
        }
    }

    public function copyLocationSetup(Request $request)
    {
        try {
            $request->validate([
                'source_id' => 'required|exists:locations,id',
                'destination_id' => 'required|exists:locations,id|different:source_id',
            ]);

            // Check if destination already has a setup
            $existingDestination = ddo_header::where('location_id', $request->destination_id)->first();
            if ($existingDestination) {
                return response()->json([
                    'error' => 'Destination location already has an DDO setup. Please delete or edit the existing setup first.'
                ], 422);
            }

            DB::beginTransaction();

            $sourceDdo = ddo_header::with('ddoDetails')->where('location_id', $request->source_id)->first();

            if (!$sourceDdo) {
                return response()->json([
                    'error' => 'No setup found for the selected source location.'
                ], 404);
            }

            $newDdo = ddo_header::create([
                'location_id' => $request->destination_id,
                'remarks' => $sourceDdo->remarks,
                'status' => 1,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);

            foreach ($sourceDdo->ddoDetails as $detail) {
                ddo_detail::create([
                    'ddo_header_id' => $newDdo->id,
                    'employee_id' => $detail->employee_id,
                    'type' => $detail->type,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => 'Setup copied successfully from ' . $sourceDdo->location->name . ' to ' . $newDdo->location->name
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Copy Setup Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Error copying setup: ' . $e->getMessage()
            ], 500);
        }
    }
}
