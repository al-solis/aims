<?php

namespace App\Http\Controllers;

use App\Models\role;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\system_theme;

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
        $themes = system_theme::orderBy('name')->get();

        return view('setup.role.form', [
            'targetRole' => new role(),
            'themes' => $themes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'theme_id' => 'nullable',
            'is_active' => 'required|in:1,0',
        ]);

        role::create($validated);

        return redirect()->route('setup.role.index')->with('success', 'Role created.');
    }

    public function edit(role $role)
    {
        $themes = system_theme::orderBy('name')->get();

        return view('setup.role.form', [
            'targetRole' => $role,
            'themes' => $themes,
        ]);
    }

    public function update(Request $request, role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'theme_id' => 'nullable',
            'is_active' => 'required|in:1,0',
        ]);

        $role->update($validated);

        return redirect()->route('setup.role.index')->with('success', 'Role updated.');
    }
}