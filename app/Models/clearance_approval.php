<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clearance_approval extends Model
{
    protected $table = 'clearance_approvals';

    protected $fillable = [
        'clearance_id',
        'location_id',
        'approver_id',
        'approved',
        'remarks',
    ];

    public function clearance()
    {
        return $this->belongsTo(clearance_header::class, 'clearance_id');
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
