<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guard = 'admin';

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
}
