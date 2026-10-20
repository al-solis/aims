<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\receiving_header;
use App\Models\uom;
use App\Models\supplies;

class receiving_detail extends Model
{
    protected $table = 'receiving_details';

    protected $fillable = [
        'receiving_header_id',
        'product_id',
        'quantity',
        'uom_id',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function receivingHeader()
    {
        return $this->belongsTo(receiving_header::class, 'receiving_header_id');
    }

    public function uom()
    {
        return $this->belongsTo(uom::class, 'uom_id');
    }

    public function product()
    {
        return $this->belongsTo(supplies::class, 'product_id');
    }
}
