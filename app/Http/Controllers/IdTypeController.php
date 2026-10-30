<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\id_type as IdType;

class IdTypeController extends Controller
{
    private const SET_ID = 'SET-07';

    private function authorizeIDSetup(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::SET_ID, $action),
            403,
            'You do not have permission in Government ID Management to perform this action.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeIDSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_ID, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_ID, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_ID, 'delete');

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

        return view('setup.idtype.index', compact(
            'idTypes',
            'totalIdTypes',
            'activeIdTypes',
            'inactiveIdTypes',
            'canCreate',
            'canUpdate',
            'canDelete'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeIDSetup('create');

        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
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
        $this->authorizeIDSetup('update');

        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:150',
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
