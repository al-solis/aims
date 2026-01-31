<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\sublocation;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\asset;
use App\Models\category;
use App\Models\location;


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
        $sublocations = Sublocation::where('location_id', $locationid)->get();

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

        return view('asset.index', compact('assets', 'categories', 'locations', 'sublocations'));
    }
}
