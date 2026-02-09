<?php

namespace App\Models;

use Hoa\Iterator\Append;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class asset_license extends Model
{
    protected $table = 'asset_licenses';

    protected $fillable = [
        'asset_id',
        'license_type_id',
        'license_number',
        'issuing_authority',
        'issue_date',
        'expiration_date',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['status', 'status_label'];

    public function getStatusAttribute()
    {
        $now = Carbon::now();
        if ($this->expiration_date > $now->copy()->addDays(30)) {
            return '1';
        } elseif ($this->expiration_date <= $now) {
            return '0';
        } elseif ($this->expiration_date > $now && $this->expiration_date <= $now->copy()->addDays(30)) {
            return '2';
        }
    }

    public function getStatusLabelAttribute()
    {
        $status = $this->status;
        if ($status == '1') {
            return [
                'label' => 'Active',
                'color' => 'green',
                'icon' => '
                    <svg class="w-5 h-5 text-green-600" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm3.97-9.03a.75.75 0 0 1-1.08.04L7.477 10.417l-2.384-2.384a.75.75 0 1 1 1.06-1.06l2.944 2.943a.75.75 0 0 1 .08.094z"/>
                    </svg>',
            ];
        } elseif ($status == '0') {
            return [
                'label' => 'Expired ' . Carbon::parse($this->expiration_date)->diffForHumans(null, true) . ' ago',
                'color' => 'red',
                'icon' => '
                    <svg class="w-5 h-5 text-red-600" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm3.536-9.95a.75.75 0 1 1-1.06-1.06L10.94 6.94l-2.475-2.475a.75.75 0 1 1-1.06 1.06L9.88 8l-2.475 2.475a.75.75 0 1 1-1.06-1.06L8.94 8l2.475-2.475z"/>
                    </svg>',
            ];
        } elseif ($status == '2') {
            return [
                'label' => 'Expires in ' . Carbon::parse($this->expiration_date)->diffForHumans(null, true),
                'color' => 'yellow',
                'icon' => '
                    <svg class="w-5 h-5 text-yellow-600" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">                            
                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                    </svg>',
            ];
        } elseif ($status == '3') {
            return [
                'label' => 'Unknown',
                'color' => 'gray',
                'icon' => '
                    <svg class="w-5 h-5 text-gray-600" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm.93-9.412c-.29.293-.72.712-.93.712-.21 0-.64-.42-.93-.712C6.24 5.293 5.5 4.42 5.5 3.5c0-.966.784-1.75 1.75-1.75s1.75.784 1.75 1.75c0 .92-.74 1.793-1.32 2.088zM8 11a1 1 0 1 1 .001-2.001A1 1 0 0 1 8 11z"/>
                    </svg>',
            ];
        } elseif ($status == '4') {
            return [
                'label' => 'No Expiration',
                'color' => 'blue',
                'icon' => '
                    <svg class="w-5 h-5 text-blue-600" width="16" height="16" fill="currentColor" class="bi bi-infinity" viewBox="0 0 16 16">
                        <path d="M5.5 3.5c1.933 0 3.5 1.567 3.5 3.5s-1.567 3.5-3.5 3.5S2 8.933 2 7s1.567-3.5 3.5-3.5zm0 1C4.12 4.5 3 5.62 3 7s1.12 2.5 2.5 2.5S8 8.38 8 7s-1.12-2.5-2.5-2.5zm4-1c1.933 0 3.5 1.567 3.5 3.5s-1.567 3.5-3.5 3.5S9 8.933 9 7s1.567-3.5 3.5-3.5zm0 1C10.12 4.5 9 5.62 9 7s1.12 2.5 2.5 2.5S14 8.38 14 7s-1.12-2.5-2.5-2.5z"/>
                    </svg>',
            ];
        } elseif ($status == '5') {
            return [
                'label' => 'Not Issued',
                'color' => 'gray',
                'icon' => '
                    <svg class="w-5 h-5 text-gray-600" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm3.536-9.95a.75.75 0 1 1-1.06-1.06L10.94 6.94l-2.475-2.475a.75.75 0 1 1-1.06 1.06L9.88 8l-2.475 2.475a.75.75 0 1 1-1.06-1.06L8.94 8l2.475-2.475z"/>
                    </svg>',
            ];
        } else {
            return [
                'label' => 'Unknown',
                'color' => 'gray',
                'icon' => '
                    <svg class="w-5 h-5 text-gray-600" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm.93-9.412c-.29.293-.72.712-.93.712-.21 0-.64-.42-.93-.712C6.24 5.293 5.5 4.42 5.5 3.5c0-.966.784-1.75 1.75-1.75s1.75.784 1.75 1.75c0 .92-.74 1.793-1.32 2.088zM8 11a1 1 0 1 1 .001-2.001A1 1 0 0 1 8 11z"/>
                    </svg>',
            ];
        }
    }

    public function scopeActive($query)
    {
        return $query->where('expiration_date', '>', Carbon::now()->addDays(30));
    }

    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<=', Carbon::now());
    }

    public function scopeExpiringSoon($query)
    {
        $now = Carbon::now();
        return $query->whereBetween('expiration_date', [$now, $now->copy()->addDays(30)]);
    }

    public function asset()
    {
        return $this->belongsTo(asset::class, 'asset_id');
    }

    public function license_type()
    {
        return $this->belongsTo(license_type::class, 'license_type_id');
    }


}
