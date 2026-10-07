<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public function users()
    {
        return $this->hasMany(user::class, 'role_id');
    }
}
