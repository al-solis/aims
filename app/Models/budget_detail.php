<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class budget_detail extends Model
{
    protected $table = 'budget_details';

    protected $fillable = [
        'budget_header_id',
        'quantity',
        'item_code',
        'item_description',
        'unit_id',
        'unit_price',
        'total_price',
        'created_by'
    ];

    public function budgetHeader()
    {
        return $this->belongsTo(budget_header::class, 'budget_header_id');
    }

    public function unit()
    {
        return $this->belongsTo(Uom::class, 'unit_id');
    }
}
