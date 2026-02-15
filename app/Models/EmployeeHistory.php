<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    protected $table = 'employee_history';

    protected $fillable = [
        'employee_id',
        'company',
        'position',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}