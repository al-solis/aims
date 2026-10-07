<?php

namespace App\Http\Controllers;

use App\Models\role;
use Illuminate\Http\Request;
use App\Models\user;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = role::withCount('users')
            ->when($request->filled('searchname'), fn($q) => $q->where('name', 'like', '%' . $request->searchname . '%'))
            ->orderBy('name')
            ->paginate(config('app.paginate', 15))
            ->withQueryString();

        return view('setup.role.index', compact('roles'));
    }

    public function create()
    {
        return view('setup.role.form', [
            'targetRole' => new role(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|in:1,0',
        ]);

        role::create($validated);

        return redirect()->route('setup.role.index')->with('success', 'Role created.');
    }

    public function edit(role $role)
    {
        return view('setup.role.form', [
            'targetRole' => $role,
        ]);
    }

    public function update(Request $request, role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|in:1,0',
        ]);

        $role->update($validated);

        return redirect()->route('setup.role.index')->with('success', 'Role updated.');
    }
}