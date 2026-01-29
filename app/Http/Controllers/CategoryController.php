<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;



class CategoryController extends Controller
{
    public function index(Request $request)
    {
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
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $categories = $query->paginate(config('app.paginate'));

        return view('setup.category.index', compact('categories', 'totalCategories', 'activeCategories', 'inactiveCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('category.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }
}