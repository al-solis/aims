<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\employee;
use App\Models\document_type;

class uploaded_file extends Model
{
    protected $table = 'uploaded_files';
    protected $fillable = [
        'employee_id',
        'module',
        'document_type_id',
        'document_date',
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

    public function documentType()
    {
        return $this->belongsTo(document_type::class, 'document_type_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}


