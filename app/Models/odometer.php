<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
