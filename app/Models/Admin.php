<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AdminRoleEnum;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

final class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use CreatedUpdatedBy, HasFactory, HasRoles, Notifiable;

    public const ADMIN_DEFAULT_PASSWORD = 'Admin@123';


    // @phpstan-ignore-next-line
    private string $guard = 'admin';

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
        'email',
        'password',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['access_permissions', 'custom_permissions', 'full_name'];

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
     * @return BelongsTo<Admin, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by', 'id');
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
         *
         * @param  \Illuminate\Support\Collection<TKey, TValue>  $collection
         * @return array<string>
         */
        $convertToStrings = (fn(\Illuminate\Support\Collection $collection): array => $collection
            ->pluck('name')
            /** @var \Illuminate\Support\Collection<int, mixed> $collection */
            ->filter(fn(mixed $item): bool => $item !== null)
            /** @var \Illuminate\Support\Collection<int, mixed> $collection */
            ->map(fn(mixed $item): string => is_string($item) ? $item : '')
            /** @var \Illuminate\Support\Collection<int, string> $collection */
            ->filter(fn(string $item): bool => $item !== '')
            ->values()
            ->toArray());

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

    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * @return array<mixed>
     */
    public function getCustomPermissionsAttribute()
    {
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * @param  Builder<Admin>  $query
     * @return Builder<Admin>
     */
    public function scopeExcludeSuperRole(Builder $query): Builder
    {
        return $query->whereNotIn('name', [Role::SUPER_ADMIN]);
    }

    /**
     * @param  Builder<Admin>  $query
     */
    public function scopeVisibility(Builder $query): void
    {
        $user = Auth::user();

        if ($user instanceof self && $user->role === AdminRoleEnum::ADMIN->value) {
            $query->whereHas('createdBy', function (Builder $query): void {
                $query->where('role', AdminRoleEnum::ADMIN->value);
            });
        }
    }

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
}
