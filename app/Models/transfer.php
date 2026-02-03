<?php

namespace App\Models;
use App\Models\transfer_detail as TransferDetail;
use Illuminate\Database\Eloquent\Model;

class transfer extends Model
{
    protected $table = 'transfers';

    protected $fillable = [
        'code',
        'date',
        'description',
        'cancelled',
        'created_by',
        'updated_by',
    ];

    public function transferDetails()
    {
        return $this->hasMany(TransferDetail::class, 'transfer_id');
    }

}