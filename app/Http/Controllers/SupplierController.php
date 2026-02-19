<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', 1)->count();
        $inactiveSuppliers = Supplier::where('is_active', 0)->count();

        $query = Supplier::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('address', 'like', '%' . $search . '%');
                $q->orWhere('contact_person', 'like', '%' . $search . '%');
                $q->orWhere('email', 'like', '%' . $search . '%');
                $q->orWhere('contact_number', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $suppliers = $query->paginate(config('app.paginate'));

        return view('setup.supplier.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers', 'inactiveSuppliers'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:100',
        ]);

        Supplier::create([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'contact_number' => $request->contact_number,
            'email' => $request->contact_email,
            'address' => $request->address,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_name' => 'required|string|max:100',
            'edit_contact_person' => 'nullable|string|max:100',
            'edit_contact_number' => 'nullable|string|max:20',
            'edit_contact_email' => 'nullable|string|email|max:100',
            'edit_address' => 'nullable|string|max:255',
            'edit_status' => 'required|boolean',
        ]);
        // dd($request->all());
        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'name' => $request->edit_name,
            'contact_person' => $request->edit_contact_person,
            'contact_number' => $request->edit_contact_number,
            'email' => $request->edit_contact_email,
            'address' => $request->edit_address,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
    }
}
