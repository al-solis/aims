<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sublocation extends Model
{
    protected $table = "sublocations";

    protected $fillable = [
        'location_id',
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // public function locations()
    // {
    //     return $this->belongsTo(location::class, 'location_id');
    // }


}
