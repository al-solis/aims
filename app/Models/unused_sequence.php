<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class unused_sequence extends Model
{
    protected $table = 'unused_sequences';

    protected $fillable = [
        'name',
        'control_number',
        'created_by',
    ];
}
