<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\license_type as LicenseType;

class LicenseTypeController extends Controller
{
    private const SET_LICENSE = 'SET-05';

    private function authorizeLicenseSetup(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::SET_LICENSE, $action),
            403,
            'You do not have permission in License Type Management to perform this action.'
        );
    }
    public function index(Request $request)
    {
        $this->authorizeLicenseSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_LICENSE, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_LICENSE, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_LICENSE, 'delete');

        $search = $request->input('search');
        $status = $request->input('status');
        $totalLicenseTypes = LicenseType::count();
        $activeLicenseTypes = LicenseType::where('is_active', 1)->count();
        $inactiveLicenseTypes = LicenseType::where('is_active', 0)->count();

        $query = LicenseType::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $licenseTypes = $query->paginate(config('app.paginate'));

        return view('setup.license.index', compact(
            'licenseTypes',
            'totalLicenseTypes',
            'activeLicenseTypes',
            'inactiveLicenseTypes',
            'canCreate',
            'canUpdate',
            'canDelete'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeLicenseSetup('create');

        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
        ]);

        LicenseType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('license.index')->with('success', 'License Type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeLicenseSetup('update');

        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:150',
        ]);

        $licenseType = LicenseType::findOrFail($id);
        $licenseType->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('license.index')->with('success', 'License Type updated successfully.');
    }
}
