<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class license_type extends Model
{
    protected $table = "license_types";

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function license_type()
    {
        return $this->hasMany(asset_license::class, 'license_type_id');
    }
}
