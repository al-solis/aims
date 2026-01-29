<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\id_type as IdType;

class IdTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $totalIdTypes = IdType::count();
        $activeIdTypes = IdType::where('is_active', 1)->count();
        $inactiveIdTypes = IdType::where('is_active', 0)->count();

        $query = IdType::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $idTypes = $query->paginate(config('app.paginate'));

        return view('setup.idtype.index', compact('idTypes', 'totalIdTypes', 'activeIdTypes', 'inactiveIdTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        IdType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('idtype.index')->with('success', 'ID Type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:255',
        ]);

        $idType = IdType::findOrFail($id);
        $idType->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('idtype.index')->with('success', 'ID Type updated successfully.');
    }
}
