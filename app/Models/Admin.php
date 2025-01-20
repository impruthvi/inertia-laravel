<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AdminRoleEnum;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Builder;
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

    /** @var string */
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

    /**
     * @return BelongsTo<Admin, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id');
    }

    /**
     * Get the access permissions for the admin.
     *
     * @return array<string>
     */
    public function getAccessPermissionsAttribute(): array
    {
        /**
         * Converts a collection of permission objects to an array of permission names.
         *
         * @template TKey of array-key
         * @template TValue
         * @param \Illuminate\Support\Collection<TKey, TValue> $collection
         * @return array<string>
         */
        $convertToStrings = function (\Illuminate\Support\Collection $collection): array {
            return $collection
                ->pluck('name')
                /** @var \Illuminate\Support\Collection<int, mixed> $collection */
                ->filter(function (mixed $item): bool {
                    return $item !== null;
                })
                /** @var \Illuminate\Support\Collection<int, mixed> $collection */
                ->map(function (mixed $item): string {
                    return is_string($item) ? $item : '';
                })
                /** @var \Illuminate\Support\Collection<int, string> $collection */
                ->filter(function (string $item): bool {
                    return $item !== '';
                })
                ->values()
                ->toArray();
        };

        /** 
         * @var \Illuminate\Support\Collection<int, \Spatie\Permission\Models\Permission>
         */
        $rolePermissions = $this->getPermissionsViaRoles();

        if ($this->permissions->count() > 0) {
            /** @var array<string> */
            $permissions = $convertToStrings($rolePermissions);

            /** 
             * @var \Illuminate\Support\Collection<int, \Spatie\Permission\Models\Permission> $directPermissions 
             */
            $directPermissions = $this->permissions;

            /** @var array<string> */
            $additionalPermissions = $convertToStrings($directPermissions);

            /** @var array<string> */
            return array_values(array_unique(
                array_merge($permissions, $additionalPermissions)
            ));
        }

        /** @var array<string> */
        return $convertToStrings($rolePermissions);
    }




    /**
     * @return array<mixed>
     */
    public function getCustomPermissionsAttribute()
    {
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * @param Builder<Admin> $query
     * @return Builder<Admin>
     */
    public function scopeExcludeSuperRole(Builder $query): Builder
    {
        return $query->whereNotIn('name', [Role::SUPER_ADMIN]);
    }


    /**
     * @param Builder<Admin> $query
     * @return void
     */
    public function scopeVisibility(Builder $query): void
    {
        $user = Auth::user();

        if ($user instanceof Admin) {
            switch ($user->role) {
                case AdminRoleEnum::ADMIN->value:
                    $query->whereHas('createdBy', function (Builder $query) {
                        $query->where('role', AdminRoleEnum::ADMIN->value);
                    });
                    break;
            }
        }
    }
}
