<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\employee_id as EmployeeId;
use App\Models\EmployeeHistory;
use App\Models\Location;
class employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'employee_code',
        'first_name',
        'middle_name',
        'last_name',
        'hire_date',
        'termination_date',
        'gender',
        'marital_status',
        'date_of_birth',
        'email',
        'mobile',
        'position',
        'location_id',
        'employment_type',
        'monthly_salary',
        'daily_rate',
        'sss_rate',
        'philhealth_rate',
        'pagibig_rate',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'highest_education',
        'school',
        'course',
        'year_attended',
        'emergency_contact',
        'emergency_phone',
        'status',
        'photo_path',
        'created_by',
        'updated_by',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function employeeIds()
    {
        return $this->hasMany(EmployeeId::class);
    }

    public function employeeHistories()
    {
        return $this->hasMany(EmployeeHistory::class, 'employee_id');
    }
}
