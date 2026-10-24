<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\employee;
use App\Models\sublocation;
use App\Models\cluster;

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
        'latitude',
        'longitude',
        'place_id',
        'employee_count',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'employee_count' => 'integer',
    ];

    public function sublocations()
    {
        return $this->hasMany(sublocation::class, 'location_id');
    }

    public function cluster()
    {
        return $this->belongsTo(cluster::class, 'cluster_id');
    }

    public function employees()
    {
        return $this->hasMany(employee::class, 'location_id');
    }

}
