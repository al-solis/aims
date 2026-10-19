<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\system_theme;
class role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description',
        'theme_id',
        'is_active'
    ];

    public function users()
    {
        return $this->hasMany(user::class, 'role_id');
    }

    public function theme()
    {
        return $this->belongsTo(system_theme::class, 'theme_id');
    }
}
