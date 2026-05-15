<?php

namespace App\Http\Controllers;

use App\Models\clearance_routing;
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
use App\Models\user;
use App\Models\clearance_approval;

class ClearanceHeaderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $searchloc = $request->input('searchloc');

        $employees = employee::where('status', '1')->get();
        $locations = Location::orderByRaw('LTRIM(RTRIM(name)) ASC')->get();

        $userLocation = DB::table('users as u')
            ->leftJoin('employees as e', 'u.employee_code', '=', 'e.employee_code')
            ->select('u.id', 'e.location_id')
            ->where('u.id', Auth::id())
            ->first();

        $totalRequests = clearance_header::count();
        $pendingRequests = clearance_header::where('status', '0')->count();
        $overdueRequests = clearance_header::where('expected_date', '<', now())
            ->where('status', '!=', '2')
            ->count();
        $completedRequests = clearance_header::where('status', '2')->count();
        // $lastApprovalStatus = clearance_approval::where('approver_id', Auth::id())
        //     ->orderBy('created_at', 'desc')
        //     ->first();

        $query = clearance_header::with('clearance_approver', 'approvalHistory');

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
            'userLocation'
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
        $userLocation = DB::table('users as u')
            ->leftJoin('employees as e', 'u.employee_code', '=', 'e.employee_code')
            ->select('u.id', 'e.location_id')
            ->where('u.id', Auth::id())
            ->first();

        $clearanceHeader = clearance_header::with('clearance_details.asset')->findOrFail($id);
        $approvalHistory = clearance_approval::with('approver', 'location')
            ->where('clearance_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('clearance.show', compact('clearanceHeader', 'userLocation', 'approvalHistory'));
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

    public function submitForApproval($id)
    {
        $approvalLevel = clearance_routing::orderBy('order')->first();
        clearance_header::where('id', $id)->update([
            'status' => 1, // Set status to in-progress
            'approval_level' => $approvalLevel ? $approvalLevel->order : 1, // Set to first approval level or default to 1
            'current_approver' => $approvalLevel ? $approvalLevel->location_id : '', // Set to first approver's department or default to 0
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Clearance request submitted for approval.']);
    }

    public function approveClearance(Request $request, $id)
    {
        $clearance = clearance_header::findOrFail($id);
        $approvalLevel = clearance_routing::where('order', $clearance->approval_level)->first();

        if ($approvalLevel) {
            clearance_approval::create([
                'clearance_id' => $id,
                'approver_id' => Auth::id(),
                'location_id' => $approvalLevel->location_id,
                'remarks' => $request->remarks,
                'approved' => 1, // Approved
                'created_at' => now(),
            ]);

            // Move to next approval level
            $nextApprovalLevel = clearance_routing::where('order', $clearance->approval_level + 1)->first();
            if ($nextApprovalLevel) {
                clearance_header::where('id', $id)->update([
                    'approval_level' => $nextApprovalLevel->order,
                    'current_approver' => $nextApprovalLevel->location_id,
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]);
            } else {
                // No more approval levels, mark as completed
                clearance_header::where('id', $id)->update([
                    'status' => 2, // Completed
                    'current_approver' => 0, // No more approver
                    'approval_status' => 1, // Approved
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['message' => 'Clearance request approved successfully.']);
        }

        return response()->json(['message' => 'Invalid approval level.'], 400);
    }

    public function rejectClearance(Request $request, $id)
    {
        $clearance = clearance_header::findOrFail($id);
        $approvalLevel = clearance_routing::where('order', $clearance->approval_level)->first();

        clearance_approval::create([
            'clearance_id' => $id,
            'approver_id' => Auth::id(),
            'location_id' => $approvalLevel ? $approvalLevel->location_id : '',
            'remarks' => $request->remarks,
            'created_at' => now(),
            'approved' => 0, // Rejected
        ]);

        clearance_header::where('id', $id)->update([
            'status' => 2, // Completed
            'approval_level' => 0, // Reset approval level
            'current_approver' => 0, // No current approver
            'approval_status' => 2, // Rejected
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Clearance request rejected successfully.']);
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
