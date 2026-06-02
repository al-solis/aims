<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class budget_routing extends Model
{
    protected $table = 'budget_routings';

    protected $fillable = [
        'location_id',
        'order',
        'created_by',
    ];

    public function location()
    {
        return $this->belongsTo(location::class);
    }
}
