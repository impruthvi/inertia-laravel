<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

use Illuminate\Database\Eloquent\Model;

class Role extends SpatieRole
{
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
}
