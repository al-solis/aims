<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\transfer;
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
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function fromEmployee()
    {
        return $this->belongsTo(Employee::class, 'from_employee_id');
    }

    public function toEmployee()
    {
        return $this->belongsTo(Employee::class, 'to_employee_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function fromSublocation()
    {
        return $this->belongsTo(Sublocation::class, 'from_subloc_id');
    }

    public function toSublocation()
    {
        return $this->belongsTo(Sublocation::class, 'to_subloc_id');
    }
}
