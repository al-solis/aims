<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class issuance_detail extends Model
{
    protected $table = 'issuance_details';

    protected $fillable = [
        'issuance_header_id',
        'supply_id',
        'quantity',
        'uom_id',
        'unit_cost',
        'total_cost'
    ];

    public function issuanceHeader()
    {
        return $this->belongsTo(issuance_header::class, 'issuance_header_id');
    }

    public function supply()
    {
        return $this->belongsTo(Supplies::class, 'supply_id');
    }

    public function uom()
    {
        return $this->belongsTo(UOM::class, 'uom_id');
    }
}
