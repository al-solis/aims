<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppliesCategory extends Model
{
    protected $table = 'supplies_categories';

    protected $fillable = [
        'name',
        'description',
        'supplies_code',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
