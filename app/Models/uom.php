<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    protected $table = 'uoms';

    protected $fillable = [
        'name',
        'code',
        'conversion_factor',
        'base_uom',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
