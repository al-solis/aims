<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuppliesCategory;

class SuppliesCategoryController extends Controller
{
    public function index(Request $request)
    {
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

        return view('setup.supplies-category.index', compact('categories', 'totalCategories', 'activeCategories', 'inactiveCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
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
        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:255',
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
