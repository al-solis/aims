<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transmittal_header extends Model
{
    protected $table = 'transmittal_headers';

    protected $fillable = [
        'transmittal_number',
        'transmittal_date',
        'transmitted_to',
        'location_id',
        'remarks',
        'created_by',
        'updated_by'
    ];

    public function transmittedTo()
    {
        return $this->belongsTo(Employee::class, 'transmitted_to');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
