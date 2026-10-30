<?php

namespace App\Http\Controllers;

use App\Models\role;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\system_theme;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    private const SET_ROLE = 'SET-15';

    private function authorizeRoleSetup(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::SET_ROLE, $action),
            403,
            'You do not have permission in Role Management to perform this action.'
        );
    }
    public function index(Request $request)
    {
        $this->authorizeRoleSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_ROLE, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_ROLE, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_ROLE, 'delete');

        $roles = role::withCount('users')
            ->when($request->filled('searchname'), fn($q) => $q->where('name', 'like', '%' . $request->searchname . '%'))
            ->orderBy('name')
            ->paginate(config('app.paginate', 15))
            ->withQueryString();

        return view('setup.role.index', compact('roles', 'canCreate', 'canUpdate', 'canDelete'));
    }

    public function create()
    {
        $this->authorizeRoleSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_ROLE, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_ROLE, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_ROLE, 'delete');

        $themes = system_theme::orderBy('name')->get();

        return view('setup.role.form', [
            'targetRole' => new role(),
            'themes' => $themes,
            'canCreate' => $canCreate,
            'canUpdate' => $canUpdate,
            'canDelete' => $canDelete
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeRoleSetup('create');

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
        $this->authorizeRoleSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_ROLE, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_ROLE, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_ROLE, 'delete');

        $themes = system_theme::orderBy('name')->get();

        return view('setup.role.form', [
            'targetRole' => $role,
            'themes' => $themes,
            'canCreate' => $canCreate,
            'canUpdate' => $canUpdate,
            'canDelete' => $canDelete
        ]);
    }

    public function update(Request $request, role $role)
    {
        $this->authorizeRoleSetup('update');

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