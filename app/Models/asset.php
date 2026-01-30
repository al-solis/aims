<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class asset extends Model
{
    protected $table = "assets";

    protected $fillable = ([
        'asset_code',
        'name',
        'description',
        'category_id',
        'cost',
        'purchase_date',
        'status',
        'manufacturer',
        'model',
        'serial',
        'assigned_to',
        'location_id',
        'subloc_id',
        'condition',
        'warranty',
        'created_by',
        'updated_by',
    ]);
}
