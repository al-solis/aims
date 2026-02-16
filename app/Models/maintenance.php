<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'maintenances';

    protected $fillable = [
        'maintenance_code',
        'asset_id',
        'type',
        'description',
        'scheduled_date',
        'priority',
        'technician',
        'cost',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
