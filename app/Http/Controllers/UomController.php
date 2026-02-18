<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Uom;

class UomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $totalUoms = Uom::count();
        $activeUoms = Uom::where('is_active', 1)->count();
        $inactiveUoms = Uom::where('is_active', 0)->count();

        $query = Uom::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('code', 'like', '%' . $search . '%');
                $q->orWhere('base_uom', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $uoms = $query->paginate(config('app.paginate'));

        return view('setup.uom.index', compact('uoms', 'totalUoms', 'activeUoms', 'inactiveUoms'));
    }

    public function store(Request $request)
    {

        $baseUom = Uom::pluck('code')->implode(',');
        // dd($baseUom);
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:uoms,code',
            'base_uom' => 'required|string|in:' . $baseUom,
            'conversion_factor' => 'required|numeric|min:0',
        ]);


        Uom::create([
            'name' => $request->name,
            'code' => $request->code,
            'conversion_factor' => $request->conversion_factor,
            'base_uom' => $request->base_uom,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('uom.index')->with('success', 'UOM created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_name' => 'required|string|max:100',
            'edit_code' => 'nullable|string|max:20|unique:uoms,code,' . $id,
            'edit_conversion_factor' => 'required|numeric|min:0',
            'edit_base_uom' => 'required|string|max:10',
        ]);

        $uom = Uom::findOrFail($id);
        $uom->update([
            'name' => $request->edit_name,
            'code' => $request->edit_code,
            'base_uom' => $request->edit_base_uom,
            'conversion_factor' => $request->edit_conversion_factor,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('uom.index')->with('success', 'UOM updated successfully.');
    }
}
