<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transfer extends Model
{
    protected $table = 'transfers';

    protected $fillable = [
        'code',
        'date',
        'asset_id',
        'description',
        'from_employee_id',
        'to_employee_id',
        'location_id',
        'subloc_id',
        'cancelled',
        'created_by',
        'updated_by',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function sublocation()
    {
        return $this->belongsTo(Sublocation::class, 'subloc_id');
    }
}