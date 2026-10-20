<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\budget_approval;
use App\Models\budget_detail;
use App\Models\location;
use App\Models\User;

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
        'approval_status',
        'approval_level',
        'current_approver',
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

    public function budgetApprover()
    {
        return $this->belongsTo(location::class, 'current_approver');
    }

    public function location()
    {
        return $this->belongsTo(location::class, 'location_id');
    }

    public function approvalHistory()
    {
        return $this->hasMany(budget_approval::class, 'budget_id');
    }

    public function latestApproval()
    {
        return $this->hasOne(budget_approval::class, 'budget_id')
            ->latestOfMany();
    }
}
