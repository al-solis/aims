<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class issuance_header extends Model
{
    protected $table = 'issuance_headers';

    protected $fillable = [
        'issuance_number',
        'purpose',
        'issued_to',
        'location_id',
        'issuance_date',
        'remarks',
        'status',
        'created_by',
        'updated_by'
    ];

    public function issuedTo()
    {
        return $this->belongsTo(employee::class, 'issued_to');
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function details()
    {
        return $this->hasMany(issuance_detail::class, 'issuance_header_id');
    }
}
