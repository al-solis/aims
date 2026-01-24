<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class location extends Model
{
    protected $table = "locations";

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function sublocations()
    {
        return $this->hasMany(sublocation::class, 'location_id');
    }
}
