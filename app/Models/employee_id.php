<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\id_type as IdType;

class employee_id extends Model
{
    protected $table = "employee_ids";

    protected $fillable = [
        'employee_id',
        'id_type_id',
        'id_number',
        'issue_date',
        'expiry_date',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function idType()
    {
        return $this->belongsTo(IdType::class, 'id_type_id');
    }
}
