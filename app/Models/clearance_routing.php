<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Location;

class clearance_routing extends Model
{
    protected $table = 'clearance_routings';

    protected $fillable = [
        'location_id',
        'order',
        'created_by',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
