<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplies extends Model
{
    protected $table = 'supplies';

    protected $fillable = [
        'code',
        'name',
        'description',
        'supplier_id',
        'category_id',
        'uom_id',
        'reorder_level',
        'reorder_quantity',
        'unit_price',
        'allocated_stock',
        'available_stock',
        'total_stock',
        'status',
        'created_by',
        'updated_by'
    ];

    public function category()
    {
        return $this->belongsTo(SuppliesCategory::class, 'category_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
