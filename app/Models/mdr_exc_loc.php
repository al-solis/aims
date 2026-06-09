<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mdr_exc_loc extends Model
{
    protected $table = 'mdr_exc_locs';

    protected $fillable = [
        'location_id',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
