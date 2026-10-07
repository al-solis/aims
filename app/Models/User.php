<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lname',
        'fname',
        'mname',
        'employee_code',
        'email',
        'password',
        'role',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_code', 'employee_code');
    }

    public function getDepartmentAttribute()
    {
        return $this->employee?->location_id;
    }

    public function accessibleSubModules(?string $moduleCode = null, ?bool $main = null)
    {
        return sub_module::query()
            ->select('sub_modules.*')
            ->join('modules', 'modules.id', '=', 'sub_modules.module_id')
            ->join('access_rights', 'access_rights.sub_module_id', '=', 'sub_modules.id')
            ->where('access_rights.role_id', $this->role_id)
            ->where('access_rights.can_read', 1)
            ->where('sub_modules.is_active', 1)
            ->where('modules.is_active', 1)
            ->when($moduleCode, fn(Builder $q) => $q->where('modules.code', $moduleCode))
            ->when($main === true, fn(Builder $q) => $q->where('sub_modules.code', 'like', '%-01'))
            ->when($main === false, fn(Builder $q) => $q->where('sub_modules.code', 'not like', '%-01'))
            ->orderBy('modules.sequence')
            ->orderBy('sub_modules.sequence');
    }

    public function hasAccess(string $subModuleCode, string $action = 'read'): bool
    {
        $column = match ($action) {
            'create' => 'can_create',
            'read' => 'can_read',
            'update' => 'can_update',
            'delete' => 'can_delete',
            default => throw new \InvalidArgumentException("Unknown action: $action"),
        };

        return access_right::query()
            ->join('sub_modules', 'sub_modules.id', '=', 'access_rights.sub_module_id')
            ->where('access_rights.role_id', $this->role_id)
            ->where('sub_modules.code', $subModuleCode)
            ->where('sub_modules.is_active', 1)
            ->where("access_rights.$column", 1)
            ->exists();
    }

    public function userRole()
    {
        return $this->belongsTo(role::class, 'role_id');
    }
}
