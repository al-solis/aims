<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mdr_header extends Model
{
    protected $table = 'mdr_headers';

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

    public function mdrDetails()
    {
        return $this->hasMany(mdr_detail::class, 'mdr_header_id');
    }

}
