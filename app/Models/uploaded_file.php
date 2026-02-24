<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class uploaded_file extends Model
{
    protected $table = 'uploaded_files';
    protected $fillable = [
        'employee_id',
        'module',
        'note',
        'file_name',
        'path',
        'file_type',
        'uploaded_by',
    ];

    public function employee()
    {
        return $this->belongsTo(employee::class, 'employee_id');
    }
}


