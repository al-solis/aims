<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Supplies;
use App\Models\UOM;
use App\Models\SuppliesCategory;
use App\Models\Supplier;
use App\Models\receiving_header;
use App\Models\receiving_detail;
use App\Models\employee;
class ReceivingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchsupplier = $request->input('searchsupplier');
        $searchemployee = $request->input('searchemployee');
        $query = receiving_header::with('supplier', 'receiver');

        $suppliers = Supplier::orderBy('name')->get();
        $employees = employee::orderBy('last_name')->get();
        $uoms = UOM::orderBy('name')->get();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($searchemployee != null) {
            $query->where('received_by', $searchemployee);
        }

        if ($searchsupplier != null) {
            $query->where('supplier_id', $searchsupplier);
        }

        $receivings = $query->orderBy('created_at', 'desc')->paginate(config('app.paginate'))
            ->appends($request->only('search', 'searchemployee', 'searchsupplier'))
        ;

        return view('supplies.receiving.index', compact('receivings', 'suppliers', 'uoms', 'employees'));
    }

    public function create()
    {
        $supplies = Supplies::where('status', 1)
            ->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', 1)
            ->orderBy('name')->get();
        $uoms = UOM::where('is_active', 1)
            ->orderBy('name')->get();
        $employees = employee::where('status', 1)
            ->orderBy('last_name')->get();

        return view('supplies.receiving.create', compact('supplies', 'suppliers', 'uoms', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'received_date' => 'required|date',
            'reference_no' => 'required|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'employee_id' => 'required|exists:employees,id',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.supplies_id' => 'required|exists:supplies,id',
            'items.*.uom_id' => 'required|exists:uoms,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate transaction number
            $transactionNumber = $this->generateTransactionNumber();

            // Create receiving header
            $receivingHeader = receiving_header::create([
                'transaction_number' => $transactionNumber,
                'description' => $request->description,
                'received_date' => $request->received_date,
                'reference' => $request->reference_no,
                'supplier_id' => $request->supplier_id,
                'received_by' => $request->employee_id,
                'remarks' => $request->remarks,
                'status' => 1, // Set status to posted                
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // dd($receivingHeader);
            // Create receiving details
            foreach ($request->items as $item) {
                receiving_detail::create([
                    'receiving_header_id' => $receivingHeader->id,
                    'product_id' => $item['supplies_id'],
                    'quantity' => $item['quantity'],
                    'uom_id' => $item['uom_id'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // Update inventory (if you have inventory tracking)
                $this->updateInventory($item['supplies_id'], $item['quantity']);
            }

            DB::commit();

            return redirect()->route('receiving.index')
                ->with('success', 'Receiving transaction #' . $transactionNumber . ' has been saved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to save receiving transaction. ' . $e->getMessage()]);
        }
    }

    private function generateTransactionNumber()
    {
        $year = date('Y');
        $month = date('m');

        $lastReceiving = receiving_header::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReceiving) {
            $lastNumber = intval(substr($lastReceiving->transaction_number, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return 'RCP-' . $year . $month . '-' . $newNumber;
    }

    private function updateInventory($productId, $quantity)
    {
        $supply = Supplies::find($productId);
        if ($supply) {
            $supply->available_stock += $quantity;
            $supply->total_stock += $quantity;
            $supply->save();
        }
    }

    public function show($id)
    {
        $receiving = receiving_header::with([
            'details.product',
            'details.uom',
            'supplier',
            'receiver'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $receiving
        ]);
    }

    public function void($id)
    {
        $receiving = receiving_header::findOrFail($id);

        if ($receiving->status == 2) {
            return response()->json([
                'success' => false,
                'message' => 'Receiving transaction is already voided.'
            ]);
        }

        try {
            DB::beginTransaction();

            foreach ($receiving->details as $detail) {
                $supply = Supplies::find($detail->product_id);
                if ($supply) {
                    if ($supply->available_stock < $detail->quantity) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot void receiving transaction because it would result in negative stock for product: ' . $supply->code . ' - ' . $supply->name
                        ]);
                    }
                }
            }

            $receiving->status = 2;
            $receiving->save();

            // Reverse inventory updates
            foreach ($receiving->details as $detail) {
                $this->updateInventory($detail->product_id, -$detail->quantity);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receiving transaction #' . $receiving->transaction_number . ' has been voided successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to void transaction. ' . $e->getMessage()
            ]);
        }
    }
}