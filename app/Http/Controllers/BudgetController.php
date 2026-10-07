<?php
namespace App\Http\Controllers;
use App\Models\budget_approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\budget_header;
use App\Models\budget_detail;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\location;
use App\Models\uom;
use App\Models\numseq;
use App\Models\budget_routing;

class BudgetController extends Controller
{
    private const BUDGET_CODE = 'BM-01';

    private function authorizeBudget(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::BUDGET_CODE, $action),
            403,
            'You do not have permission in Budget Management to perform this action.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeBudget('read');

        $canCreate = Auth::user()->hasAccess(self::BUDGET_CODE, 'create');
        $canUpdate = Auth::user()->hasAccess(self::BUDGET_CODE, 'update');
        $canDelete = Auth::user()->hasAccess(self::BUDGET_CODE, 'delete');

        $search = $request->input('search');
        $searchLocation = $request->input('searchloc');
        $searchStatus = $request->input('status');

        $totalRequests = budget_header::count();
        $pendingRequests = budget_header::where('status', 0)->count();
        $overdueRequests = budget_header::where('status', 1)->where('requested_at', '<', now()->subDays(7))->count();
        $completedRequests = budget_header::where('status', 2)->count();
        $locations = location::orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        // $employees = Employee::where('status', 1)->orderByRaw('RTRIM(LTRIM(last_name))) ASC')->get();

