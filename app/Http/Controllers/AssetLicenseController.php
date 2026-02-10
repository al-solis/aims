<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Facades\DB;
use Illuminate\Http\Request;
use App\Models\asset_license;
use App\Models\license_type;
use App\Models\asset;
use App\Models\setting;
use Carbon\Carbon;

class AssetLicenseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $searchType = $request->input('searchType');

        $now = Carbon::now();
        $licenseExpiringDaysSetting = setting::where('key', 'license_expiring_days')->first();
        $licenseExpiringDays = $licenseExpiringDaysSetting ? (int) $licenseExpiringDaysSetting->value : 30;

        $totalLicenses = asset_license::count();
        $activeLicenses = asset_license::active()->count();
        $expiringSoonLicenses = asset_license::expiringSoon()->count();
        $expiredLicenses = asset_license::expired()->count();
        $query = asset_license::query();

        $assets = asset::where('status', '!=', '0')
            ->orderBy('name', 'asc')
            ->get();

        $allLicenseTypes = license_type::orderBy('name', 'asc')->get();

        $licenseTypes = license_type::where('is_active', '!=', '0')
            ->orderBy('name', 'asc')
            ->get();

        if ($search) {
            $query->orWhere('license_number', 'like', '%' . $search . '%')
                ->orWhere('issuing_authority', 'like', '%' . $search . '%')
                ->orWhere('expiration_date', 'like', '%' . $search . '%');
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('asset_code', 'like', '%' . $search . '%');
            });
        }

        if ($searchType) {
            $query->where('license_type_id', $searchType);
        }

        if ($status !== null) {
            $query->status($status);
        }

        $assetLicenses = $query->with('asset', 'license_type')->paginate(config('app.paginate'))
            ->appends([
                'search' => $search,
                'status' => $status,
                'searchType' => $searchType,
            ]);

        return view(
            'licenses.index',
            compact('assetLicenses', 'assets', 'licenseTypes', 'allLicenseTypes', 'totalLicenses', 'activeLicenses', 'expiringSoonLicenses', 'expiredLicenses', 'licenseExpiringDays')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'license_type_id' => 'required|exists:license_types,id',
            'license_number' => 'required|string|max:255',
            'issuing_authority' => 'required|string|max:255',
            'expiration_date' => 'required|date',
        ]);

        asset_license::create([
            'asset_id' => $request->input('asset_id'),
            'license_type_id' => $request->input('license_type_id'),
            'license_number' => $request->input('license_number'),
            'issuing_authority' => $request->input('issuing_authority'),
            'issue_date' => $request->input('issue_date'),
            'expiration_date' => $request->input('expiration_date'),
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return redirect()->route('licenses.index')->with('success', 'License added successfully.');
    }

    public function update(Request $request, $id)
    {


        $request->validate([
            'edit_asset_id' => 'required|exists:assets,id',
            'edit_license_type_id' => 'required|exists:license_types,id',
            'edit_license_number' => 'required|string|max:255',
            'edit_issuing_authority' => 'required|string|max:255',
            'edit_expiration_date' => 'required|date',
        ]);

        $license = asset_license::findOrFail($id);
        $license->update([
            'asset_id' => $request->edit_asset_id,
            'license_type_id' => $request->edit_license_type_id,
            'license_number' => $request->edit_license_number,
            'issuing_authority' => $request->edit_issuing_authority,
            'issue_date' => $request->edit_issue_date,
            'expiration_date' => $request->edit_expiration_date,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        return redirect()->route('licenses.index')->with('success', 'License updated successfully.');
    }
}
