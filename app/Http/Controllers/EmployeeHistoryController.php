<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\employee;
use App\Models\EmployeeHistory;
;


class EmployeeHistoryController extends Controller
{
    public function viewHistory($employeeId)
    {

        $history = EmployeeHistory::where('employee_id', $employeeId)->get();
        return response()->json([
            'record' => $history
        ]);
    }

    public function show($id)
    {
        $history = EmployeeHistory::where('id', $id)->firstOrFail();

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }


    public function update(Request $request, $id)
    {
        $history = EmployeeHistory::findOrFail($id);
        $history->update($request->all());

        return response()->json($history);
    }

    public function destroy($id)
    {
        EmployeeHistory::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function getByEmployee($employeeId)
    {
        return EmployeeHistory::where('employee_id', $employeeId)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'company_name' => 'required|string|max:255',
            'position_held' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $history = EmployeeHistory::create([
            'employee_id' => $request->employee_id,
            'company' => $request->company_name,
            'position' => $request->position_held,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json($history);
    }

}
