<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\uom as UOM;

class UomController extends Controller
{
    private const SET_UOM = 'SET-09';

    private function authorizeUOMSetup(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::SET_UOM, $action),
            403,
            'You do not have permission in Supplies Unit of Measure Management to perform this action.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeUOMSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_UOM, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_UOM, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_UOM, 'delete');

        $search = $request->input('search');
        $status = $request->input('status');
        $totalUoms = UOM::count();
        $activeUoms = UOM::where('is_active', 1)->count();
        $inactiveUoms = UOM::where('is_active', 0)->count();

        $query = UOM::query();

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

        return view('setup.uom.index', compact(
            'uoms',
            'totalUoms',
            'activeUoms',
            'inactiveUoms',
            'canCreate',
            'canUpdate',
            'canDelete'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeUOMSetup('create');

        $baseUom = UOM::pluck('code')->implode(',');
        // dd($baseUom);
        $request->validate([
            'name' => 'required|string|max:20',
            'code' => 'nullable|string|max:10|unique:uoms,code',
            'base_uom' => 'required|string|in:' . $baseUom,
            'conversion_factor' => 'required|numeric|min:0',
        ]);


        UOM::create([
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
        $this->authorizeUOMSetup('update');

        $request->validate([
            'edit_name' => 'required|string|max:20',
            'edit_code' => 'nullable|string|max:10|unique:uoms,code,' . $id,
            'edit_conversion_factor' => 'required|numeric|min:0',
            'edit_base_uom' => 'required|string|max:10',
        ]);

        $uom = UOM::findOrFail($id);
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
