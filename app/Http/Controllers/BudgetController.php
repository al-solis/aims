<?php
namespace App\Http\Controllers;
use App\Models\budget_header;
use App\Models\budget_detail;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\location;
use App\Models\uom;
use App\Models\numseq;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
class BudgetController extends Controller
{
    public function index()
    {
        $totalRequests = budget_header::count();
        $pendingRequests = budget_header::where('status', 0)->count();
        $overdueRequests = budget_header::where('status', 1)->where('requested_at', '<', now()->subDays(7))->count();
        $completedRequests = budget_header::where('status', 2)->count();
        $locations = location::orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        // $employees = Employee::where('status', 1)->orderByRaw('RTRIM(LTRIM(last_name))) ASC')->get();
        $budgets = budget_header::with('budgetDetails')->paginate(config('app.paginate'));
        return view('budget.index', compact('budgets', 'totalRequests', 'pendingRequests', 'overdueRequests', 'completedRequests', 'locations'));
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
        $locations = location::orderByRaw('LTRIM(RTRIM(name)) ASC')->get();
        $uoms = uom::orderBy('name')->get();
        $budget = null;
        return view('budget.show', compact('locations', 'uoms', 'budget'));
    }

    public function store(Request $request)
    {
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
        $budget = budget_header::findOrFail($id);

        if ($budget->status != 0) {
            return redirect()
                ->route('budget.index')
                ->with('error', 'Only pending budgets can be submitted for approval.');
        }

        $budget->update([
            'status' => 1,
            'submitted_at' => now(),
        ]);

        return response()->json(['message' => 'Budget request submitted for approval.']);
    }

    public function approveRequest(Request $request, $id)
    {
        $budget = budget_header::findOrFail($id);

        if ($budget->status != 1) {
            return redirect()
                ->route('budget.index')
                ->with('error', 'Only budgets pending approval can be approved.');
        }

        $budget->update([
            'status' => 2,
            'approver_id' => Auth::id(),
            'approver_remarks' => $request->remarks,
            'approved_at' => now(),
        ]);

        return response()->json(['message' => 'Budget request approved.']);
    }

    public function rejectRequest(Request $request, $id)
    {
        $budget = budget_header::findOrFail($id);

        if ($budget->status != 1) {
            return redirect()
                ->route('budget.index')
                ->with('error', 'Only budgets pending approval can be rejected.');
        }

        $budget->update([
            'status' => 3,
            'approver_id' => Auth::id(),
            'approver_remarks' => $request->remarks,
            'rejected_at' => now(),
        ]);

        return response()->json(['message' => 'Budget request rejected.']);
    }

    public function voidBudget(Request $request, $id)
    {
        $budget = budget_header::findOrFail($id);

        if (!in_array($budget->status, [0, 1]) || Auth::id() != $budget->requested_by) {
            return redirect()
                ->route('budget.index')
                ->with('error', 'Only pending or submitted budgets can be voided by the requester.');
        }

        $budget->update([
            'status' => 4,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Budget request voided.']);
    }

    public function printBudget($id)
    {
        $budget = budget_header::with('budgetDetails.unit', 'location', 'requester')->findOrFail($id);
        $pdf = Pdf::loadView('reports.budget-request-form', compact('budget'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('Budget-' . $budget->id . '.pdf');
    }
}