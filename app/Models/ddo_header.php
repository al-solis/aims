<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ddo_header extends Model
{
    protected $table = 'ddo_headers';

    protected $fillable = [
        'location_id',
        'remarks',
        'count',
        'status',
        'created_by',
        'updated_by',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function ddoDetails()
    {
        return $this->hasMany(ddo_detail::class, 'ddo_header_id');
    }

}
