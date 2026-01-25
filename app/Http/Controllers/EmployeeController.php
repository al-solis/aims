<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Location;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Employee::query();
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 1)->count();
        $onleaveEmployees = Employee::where('status', 2)->count();
        $inactiveEmployees = Employee::where('status', 0)->count();

        if ($status !== null && $status !== '') {
            $search = '';
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->orWhere('code', 'like', '%' . $search . '%');
                $q->orWhere('position', 'like', '%' . $search . '%');
                $q->orWhereHas('location', function ($locQuery) use ($search) {
                    $locQuery->where('name', 'like', '%' . $search . '%');
                });
                $q->orWhere('email', 'like', '%' . $search . '%');
                $q->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $employees = $query->with('location')->paginate(config('app.paginate'));

        return view('employee.index', compact('employees', 'totalEmployees', 'activeEmployees', 'onleaveEmployees', 'inactiveEmployees', 'search', 'status'));
    }

    public function create()
    {
        $locations = Location::where('status', 1)->get();
        return view('employee.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:15|unique:employees,code',
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:15',
            'status' => 'required|integer|in:0,1,2',
        ]);

        Employee::create([
            'code' => $request->code,
            'name' => $request->name,
            'position' => $request->position,
            'location_id' => $request->location_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            return response()->json(['path' => $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
