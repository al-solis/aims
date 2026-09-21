<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\cluster;
use Illuminate\Support\Facades\Auth;

class ClusterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $totalClusters = cluster::count();
        $activeClusters = cluster::where('is_active', 1)->count();
        $inactiveClusters = cluster::where('is_active', 0)->count();

        $query = cluster::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null) {
            $query = $query->where('is_active', $status);
        }

        $clusters = $query->paginate(config('app.paginate'));

        return view('setup.cluster.index', compact('clusters', 'totalClusters', 'activeClusters', 'inactiveClusters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        cluster::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('cluster.index')->with('success', 'Cluster created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_name' => 'required|string|max:50',
            'edit_description' => 'nullable|string|max:255',
        ]);

        $docType = cluster::findOrFail($id);
        $docType->update([
            'name' => $request->edit_name,
            'description' => $request->edit_description,
            'is_active' => $request->edit_status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('cluster.index')->with('success', 'Cluster updated successfully.');
    }
}
