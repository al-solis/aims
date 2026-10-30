<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\suppliescategory as SuppliesCategory;

class SuppliesCategoryController extends Controller
{
    private const SET_SUPCAT = 'SET-08';

    private function authorizeSupCatSetup(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::SET_SUPCAT, $action),
            403,
            'You do not have permission in Supplies Category Management to perform this action.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeSupCatSetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_SUPCAT, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_SUPCAT, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_SUPCAT, 'delete');

        $search = $request->input('search');
        $status = $request->input('status');
        $totalCategories = SuppliesCategory::count();
        $activeCategories = SuppliesCategory::where('is_active', 1)->count();
        $inactiveCategories = SuppliesCategory::where('is_active', 0)->count();

        $query = SuppliesCategory::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
                $q->orWhere('supplies_code', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $categories = $query->paginate(config('app.paginate'));

        return view('setup.supplies-category.index', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'inactiveCategories',
            'canCreate',
            'canUpdate',
            'canDelete'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeSupCatSetup('create');

        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
            'supplies_code' => 'required|string|max:10',
        ]);

        SuppliesCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'supplies_code' => $request->supplies_code,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('supplies-category.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeSupCatSetup('update');

        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:150',
            'edit_supplies_code' => 'required|string|max:10',
        ]);

        $category = SuppliesCategory::findOrFail($id);
        $category->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'supplies_code' => $request->edit_supplies_code,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('supplies-category.index')->with('success', 'Category updated successfully.');
    }
}
