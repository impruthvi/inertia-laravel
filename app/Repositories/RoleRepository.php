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

    public function find(string $id): ?Role
    {
        // return Role::with(['favorite', 'permissions'])->visibility()->withTrashed()->find($id);
        return Role::find($id);
    }

    public function update(int $id, array $attributes): bool
    {
        $role = $this->find($id);

        $role->syncPermissions($attributes['permissions']);

        // if (auth()->user()->role == AdminRoleEnum::PRACTICE->value || auth()->user()->role == AdminRoleEnum::PROVIDER->value) {
        //     return true;
        // }

        return $role->update(['display_name' => $attributes['display_name']]) > 0;
    }

    public function delete(int $id): bool
    {
        // return Role::withTrashed()->findOrFail($id)->delete() > 0;
        return Role::findOrFail($id)->delete() > 0;
    }
}
