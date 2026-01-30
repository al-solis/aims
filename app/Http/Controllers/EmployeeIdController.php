<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\employee_id as EmployeeId;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class EmployeeIdController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'employee_id_type' => 'required|exists:id_types,id',
            'employee_id_number' => 'required|string|max:50',
            'employee_id_issued_date' => 'required|date',
            'employee_id_expiry_date' => 'nullable|date|after_or_equal:employee_id_issued_date',
        ]);

        if ($request->employee_id_record_id) {
            $employeeId = EmployeeId::findOrFail($request->employee_id_record_id);
            $employeeId = EmployeeId::update([
                'id_type_id' => $data['employee_id_type'],
                'id_number' => $data['employee_id_number'],
                'issue_date' => $data['employee_id_issued_date'],
                'expiry_date' => $data['employee_id_expiry_date'],
                'updated_by' => auth()->id(),
            ]);
        } else {
            $employeeId = EmployeeId::create([
                'employee_id' => $employee->id,
                'id_type_id' => $data['employee_id_type'],
                'id_number' => $data['employee_id_number'],
                'issue_date' => $data['employee_id_issued_date'],
                'expiry_date' => $data['employee_id_expiry_date'],
                'created_by' => auth()->id(),
            ]);
        }

        $employeeId->load('idType');

        return response()->json([
            'success' => true,
            'message' => 'Employee ID added successfully.',
            'data' => $employeeId
        ]);
    }
}
