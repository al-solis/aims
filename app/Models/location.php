<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class location extends Model
{
    protected $table = "locations";

    protected $fillable = [
        'cluster_id',
        'code',
        'name',
        'description',
        'address',
        'contact_number',
        'status',
        'created_by',
        'updated_by',
    ];

    public function sublocations()
    {
        return $this->hasMany(sublocation::class, 'location_id');
    }

    public function cluster()
    {
        return $this->belongsTo(cluster::class, 'cluster_id');
    }
}
