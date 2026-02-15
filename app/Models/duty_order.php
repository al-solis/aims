<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class duty_order extends Model
{
    protected $table = 'duty_orders';
    protected $fillable = [
        'order_number',
        'employee_id',
        'asset_id',
        'expiry_date',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
