<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\category as Category;



class CategoryController extends Controller
{
    private const SET_CATEGORY = 'SET-04';

    private function authorizeCategorySetup(string $action): void
    {
        abort_unless(
            Auth::user()->hasAccess(self::SET_CATEGORY, $action),
            403,
            'You do not have permission in Asset Category Management to perform this action.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeCategorySetup('read');
        $canCreate = Auth::user()->hasAccess(self::SET_CATEGORY, 'create');
        $canUpdate = Auth::user()->hasAccess(self::SET_CATEGORY, 'update');
        $canDelete = Auth::user()->hasAccess(self::SET_CATEGORY, 'delete');

        $search = $request->input('search');
        $status = $request->input('status');
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', 1)->count();
        $inactiveCategories = Category::where('is_active', 0)->count();

        $query = Category::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
                $q->orWhere('asset_code', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $categories = $query->paginate(config('app.paginate'));

        return view('setup.category.index', compact(
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
        $this->authorizeCategorySetup('create');

        $request->validate([
            'name' => 'required|string|max:60',
            'description' => 'nullable|string|max:150',
            'asset_code' => 'required|string|max:10',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'asset_code' => $request->asset_code,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('category.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeCategorySetup('update');

        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:150',
            'edit_asset_code' => 'required|string|max:10',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'asset_code' => $request->edit_asset_code,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }
}