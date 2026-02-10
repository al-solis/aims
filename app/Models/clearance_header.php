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
}
