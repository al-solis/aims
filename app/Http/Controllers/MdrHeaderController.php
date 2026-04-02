<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\mdr_header;
use App\Models\mdr_detail;
use App\Models\Employee;
use App\Models\Location;
use Carbon\Carbon;


class MdrHeaderController extends Controller
{
    public function index(Request $request)
    {
        // You can add any necessary logic here, such as fetching data from the database
        $search = $request->input('search');
        $searchlocation = $request->input('searchlocation');
        $status = $request->input('status');

        $totalSetups = mdr_header::count();
        $activeSetups = mdr_header::where('status', 1)->count();
        $totalInactiveSetups = mdr_header::where('status', 0)->count();
        $locations = Location::orderBy('name')->get();
        $employees = Employee::where('status', 1)
            ->orderBy('last_name')->get();

        $createdSetups = mdr_header::get()->pluck('location_id')->toArray();
        
        $destination = Location::where('status', 1)
            ->whereNotIn('id', $createdSetups)
            ->orderBy('name')->get();

        $query = mdr_header::with('location', 'mdrDetails.employee');

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

        return view('setup.mdr.index', compact('totalSetups', 'activeSetups', 'totalInactiveSetups', 'locations', 'employees', 'setups', 'destination'));
    }

    public function create(Request $request, $id = null)
    {
        $locations = Location::where('status', 1)
            ->orderBy('name')->get();

        $employees = Employee::where('status', 1)
            ->orderBy('last_name')->get();

        $mdr = mdr_header::with('location')
            ->where('id', $id)
            ->first();

        return view('setup.mdr.create', compact('locations', 'employees', 'mdr'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'employees' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            $mdrHeader = mdr_header::create([
                'location_id' => $request->location_id,
                'remarks' => $request->remarks ?? null,
                'status' => 1,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);

            foreach ($request->employees as $emp) {
                mdr_detail::create([
                    'mdr_header_id' => $mdrHeader->id,
                    'employee_id' => $emp['employee_id'],
                    'type' => $emp['type'],
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return redirect()->route('mdr.index')
                ->with('success', 'MDR setup created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->withErrors(['error' => 'Error saving MDR setup']);
        }
    }

    public function getEmployeesByLocation(Request $request)
    {
        $locationId = $request->location_id;

        // Get employees already assigned in this location
        $existingEmployeeIds = mdr_detail::whereHas('mdrHeader', function ($q) use ($locationId) {
            $q->where('location_id', $locationId);
        })->pluck('employee_id');

        // Get employees NOT yet assigned
        $employees = Employee::where('status', 1)
            ->whereNotIn('id', $existingEmployeeIds)
            ->orderBy('last_name')
            ->get();

        return response()->json($employees);
    }    

    public function edit(Request $request, mdr_header $mdr)
    {        
        $locations = Location::where('status', 1)
            ->orderBy('name')->get();

        $employees = Employee::where('status', 1)
            ->orderBy('last_name')->get();

        $mdr = mdr_header::with(['location', 'mdrDetails.employee'])
        ->where('id', $mdr->id)
        ->first();

        
        return view('setup.mdr.create', compact('locations', 'employees', 'mdr'));

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
            $mdrHeader = mdr_header::findOrFail($id);

            $mdrHeader->update([
                'remarks' => $request->remarks,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);
            
            mdr_detail::where('mdr_header_id', $mdrHeader->id)->delete();
            
            foreach ($request->employees as $emp) {
                mdr_detail::create([
                    'mdr_header_id' => $mdrHeader->id,
                    'employee_id' => $emp['employee_id'],
                    'type' => $emp['type'],
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return redirect()->route('mdr.index')
                ->with('success', 'MDR updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->withErrors(['error' => 'Error updating MDR: ' . $e->getMessage()]);
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
            $existingDestination = mdr_header::where('location_id', $request->destination_id)->first();
            if ($existingDestination) {
                return response()->json([
                    'error' => 'Destination location already has an MDR setup. Please delete or edit the existing setup first.'
                ], 422);
            }

            DB::beginTransaction();

            $sourceMdr = mdr_header::with('mdrDetails')->where('location_id', $request->source_id)->first();

            if (!$sourceMdr) {
                return response()->json([
                    'error' => 'No setup found for the selected source location.'
                ], 404);
            }

            $newMdr = mdr_header::create([
                'location_id' => $request->destination_id,
                'remarks' => $sourceMdr->remarks,
                'status' => 1,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);

            foreach ($sourceMdr->mdrDetails as $detail) {
                mdr_detail::create([
                    'mdr_header_id' => $newMdr->id,
                    'employee_id' => $detail->employee_id,
                    'type' => $detail->type,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => 'Setup copied successfully from ' . $sourceMdr->location->name . ' to ' . $newMdr->location->name
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
