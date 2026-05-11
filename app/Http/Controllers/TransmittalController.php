<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\transmittal_header;
use App\Models\transmittal_detail;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Asset;

class TransmittalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchemployee = $request->input('searchemployee');

        $query = transmittal_header::with([
            'transmittedTo',
            'location',
            'details.asset'
        ]);

        $employees = Employee::orderBy('last_name')->get();
        $locations = Location::orderBy('name')->get();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transmittal_number', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            });
        }

        if ($searchemployee != null) {
            $query->where('transmitted_to', $searchemployee);
        }

        $transmittals = $query
            ->latest()
            ->paginate(config('app.paginate'));

        return view('asset.transmittal.index', compact(
            'transmittals',
            'employees',
            'locations'
        ));
    }

    public function create()
    {
        $employees = Employee::where('status', 1)
            ->orderBy('last_name')
            ->get();

        $locations = Location::orderBy('name')->get();

        return view('asset.transmittal.create', compact(
            'employees',
            'locations'
        ));
    }

    public function getAssets($locationId)
    {
        $assets = Asset::where('location_id', $locationId)
            ->whereIn('status', [1, 2, 3]) // Available, Active, Assigned
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'asset_code'
            ]);

        return response()->json($assets);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date' => 'required|date',
            'location_id' => 'required',
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
        ]);

        DB::beginTransaction();

        try {

            $count = transmittal_header::count() + 1;

            $transmittalNumber =
                'TRN-' .
                now()->format('Ymd') .
                '-' .
                str_pad($count, 5, '0', STR_PAD_LEFT);

            $header = transmittal_header::create([
                'transmittal_number' => $transmittalNumber,
                'transmittal_date' => $request->date,
                'transmitted_to' => $request->transmit_to,
                'location_id' => $request->location_id,
                'remarks' => $request->remarks,
                'status' => 1,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $item) {

                transmittal_detail::create([
                    'transmittal_header_id' => $header->id,
                    'asset_id' => $item['asset_id'],
                    'quantity' => 1,
                    'tag' => 'A',
                    'created_by' => Auth::id(),
                ]);

                // OPTIONAL:
                // update asset location/status if needed

                // Asset::where('id', $item['asset_id'])->update([
                //     'location_id' => $request->location_name
                // ]);
            }

            DB::commit();

            return redirect()
                ->route('transmittal.index')
                ->with('success', 'Transmittal saved successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage()
                ]);
        }
    }

    public function voidTransmittal($id)
    {
        $transmittal = transmittal_header::findOrFail($id);

        if ($transmittal->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Transmittal is already voided.'
            ]);
        }

        DB::beginTransaction();

        try {
            $transmittal->update(['status' => 0]); // Mark as voided

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transmittal voided successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function printTransmittal($id)
    {
        $empCode = Auth::user()->employee_code;

        $preparedBy = employee::where('employee_code', $empCode)->first();

        $transmittal = transmittal_header::with([
            'details.asset',
            'transmittedTo',
            'location'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('reports.asset-transmittal', compact('transmittal', 'preparedBy'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('transmittal_' . $transmittal->transmittal_number . '.pdf');
    }
}