        $query = budget_header::with('location', 'requester');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('apv_no', 'like', '%' . $search . '%')
                    ->orWhere('purpose', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            });
        }
        if ($searchLocation) {
            $query->where('location_id', $searchLocation);
        }
        if ($searchStatus !== null) {
            if (in_array($searchStatus, ['0', '1', '2', '3', '4'])) {
                $query->where('status', $searchStatus);
            } else {
                $query->whereHas('latestApproval', function ($q) use ($searchStatus) {
                    if ($searchStatus === '5') {
                        $q->where('approved', 1);
                    } elseif ($searchStatus === '6') {
                        $q->where('approved', 0);
                    }
                });
            }
        }

        $budgets = $query->with('budgetDetails')->paginate(config('app.paginate'));

        $userLocation = DB::table('users as u')
            ->leftJoin('employees as e', 'u.employee_code', '=', 'e.employee_code')
            ->select('u.id', 'e.location_id')
            ->where('u.id', Auth::id())
            ->first();

        return view(
            'budget.index',
            compact(
                'budgets',
                'totalRequests',
                'pendingRequests',
                'overdueRequests',
                'completedRequests',
                'locations',
                'userLocation',
                'canCreate',
                'canUpdate',
                'canDelete'
            )
        );
    }

    public function show($id)
    {
        $locations = location::orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        $uoms = uom::orderBy('name')->get();
        $budget = budget_header::with('budgetDetails.unit')->findOrFail($id);
        return view('budget.show', compact('budget', 'locations', 'uoms'));
    }

    public function create(Request $request, $id = null)
    {
        $this->authorizeBudget('create');

        $locations = location::orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        $uoms = uom::orderBy('name')->get();
        $budget = null;
        return view('budget.show', compact('locations', 'uoms', 'budget'));
    }

    public function store(Request $request)
    {
        $this->authorizeBudget('create');

        $request->validate([
            'location_id_display' => 'required|exists:locations,id',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        $numseq = numseq::where('name', 'BUDGET')->first();
        $numseq->increment('current_number');

        $apvNo = $numseq->prefix . str_pad($numseq->current_number, $numseq->number_length, '0', STR_PAD_LEFT);
        $totalAmount = collect($request->items)->sum('total_price');

        $budget = budget_header::create([
            'apv_no' => $apvNo,
            'requested_by' => Auth::id(),
            'location_id' => $request->location_id_display,
            'purpose' => $request->purpose,
            'remarks' => $request->remarks,
            'total_amount' => $totalAmount,
            'status' => 0,
            'requested_at' => now(),
        ]);

        foreach ($request->items as $item) {

            budget_detail::create([
                'budget_header_id' => $budget->id,
                'quantity' => $item['quantity'],
                'item_code' => $item['item_code'],
                'item_description' => $item['item_description'],
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('budget.index')
            ->with('success', 'Budget request created successfully.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeBudget('update');

        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        $budget = budget_header::findOrFail($id);

        $totalAmount = collect($request->items)->sum('total_price');

        $budget->update([
            'location_id' => $request->location_id,
            'purpose' => $request->purpose,
            'remarks' => $request->remarks,
            'total_amount' => $totalAmount,
        ]);

        // delete old details
        budget_detail::where('budget_header_id', $budget->id)->delete();

        // recreate details
        foreach ($request->items as $item) {

            budget_detail::create([
                'budget_header_id' => $budget->id,
                'quantity' => $item['quantity'],
                'item_code' => $item['item_code'],
                'item_description' => $item['item_description'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'unit_id' => $item['unit_id'],
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('budget.index')
            ->with('success', 'Budget request updated successfully.');
    }

    public function submitForApproval(Request $request, $id)
    {
        $this->authorizeBudget('update');

        $approvalLevel = budget_routing::orderBy('order')->first();
        $budget = budget_header::findOrFail($id);

        if ($budget->status != 0) {
            return response()->json(['message' => 'Only pending budgets can be submitted for approval.']);
        }

        if (!$approvalLevel) {
            return response()->json(['message' => 'No approval routing defined. Please contact administrator.']);
        }
        $budget->update([
            'status' => 1, // set status to in-progress
            'approval_level' => $approvalLevel->order,
            'current_approver' => $approvalLevel->location_id,
            'submitted_at' => now(),
        ]);

        return response()->json(['message' => 'Budget request submitted for approval.']);
    }

    public function approveRequest(Request $request, $id)
    {
        $this->authorizeBudget('update');

        $budget = budget_header::findOrFail($id);
        $approvalLevel = budget_routing::where('order', $budget->approval_level)->first();

        if ($budget->status != 1) {
            return response()->json(['message' => 'Only budgets pending approval can be approved.']);
        }

        if ($approvalLevel) {
            budget_approval::create([
                'budget_id' => $budget->id,
                'location_id' => $approvalLevel->location_id,
                'approver_id' => Auth::id(),
                'approved' => 1,
                'remarks' => $request->remarks,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $nextLevel = budget_routing::where('order', $approvalLevel->order + 1)->first();

            if ($nextLevel) {
                // Move to next approval level
                $budget->update([
                    'approval_level' => $nextLevel->order,
                    'current_approver' => $nextLevel->location_id,
                ]);

                return response()->json(['message' => 'Budget request approved and moved to next approver.']);
            } else {
                // Final approval
                $budget->update([
                    'status' => 2, // completed
                    'current_approver' => 0,
                    'approval_status' => 1,
                    'approved_at' => now(),
                ]);

                return response()->json(['message' => 'Budget request approved.']);
            }
        } else {
            return response()->json(['message' => 'No approval routing defined. Please contact administrator.']);
        }
    }

    public function rejectRequest(Request $request, $id)
    {
        $this->authorizeBudget('update');
        $budget = budget_header::findOrFail($id);
        $approvalLevel = budget_routing::where('order', $budget->approval_level)->first();

        if ($budget->status != 1) {
            return response()->json(['message' => 'Only budgets pending approval can be rejected.']);
        }

        budget_approval::create([
            'budget_id' => $budget->id,
            'location_id' => $approvalLevel->location_id,
            'approver_id' => Auth::id(),
            'approved' => 0,
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $budget->update([
            'status' => 2, // completed
            'approval_level' => 0,
            'current_approver' => 0,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Budget request rejected.']);
    }

    public function voidBudget(Request $request, $id)
    {
        $this->authorizeBudget('update');

        $budget = budget_header::findOrFail($id);

        if (!in_array($budget->status, [0, 1]) || Auth::id() != $budget->requested_by) {
            return response()->json(['message' => 'Only pending or submitted budgets can be voided by the requester.']);
        }

        $budget->update([
            'status' => 4,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Budget request voided.']);
    }

    public function printBudget($id)
    {
        $budget = budget_header::with('budgetDetails.unit', 'location', 'requester', 'latestApproval')->findOrFail($id);
        $pdf = Pdf::loadView('reports.budget-request-form', compact('budget'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('Budget-' . $budget->id . '.pdf');
    }

    public function getApprovalHistory($id)
    {
        $budget = budget_header::findOrFail($id);

        $history = $budget->approvalHistory()
            ->with('approver')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($item) {

                $actions = [
                    1 => 'Approved',
                    0 => 'Rejected',
                ];

                return [
                    'location' => $item->location->name ?? '',
                    'approver' => $item->approver->lname . ', ' . $item->approver->fname,
                    'action' => $actions[$item->approved] ?? 'Unknown',
                    'date' => $item->created_at->format('Y-m-d h:i A'),
                    'remarks' => $item->remarks,
                ];
            });

        return response()->json([
            'history' => $history
        ]);
    }
}