<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'asset_code',
        'is_active',
        'created_by',
        'updated_by',
    ];

}
