<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mdr_detail extends Model
{
    protected $table = 'mdr_details';

    protected $fillable = [
        'mdr_header_id',
        'employee_id',
        'type',
        'created_by',
        'updated_by',
    ];

    public function mdrHeader()
    {
        return $this->belongsTo(mdr_header::class, 'mdr_header_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
