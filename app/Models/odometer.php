<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\asset;
use App\Models\employee;

class odometer extends Model
{
    protected $table = 'odometers';

    protected $fillable = [
        'asset_id',
        'employee_id',
        'date',
        'from_reading',
        'to_reading',
        'remarks',
        'created_by',
        'updated_by'
    ];

    public function asset()
    {
        return $this->belongsTo(asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(employee::class);
    }
}
