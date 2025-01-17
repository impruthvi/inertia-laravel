<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;


class Role extends SpatieRole
{
    use CreatedUpdatedBy;

    protected $table = 'roles';

    const SUPER_ADMIN = 'super_admin';

    const SUPER_ADMIN_EMAIL = 'admin@test.com';

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'portal',
        'is_common_role',
        'created_by',
        'updated_by',
    ];

    public function scopeExcludeSuperRole($query)
    {
        $query->whereNotIn('display_name', [Role::SUPER_ADMIN]);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id');
    }
}
