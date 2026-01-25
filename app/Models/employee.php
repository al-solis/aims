<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'employee_code',
        'first_name',
        'middle_name',
        'last_name',
        'hire_date',
        'date_of_birth',
        'email',
        'mobile',
        'position',
        'location_id',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'emergency_contact',
        'emergency_phone',
        'status',
        'photo_path',
        'created_by',
        'updated_by',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
