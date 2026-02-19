<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Supplies;
use App\Models\UOM;
use App\Models\SuppliesCategory;
use App\Models\Supplier;

class SuppliesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchcat = $request->input('searchcat');
        $searchsupplier = $request->input('searchsupplier');

        $uoms = UOM::orderBy('name')->get();
        $categories = SuppliesCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        $query = Supplies::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('uom', function ($q2) use ($search) {
                        $q2->where('code', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($searchcat != '' && $searchcat != null) {
            $query->where('category_id', $searchcat);
        }

        if ($searchsupplier != '' && $searchsupplier != null) {
            $query->where('supplier_id', $searchsupplier);
        }

        $supplies = $query->paginate(config('app.paginate'))
            ->appends('search', $search)
            ->appends('searchcat', $searchcat)
            ->appends('searchsupplier', $searchsupplier)
        ;

        return view('supplies.index', compact('supplies', 'uoms', 'categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:supplies_categories,id',
            'uom_id' => 'required|exists:uoms,id',
            'unit_price' => 'required|numeric|min:0',
            'initial_stock' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $prefix = SuppliesCategory::find($validatedData['category_id'])->supplies_code;
        $year = now()->year;
        $lastCount = Supplies::where('category_id', $request->category_id)
            ->whereYear('created_at', $year)->count() + 1;

        Supplies::create([
            'name' => $validatedData['name'],
            'code' => $prefix . '-' . $year . '-' . str_pad($lastCount, 5, '0', STR_PAD_LEFT),
            'description' => $validatedData['description'],
            'supplier_id' => $validatedData['supplier_id'] ?? null,
            'category_id' => $validatedData['category_id'],
            'uom_id' => $validatedData['uom_id'],
            'reorder_quantity' => $request->reorder_quantity ?? 0,
            'unit_price' => $validatedData['unit_price'],
            'allocated_stock' => 0,
            'available_stock' => $validatedData['initial_stock'] ?? 0,
            'total_stock' => $validatedData['initial_stock'] ?? 0,
            'status' => 1,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('supplies.index')->with('success', 'Supply created successfully.');
    }

    public function update(Request $request, $id)
    {
        dd($request->all());
        $supply = Supplies::findOrFail($id);

        $request->validate([
            'edit_name' => 'required|string|max:255',
            'edit_description' => 'nullable|string',
            'edit_category_id' => 'required|exists:supplies_categories,id',
            'edit_uom_id' => 'required|exists:uoms,id',
            'edit_unit_price' => 'required|numeric|min:0',
            'edit_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $supply->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'supplier_id' => $request->edit_supplier_id,
            'category_id' => $request->edit_category_id,
            'uom_id' => $request->edit_uom_id,
            'reorder_quantity' => $request->edit_reorder_quantity ?? 0,
            'unit_price' => $request->edit_unit_price,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        // dd($supply);
        return redirect()->route('supplies.index')->with('success', 'Supply updated successfully.');
    }
}
