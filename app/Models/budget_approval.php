<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class budget_approval extends Model
{
    protected $table = 'budget_approvals';

    protected $fillable = [
        'budget_id',
        'location_id',
        'approver_id',
        'approved',
        'remarks',
    ];

    public function budget()
    {
        return $this->belongsTo(budget_header::class, 'budget_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
