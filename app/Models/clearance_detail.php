<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clearance_detail extends Model
{
    protected $table = 'clearance_details';

    protected $fillable = [
        'clearance_header_id',
        'asset_id',
        'quantity',
        'return_quantity',
        'purchase_cost',
        'actual_cost',
        'total',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function clearance_header()
    {
        return $this->belongsTo(clearance_header::class, 'clearance_header_id');
    }

    public function asset()
    {
        return $this->belongsTo(asset::class, 'asset_id');
    }
}
