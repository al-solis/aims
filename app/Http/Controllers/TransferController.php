<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\asset;
use App\Models\location;
use App\Models\sublocation;
use App\Models\employee;
use App\Models\transfer;
use App\Models\transfer_detail as TransferDetail;

class TransferController extends Controller
{
    public function show(Request $request, $assetId)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $searchLoc = $request->input('searchloc');

        $totalTransfers = Transfer::count();
        $activeTransfers = Transfer::where('cancelled', 0)->count();
        $cancelledTransfers = Transfer::where('cancelled', 1)->count();

        $locations = Location::orderBy('name')->get();
        $sublocation = sublocation::orderBy('name')->get();
        $employees = Employee::where('status', '!=', '0')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->get();

        $query = Transfer::with('transferDetails');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%');
                $q->orWhere('date', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('cancelled', $status);
        }

        if ($searchLoc) {
            $query->whereHas('location', function (Builder $q) use ($searchLoc) {
                $q->where('name', 'like', '%' . $searchLoc . '%');
            });
        }

        if ($assetId) {
            $query->whereHas('transferDetails', function (Builder $q) use ($assetId) {
                $q->where('asset_id', $assetId);
            });

            $assets = Asset::where('id', $assetId)->first();
        } else {
            $assets = Asset::where('status', '!=', '5')->get();
        }

        // dd($assets);
        $transfers = $query->with(
            [
                'transferDetails',
                'transferDetails.asset',
                'transferDetails.fromEmployee',
                'transferDetails.toEmployee',
                'transferDetails.fromLocation',
                'transferDetails.toLocation',
                'transferDetails.fromSublocation',
                'transferDetails.toSublocation',
            ]
        )
            ->paginate(config('app.paginate'))
            ->appends([
                'search' => $search,
                'status' => $status,
                'searchloc' => $searchLoc,
            ]);

        return view(
            'asset.transfer.show',
            compact('locations', 'employees', 'transfers', 'assetId', 'totalTransfers', 'activeTransfers', 'cancelledTransfers', 'assets')
        );
    }

    public function store(Request $request, Transfer $transfer)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'transfer_date' => 'required|date',
            'from_employee_id' => 'required|exists:employees,id',
            'to_employee_id' => 'required|exists:employees,id',
            'location_id' => 'nullable|exists:locations,id',
            'sublocation_id' => 'nullable|exists:sublocations,id',
            'description' => 'nullable|string|max:255',
        ]);

        $year = Carbon::parse($request->transfer_date)->year;
        $count = Transfer::whereYear('date', $year)->count() + 1;
        $sequence = str_pad($count, 5, '0', STR_PAD_LEFT);
        $trxcode = 'TRF-' . $year . '-' . $sequence;

        $transfer = Transfer::create([
            'code' => $trxcode,

            'date' => $request->transfer_date,

            'description' => $request->description,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        $transferDetails = TransferDetail::create([
            'transfer_id' => $transfer->id,
            'asset_id' => $request->asset_id,
            'from_employee_id' => $request->from_employee_id,
            'from_location_id' => $request->from_location_id ?? null,
            'from_sublocation_id' => $request->from_sublocation_id ?? null,
            'to_employee_id' => $request->to_employee_id,
            'to_location_id' => $request->location_id ?? null,
            'to_subloc_id' => $request->sublocation_id ?? null,
            'created_at' => now(),
        ]);

        DB::table('assets')
            ->where('id', $request->asset_id)
            ->update([
                'assigned_to' => $request->to_employee_id,
                'location_id' => $request->location_id ?? null,
                'subloc_id' => $request->sublocation_id ?? null,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Transfer created successfully.');
    }

    public function countAssetTransfers($assetId)
    {
        $count = Transfer::with('transferDetails')->whereHas('transferDetails', function (Builder $q) use ($assetId) {
            $q->where('asset_id', $assetId);
            $q->where('cancelled', false);
        })->count();
        return response()->json(['count' => $count]);
    }

    public function validateTransferDate(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'transfer_date' => 'required|date',
        ]);

        $assetId = $request->input('asset_id');
        $transferDate = Carbon::parse($request->input('transfer_date'));

        $latestTransfer = Transfer::whereHas('transferDetails', function (Builder $q) use ($assetId) {
            $q->where('asset_id', $assetId);
            $q->where('cancelled', false);
        })->orderBy('date', 'desc')->first();

        if ($latestTransfer && $transferDate->lt(Carbon::parse($latestTransfer->date))) {
            return response()->json(['valid' => false, 'message' => 'Transfer date cannot be earlier than the latest transfer date.']);
        }

        return response()->json(['valid' => true]);
    }

    public function voidTransfer($transferId)
    {
        $transfer = Transfer::findOrFail($transferId);
        $transfer->cancelled = true;
        $transfer->save();

        foreach ($transfer->transferDetails as $detail) {
            DB::table('assets')
                ->where('id', $detail->asset_id)
                ->update([
                    'assigned_to' => $detail->from_employee_id,
                    'location_id' => $detail->from_location_id,
                    'subloc_id' => $detail->from_sublocation_id,
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Transfer has been voided.']);
    }

    public function getLastTransferCode($assetId)
    {
        $lastTransfer = Transfer::whereHas('transferDetails', function (Builder $q) use ($assetId) {
            $q->where('asset_id', $assetId);
            $q->where('cancelled', false);
        })->orderBy('date', 'desc')->first();

        return response()->json(['last_code' => $lastTransfer ? $lastTransfer->code : null]);
    }
}
