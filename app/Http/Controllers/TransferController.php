<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\asset;
use App\Models\location;
use App\Models\sublocation;
use App\Models\employee;
use App\Models\transfer;

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
        $employees = Employee::orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->get();

        $query = Transfer::query();

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
            $query->where('asset_id', $assetId);
        }

        $transfers = $query->with(['asset', 'location', 'sublocation'])->paginate(config('app.paginate'));

        return view(
            'asset.transfer.show',
            compact('locations', 'status', 'search', 'searchLoc', 'employees', 'transfers', 'assetId', 'totalTransfers', 'activeTransfers', 'cancelledTransfers')
        );
    }
}
