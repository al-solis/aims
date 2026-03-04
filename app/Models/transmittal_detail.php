<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transmittal_detail extends Model
{
    protected $table = 'transmittal_details';
    protected $fillable = [
        'transmittal_header_id',
        'quantity',
        'unit',
        'item_id',
        'asset_id',
        'tag',
        'created_by',
        'updated_by'
    ];
    public function transmittalHeader()
    {
        return $this->belongsTo(transmittal_header::class, 'transmittal_header_id');
    }
    public function item()
    {
        return $this->belongsTo(Supplies::class, 'item_id');
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
