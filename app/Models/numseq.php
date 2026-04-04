<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class numseq extends Model
{
    protected $table = 'numseqs';

    protected $fillable = [
        'name',
        'prefix',
        'month',
        'year',
        'current_number',
        'number_length',
        'created_by',
    ];
}
