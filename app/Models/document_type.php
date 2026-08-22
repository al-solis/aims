<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class document_type extends Model
{
    protected $table = 'document_types';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
