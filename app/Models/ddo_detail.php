<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ddo_detail extends Model
{
    protected $table = 'ddo_details';

    protected $fillable = [
        'ddo_header_id',
        'employee_id',
        'type',
        'created_by',
        'updated_by',
    ];

    public function ddoHeader()
    {
        return $this->belongsTo(ddo_header::class, 'ddo_header_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
