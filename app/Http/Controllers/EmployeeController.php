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
}
