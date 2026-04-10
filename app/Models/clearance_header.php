<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clearance_header extends Model
{
    protected $table = 'clearance_headers';

    protected $fillable = [
        'request_number',
        'employee_id',
        'type',
        'expected_date',
        'status',
        'remarks',
        'approval_status',
        'approval_level',
        'current_approver',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(employee::class, 'employee_id');
    }

    public function clearance_details()
    {
        return $this->hasMany(clearance_detail::class, 'clearance_header_id');
    }

    public function clearance_approver()
    {
        return $this->belongsTo(location::class, 'current_approver');
    }

    public function approvalHistory()
    {
        return $this->hasMany(clearance_approval::class, 'clearance_id');
    }

}
