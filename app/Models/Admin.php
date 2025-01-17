<?php

namespace App\Models;

use App\Enums\AdminRoleEnum;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use CreatedUpdatedBy, HasFactory, Notifiable, HasRoles;

    protected $guard = 'admin';

    public const ADMIN_DEFAULT_PASSWORD = 'Admin@123';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'role_id',
        'name',
        'email',
        'password',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['access_permissions', 'custom_permissions'];

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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id');
    }


    public function getAccessPermissionsAttribute(): array
    {
        if ($this->permissions->count() > 0 && ! $this?->trashedRole?->trashed()) {
            $permission = $this->getPermissionsViaRoles()->pluck('name')->toArray();
            $additional_permissions = $this->permissions->pluck('name')->toArray();

            return array_unique([...$permission, ...$additional_permissions]);
        } else {
            return $this->getPermissionsViaRoles()->pluck('name')->toArray();
        }
    }

    public function getCustomPermissionsAttribute()
    {
        return $this->permissions->pluck('name')->toArray();
    }

    public function scopeExcludeSuperRole($query)
    {
        $query->whereNotIn('name', [Role::SUPER_ADMIN]);
    }

    public function scopeVisibility($query): void
    {
        $user = Auth::user();

        switch ($user->role) {
            case AdminRoleEnum::ADMIN->value:
                $query->whereHas('createdBy', fn($query) => $query->where('role', AdminRoleEnum::ADMIN->value));
                break;
        }
    }
}
