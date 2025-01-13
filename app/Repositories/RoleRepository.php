<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use App\Models\Role;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RoleRepository implements RoleInterface
{


    public function store(array $attributes): Role
    {
        $role = Role::create(['name' => Str::uuid(), 'display_name' => $attributes['display_name']]);

        foreach ($attributes['permissions'] as $permissionName) {
            $permission = Permission::updateOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        return $role;
    }
}
