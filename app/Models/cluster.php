<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cluster extends Model
{
    protected $table = 'clusters';
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
