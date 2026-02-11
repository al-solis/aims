<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\asset;
use App\Models\clearance_detail;
use App\Models\clearance_header;
use App\Models\employee;
use App\Models\location;

class ClearanceHeaderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $searchloc = $request->input('searchloc');

        $employees = employee::where('status', '1')->get();
        $locations = location::all();

        $totalRequests = clearance_header::count();
        $pendingRequests = clearance_header::where('status', '0')->count();
        $overdueRequests = clearance_header::where('expected_date', '<', now())
            ->where('status', '!=', '2')
            ->count();
        $completedRequests = clearance_header::where('status', '2')->count();

        $query = clearance_header::query();

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_name', 'like', '%' . $search . '%');
            });
        }

        if ($searchloc) {
            $query->whereHas('employee.location', function ($q) use ($searchloc) {
                $q->where('id', $searchloc);
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $clearanceHeaders = $query->orderBy('created_at', 'desc')->paginate(config('app.paginate'));

        return view('clearance.index', compact(
            'clearanceHeaders',
            'totalRequests',
            'pendingRequests',
            'overdueRequests',
            'completedRequests',
            'employees',
            'locations',
            'searchloc',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'remarks' => 'nullable|string',
        ]);

        $employee = employee::findOrFail($request->employee_id);
        $year = now()->year;
        $trxNo = 'CLR-' . $year . '-' . str_pad(clearance_header::count() + 1, 5, '0', STR_PAD_LEFT);
        $clearanceHeader = clearance_header::create([
            'request_number' => $trxNo,
            'employee_id' => $employee->id,
            'type' => $request->type,
            'expected_date' => $request->expected_date,
            'status' => 0, // Set initial status to pending
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        $assignedAssets = asset::where('assigned_to', $employee->id)->get();
        foreach ($assignedAssets as $asset) {
            clearance_detail::create([
                'clearance_header_id' => $clearanceHeader->id,
                'asset_id' => $asset->id,
                'quantity' => 1,
                'returned_quantity' => 0,
                'purchase_cost' => $asset->cost,
                'actual_cost' => $asset->cost,
                'total' => $asset->cost,
                'remarks' => null,
                'status' => 0, // Set initial status to pending
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);
        }

        return redirect()->route('clearance.index')->with('success', 'Clearance request created successfully.');
    }

    public function show($id)
    {
        $clearanceHeader = clearance_header::with('clearance_details.asset')->findOrFail($id);
        return view('clearance.show', compact('clearanceHeader'));
    }

    public function updateDetails(Request $request, $id)
    {
        $detailIds = $request->detail_id;
        $actuals = $request->actual;
        $statuses = $request->status;
        $totals = $request->total;


        if ($detailIds || $actuals || $statuses || $totals) {

            foreach ($detailIds as $index => $detailId) {

                clearance_detail::where('id', $detailId)->update([
                    'actual_cost' => $actuals[$index],
                    'status' => $statuses[$index],
                    'total' => $totals[$index],
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]);
            }
        }

        clearance_header::where('id', $id)->update([
            'status' => 1,
            'type' => $request->type,
            'expected_date' => $request->expected_date,
            'remarks' => $request->remarks,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return redirect(route('clearance.index'))->with('success', 'Clearance details updated successfully.');
    }

    public function markAsComplete($id)
    {
        clearance_header::where('id', $id)->update([
            'status' => 2,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Clearance request marked as complete.']);
    }

    public function print($id)
    {
        $clearance = clearance_header::with([
            'employee.location',
            'clearance_details.asset'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('reports.clearance', compact('clearance'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('Clearance-' . $clearance->request_number . '.pdf');
    }

    public function voidClearance($id)
    {
        clearance_header::where('id', $id)->update([
            'status' => 4,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Clearance request voided successfully.']);
    }
}
