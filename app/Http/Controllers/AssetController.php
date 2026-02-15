<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\sublocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\User;
use App\Models\asset;
use App\Models\category;
use App\Models\location;
use App\Models\employee;
use App\Models\transfer;
use App\Models\transfer_detail as TransferDetail;
use App\Models\asset_license as AssetLicense;
use App\Models\duty_order as DutyOrder;
use App\Models\clearance_header as ClearanceHeader;
use App\Models\clearance_detail as ClearanceDetail;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchloc = $request->input('searchloc');
        $searchcat = $request->input('searchcat');
        $searchstat = $request->input('searchstat');

        $locationid = $request->route('location');
        $locations = Location::get();
        $categories = Category::get();
        $sublocations = Sublocation::get();
        $employees = Employee::where('status', '!=', '0')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->orderBy('middle_name', 'asc')
            ->get();

        $query = Asset::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('asset_code', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
                $q->orWhere('serial', 'like', '%' . $search . '%');
                $q->orWhereHas('assigned_user', function ($q) use ($search) {
                    $q->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('middle_name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($searchloc) {
            $query->where('location_id', 'like', '%' . $searchloc . '%');
        }
        if ($searchcat) {
            $query->where('category_id', 'like', '%' . $searchcat . '%');
        }

        if ($searchstat !== null) {
            $query->where('status', $searchstat);
        }

        $assets = $query->with('licenses')->paginate(config('app.paginate'))
            ->appends([
                'search' => $search,
                'searchcat' => $searchcat,
                'searchloc' => $searchloc,
                'searchstat' => $searchstat,
                'selected_assets' => $request->input('selected_assets', '')
            ]);

        return view('asset.index', compact('assets', 'categories', 'locations', 'sublocations', 'employees'));
    }

    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            // 'asset_code' => 'required|string|max:25|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:250',
            'category_id' => 'required|exists:categories,id',
            'cost' => 'nullable|numeric',
            'purchase_date' => 'nullable|date|before_or_equal:' . now()->format(format: 'm/d/Y'),
            'manufacturer' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:150',
            'serial' => 'nullable|string|max:50',
        ]);

        $year = Carbon::parse($request->purchase_date)->year;
        $count = Asset::where('category_id', $request->category_id)
            ->whereYear('purchase_date', $year)
            ->count() + 1;
        $sequence = str_pad($count, 5, '0', STR_PAD_LEFT);
        $category = Category::find($request->category_id);
        $assetcode = "{$category->asset_code}-{$year}-{$sequence}";


        $asset = Asset::create([
            'asset_code' => $assetcode,
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'subcategory' => $request->subcategory,
            'cost' => $request->cost ?? 0,
            'purchase_date' => $request->purchase_date,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'serial' => $request->serial,
            'assigned_to' => $request->assigned_to ?? null,
            'status' => $request->assigned_to ? 3 : 2,
            'location_id' => empty($request->location_id)
                ? null
                : $request->location_id,
            'subloc_id' => empty($request->sublocation_id)
                ? null
                : $request->sublocation_id,
            'warranty' => $request->warranty,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Asset created successfully.');
    }

    public function printAssetLabel(Request $request)
    {
        $assetIds = $request->input('asset_ids', []);

        if (is_string($assetIds)) {
            $assetIds = explode(',', $assetIds);
        }

        $assetIds = array_values(
            array_filter(array_map('intval', (array) $assetIds))
        );

        if (empty($assetIds)) {
            return redirect()->back()->with('error', 'No assets selected for label printing.');
        }

        // dd($assetIds);

        $assets = Asset::with(['category', 'location', 'assigned_user'])
            ->whereIn('id', $assetIds)
            ->get();

        // dd($assets);
        $pdf = Pdf::loadView('asset.asset_labels', compact('assets'))
            ->setPaper([0, 0, 198.5, 70.9], 'portrait');

        return $pdf->stream('asset_labels.pdf');
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:250',
            'edit_category_id' => 'required|exists:categories,id',
            'edit_cost' => 'nullable|numeric',
            'edit_purchase_date' => 'nullable|date|before_or_equal:' . now()->format(format: 'm/d/Y'),
            'edit_manufacturer' => 'nullable|string|max:150',
            'edit_model' => 'nullable|string|max:150',
            'edit_serial' => 'nullable|string|max:50',
        ]);

        $asset->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'category_id' => $request->edit_category_id,
            'subcategory' => $request->edit_subcategory,
            'cost' => $request->edit_cost ?? 0,
            'purchase_date' => $request->edit_purchase_date,
            'manufacturer' => $request->edit_manufacturer,
            'model' => $request->edit_model,
            'serial' => $request->edit_serial,
            'assigned_to' => empty($request->hidden_edit_assigned_to)
                ? $request->edit_assigned_to
                : $request->hidden_edit_assigned_to,
            'status' => empty($request->hidden_edit_assigned_to) && empty($request->edit_assigned_to) ? 2 : 3,
            'location_id' => empty($request->hidden_edit_location_id)
                ? null
                : $request->hidden_edit_location_id,
            'subloc_id' => empty($request->hidden_edit_sublocation_id)
                ? null
                : $request->hidden_edit_sublocation_id,
            'warranty' => $request->edit_warranty,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Asset updated successfully.');
    }

    //asset toggle session
    public function saveSelection(Request $request)
    {
        $selectedAssets = $request->input('selected_assets', '');

        if ($selectedAssets) {
            $assetIds = explode(',', $selectedAssets);
            // Filter out empty values
            $assetIds = array_filter($assetIds, function ($id) {
                return !empty($id);
            });
            // Store in session
            session(['selected_assets' => array_values($assetIds)]);
        } else {
            session()->forget('selected_assets');
        }

        return response()->json(['success' => true]);
    }

    public function clearSelection()
    {
        session()->forget('selected_assets');
        return response()->json(['success' => true]);
    }

    public function printARE(Request $request)
    {
        $id = $request->empId;

        $employee = Employee::with('location')
            ->findOrFail($id);

        $assets = Asset::with('category', 'location')
            ->where('assigned_to', $id)
            ->orderBy('name')
            ->get();

        $pdf = PDF::loadView('reports.are', compact('employee', 'assets'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('are.pdf');
    }

    public function printDutyDetail(Request $request)
    {
        $id = $request->empId;

        $employee = Employee::with('location')
            ->findOrFail($id);

        //generate duty orders for the employee
        $assetList = Asset::with('licenses')
            ->where('assigned_to', $id)
            ->where('category_id', 1)
            ->whereDoesntHave('duty_orders', function ($query) use ($id) {
                $query->where('employee_id', $id);
            })
            ->get();

        $now = Carbon::now();
        $lastOrderNo = DutyOrder::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->lockForUpdate()
            ->max('order_number');

        if ($assetList->isEmpty()) {
            // $lastOrder = DutyOrder::with('asset')
            //     ->where('employee_id', $id)
            //     ->orderBy('created_at', 'desc')
            //     ->first();
            $currentAssets = Asset::where('assigned_to', $id)
                ->where('category_id', 1)
                ->pluck('id');

            $lastDutyOrder = DutyOrder::whereIn('asset_id', $currentAssets)
                ->where('employee_id', $id)
                ->orderBy('created_at', 'desc')
                ->first();

            $newOrderNo = $lastDutyOrder ? $lastDutyOrder->order_number : null;
        } else {
            $parts = explode('-', $lastOrderNo);
            $lastOrderId = (int) end($parts);
            $newOrderNo = $now->format('F Y') . '-' . str_pad(($lastOrderId ?? 0) + 1, 3, '0', STR_PAD_LEFT);
        }

        foreach ($assetList as $asset) {
            $expiryDate = $asset->licenses->min('expiration_date');
            if (!$expiryDate) {
                $expiryDate = null;
            }

            DutyOrder::create([
                'order_number' => $newOrderNo,
                'employee_id' => $id,
                'asset_id' => $asset->id,
                'expiry_date' => $expiryDate ?? null,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);
        }

        $assets = Asset::with('category', 'location', 'licenses', 'duty_orders')
            ->where('assigned_to', $id)
            ->where('category_id', 1)
            ->orderBy('name')
            ->get();

        $pdf = PDF::loadView('reports.dutydetail', compact('employee', 'assets', 'newOrderNo'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('duty_detail.pdf');
    }

    public function retire($id)
    {
        $asset = Asset::findOrFail($id);

        if ($asset->status == 5) {
            return response()->json(['success' => false, 'message' => 'Asset is already retired.']);
        }

        if ($asset->status == 4) {
            return response()->json(['success' => false, 'message' => 'Asset is scheduled for maintenance. Please complete or void the maintenance before retiring this asset.']);
        }

        //Check for active clearance
        $activeClearance = ClearanceDetail::where('asset_id', $id)
            ->whereHas('clearance_header', function ($query) {
                $query->whereIn('status', [0, 1, 3]);
            })
            ->exists();

        if ($activeClearance) {
            return response()->json(['success' => false, 'message' => 'Asset is currently part of an active clearance. Please complete or void the clearance before retiring this asset.']);
        }

        $asset->status = 5; // Set status to Retired
        $asset->updated_by = Auth::id();
        $asset->updated_at = now();
        $asset->save();

        return response()->json(['success' => true]);
    }
}
