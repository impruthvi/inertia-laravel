<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

final class Role extends SpatieRole
{
    use CreatedUpdatedBy;

    public const SUPER_ADMIN = 'super_admin';

    public const SUPER_ADMIN_EMAIL = 'admin@test.com';

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'portal',
        'is_common_role',
        'created_by',
        'updated_by',
    ];

    /**
     * Scope a query to exclude the super admin role.
     *
     * @param  Builder<Role>  $query
     * @return Builder<Role>
     */
    public function scopeExcludeSuperRole(Builder $query): Builder
    {
        return $query->whereNotIn('display_name', [self::SUPER_ADMIN]);
    }

    /**
     * Get the admin who created the role.
     *
     * @return BelongsTo<Admin, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id');
    }
}
