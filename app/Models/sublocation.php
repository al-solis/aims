<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sublocation extends Model
{
    protected $table = "sublocations";

    protected $fillable = [
        'code',
        'location_id',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

}
