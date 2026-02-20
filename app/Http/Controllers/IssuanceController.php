<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Supplies;
use App\Models\UOM;
use App\Models\issuance_header;
use App\Models\issuance_detail;
use App\Models\employee;
use App\Models\Location;

class IssuanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchlocation = $request->input('searchlocation');
        $searchemployee = $request->input('searchemployee');
        $query = issuance_header::with('issuedTo', 'Location');

        $employees = employee::orderBy('last_name')->get();
        $locations = location::orderBy('name')->get();
        $uoms = UOM::orderBy('name')->get();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', '%' . $search . '%')
                    ->orWhere('purpose', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            });
        }

        if ($searchemployee != null) {
            $query->where('issued_to', $searchemployee);
        }

        if ($searchlocation != null) {
            $query->where('location_id', $searchlocation);
        }

        $issuances = $query->orderBy('created_at', 'desc')->paginate(config('app.paginate'))
            ->appends($request->only('search', 'searchemployee', 'searchlocation'))
        ;

        return view('supplies.issuance.index', compact('issuances', 'employees', 'uoms', 'locations'));
    }

    public function create()
    {
        $supplies = Supplies::where('status', 1)
            ->orderBy('name')->get();
        $employees = employee::whereIn('status', [1])
            ->orderBy('last_name')->get();
        $locations = Location::orderBy('name')->get();
        $uoms = UOM::orderBy('name')->get();

        return view('supplies.issuance.create', compact('supplies', 'employees', 'locations', 'uoms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purpose' => 'required|string',
            'issuance_date' => 'required|date',
            'issued_to' => 'required|exists:employees,id',
            'location_id' => 'nullable',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.supplies_id' => 'required|exists:supplies,id',
            'items.*.uom_id' => 'required|exists:uoms,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
        ]);
        // dd($request->all());

        try {
            DB::beginTransaction();

            // Generate transaction number
            $transactionNumber = $this->generateTransactionNumber();

            // Create issuance header
            $issuanceHeader = issuance_header::create([
                'issuance_number' => $transactionNumber,
                'purpose' => $request->purpose,
                'issuance_date' => $request->issuance_date,
                'issued_to' => $request->issued_to,
                'location_id' => $request->location_id,
                'remarks' => $request->remarks,
                'status' => 1, // Set status to posted                 
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // dd($issuanceHeader);
            //Check if items has available stock
            foreach ($request->items as $item) {
                $supply = Supplies::find($item['supplies_id']);
                if ($supply) {
                    if ($supply->available_stock < $item['quantity']) {
                        DB::rollBack();
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Insufficient stock for ' . $supply->name . '. Available: ' . $supply->available_stock]);
                    }
                } else {
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['error' => 'Supply not found.']);
                }
            }

            // Create issuance details
            foreach ($request->items as $item) {
                issuance_detail::create([
                    'issuance_header_id' => $issuanceHeader->id,
                    'supply_id' => $item['supplies_id'],
                    'quantity' => $item['quantity'],
                    'uom_id' => $item['uom_id'],
                    'unit_cost' => $item['unit_price'],
                    'total_cost' => $item['total_price'],
                ]);

                $this->updateInventory($item['supplies_id'], $item['quantity']);
            }

            DB::commit();

            return redirect()->route('issuance.index')
                ->with('success', 'Issuance transaction #' . $transactionNumber . ' has been saved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to save issuance transaction. ' . $e->getMessage()]);
        }
    }

    private function generateTransactionNumber()
    {
        $year = date('Y');
        $month = date('m');

        $lastIssuance = issuance_header::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastIssuance) {
            $lastNumber = intval(substr($lastIssuance->issuance_number, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return 'ISS-' . $year . $month . '-' . $newNumber;
    }

    private function updateInventory($productId, $quantity)
    {
        $supply = Supplies::find($productId);
        if ($supply) {
            $supply->available_stock -= $quantity;
            $supply->total_stock -= $quantity;
            $supply->save();
        }
    }

    public function show($id)
    {
        $issuance = issuance_header::with([
            'details.supply',
            'details.uom',
            'issuedTo',
            'Location'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $issuance
        ]);
    }

    public function void($id)
    {
        $issuance = issuance_header::findOrFail($id);

        if ($issuance->status == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Issuance transaction is already voided.'
            ]);
        }

        try {
            DB::beginTransaction();

            $issuance->status = 0;
            $issuance->save();

            // Reverse inventory updates
            foreach ($issuance->details as $detail) {
                $this->updateInventory($detail->supply_id, -$detail->quantity);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Issuance transaction #' . $issuance->issuance_number . ' has been voided successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to void transaction. ' . $e->getMessage()
            ]);
        }
    }

    public function print($id)
    {
        $issuances = issuance_header::with([
            'details.supply',
            'details.uom',
            'issuedTo'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('reports.issuance', compact('issuances'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('issuance_' . $issuances->issuance_number . '.pdf');
    }
}