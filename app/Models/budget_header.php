<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class budget_header extends Model
{
    protected $table = 'budget_headers';

    protected $fillable = [
        'apv_no',
        'requested_by',
        'location_id',
        'purpose',
        'remarks',
        'total_amount',
        'status',
        'approver_id',
        'approver_remarks',
        'requested_at',
        'submitted_at',
        'approved_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function budgetDetails()
    {
        return $this->hasMany(budget_detail::class, 'budget_header_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
