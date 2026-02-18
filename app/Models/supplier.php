<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
