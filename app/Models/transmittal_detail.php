<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\transmittal_header;
use App\Models\supplies;
use App\Models\asset;

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
        return $this->belongsTo(supplies::class, 'item_id');
    }
    public function asset()
    {
        return $this->belongsTo(asset::class, 'asset_id');
    }
}
