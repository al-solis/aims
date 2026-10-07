<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class module extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'img',
        'src',
        'sequence',
        'is_active',
    ];

    public function subModules()
    {
        return $this->hasMany(sub_module::class, 'module_id', 'id');
    }
}
