<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\transfer;
use App\Models\asset;
use App\Models\employee;
use App\Models\location;
use App\Models\sublocation;
class transfer_detail extends Model
{
    protected $table = 'transfer_details';

    protected $fillable = [
        'transfer_id',
        'asset_id',
        'from_employee_id',
        'from_location_id',
        'from_subloc_id',
        'to_employee_id',
        'to_location_id',
        'to_subloc_id',
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id');
    }
    public function asset()
    {
        return $this->belongsTo(asset::class, 'asset_id');
    }

    public function fromEmployee()
    {
        return $this->belongsTo(employee::class, 'from_employee_id');
    }

    public function toEmployee()
    {
        return $this->belongsTo(employee::class, 'to_employee_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(location::class, 'to_location_id');
    }

    public function fromSublocation()
    {
        return $this->belongsTo(sublocation::class, 'from_subloc_id');
    }

    public function toSublocation()
    {
        return $this->belongsTo(sublocation::class, 'to_subloc_id');
    }
}
