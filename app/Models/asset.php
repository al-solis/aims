<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\category;
use App\Models\location;
use App\Models\employee;

class asset extends Model
{
    protected $table = "assets";

    protected $fillable = ([
        'asset_code',
        'name',
        'description',
        'category_id',
        'subcategory',
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

    public function location()
    {
        return $this->belongsTo(location::class, 'location_id');
    }

    public function category()
    {
        return $this->belongsTo(category::class, 'category_id');
    }

    public function assigned_user()
    {
        return $this->belongsTo(employee::class, 'assigned_to');
    }

    public function licenses()
    {
        return $this->hasMany(asset_license::class, 'asset_id');
    }

    public function duty_orders()
    {
        return $this->hasMany(duty_order::class, 'asset_id');
    }
}
