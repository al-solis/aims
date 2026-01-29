<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class id_type extends Model
{
    protected $table = "id_types";

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
