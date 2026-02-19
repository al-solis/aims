<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class receiving_header extends Model
{
    protected $table = 'receiving_headers';

    protected $fillable = [
        'transaction_number',
        'description',
        'received_date',
        'reference',
        'supplier_id',
        'received_by',
        'remarks',
        'status',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }

    public function details()
    {
        return $this->hasMany(receiving_detail::class, 'receiving_header_id');
    }
}
