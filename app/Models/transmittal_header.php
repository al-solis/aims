<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\transmittal_detail;
use App\Models\employee;
use App\Models\location;

class transmittal_header extends Model
{
    protected $table = 'transmittal_headers';

    protected $fillable = [
        'transmittal_number',
        'transmittal_date',
        'transmitted_to',
        'location_id',
        'remarks',
        'status',
        'created_by',
        'updated_by'
    ];

    public function transmittedTo()
    {
        return $this->belongsTo(employee::class, 'transmitted_to');
    }

    public function location()
    {
        return $this->belongsTo(location::class, 'location_id');
    }

    public function details()
    {
        return $this->hasMany(
            transmittal_detail::class,
            'transmittal_header_id'
        );
    }
}
