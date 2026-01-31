<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\sublocation;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\asset;
use App\Models\category;
use App\Models\location;
use App\Models\employee;
use Illuminate\Support\Facades\Auth;


class AssetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchloc = $request->input('searchloc');
        $searchcat = $request->input('searchcat');

        $locationid = $request->route('location');
        $locations = Location::get();
        $categories = Category::get();
        $sublocations = Sublocation::get();
        $employees = Employee::where('status', '1')->get();

        $query = Asset::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('asset_code', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
                $q->orWhere('serial', 'like', '%' . $search . '%');
            });
        }

        if ($searchloc) {
            $query->where('location_id', 'like', '%' . $searchloc . '%');
        }
        if ($searchcat) {
            $query->where('category_id', 'like', '%' . $searchcat . '%');
        }

        $assets = $query->paginate(config('app.paginate'))
            ->appends([
                'search' => $search,
                'searchcat' => $searchcat,
                'searchloc' => $searchloc
            ]);

        return view('asset.index', compact('assets', 'categories', 'locations', 'sublocations', 'employees'));
    }

    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_code' => 'required|string|max:25|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:250',
            'category_id' => 'nullable',
            'cost' => 'nullable|decimal',
            'purchase_date' => 'nullable|date|after_or_equal:' . now()->format(''),
            'manufacturer' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:150',
            'serial' => 'nullable|string|max:50',
        ]);

        $asset->insert([
            'asset_code' => $request->asset_code,
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'cost' => $request->cost,
            'purchase_date' => $request->purchase_date,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'serial' => $request->serial,
            'assigned_to' => $request->assigned_to,
            'location_id' => $request->location_id,
            'sublocation_id' => $request->sublocation_id,
            'condition' => $request->condition,
            'warranty' => $request->warranty,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Asset created successfully.');
    }
}
