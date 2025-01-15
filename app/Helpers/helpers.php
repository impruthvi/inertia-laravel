<?php

use Spatie\Permission\Models\Permission;

if (! function_exists('admin_roles')) {
    function admin_roles(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Users',
                'permissions' => ['add', 'edit', 'view', 'delete'],
                'route_prefix' => 'users',
            ],
            [
                'id' => 2,
                'name' => 'Roles',
                'permissions' => ['add', 'edit', 'view', 'delete'],
                'route_prefix' => 'roles',
            ],

        ];
    }
}

if (!function_exists('generatePassword')) {
    function generatePassword(string $string = ''): string
    {
        return \Hash::make($string);
    }
}

if (! function_exists('get_ability')) {
    function get_ability($access): string
    {
        $routeNameArray = explode('.', request()->route()->getName());
        switch (count($routeNameArray)) {
            case '1':
            case '2':
                return $access . '_' . $routeNameArray[0];

            case '3':
                return $access . '_' . $routeNameArray[1];
        }
    }
}

if (! function_exists('get_system_permissions')) {
    function get_system_permissions(array $permissions): array
    {
        $permissions_array = [];

        foreach ($permissions as $permission) {
            array_push($permissions_array, 'access_' . $permission['route_prefix']);
            foreach ($permission['permissions'] as $access) {
                array_push($permissions_array, $access . '_' . $permission['route_prefix']);
            }
        }

        return $permissions_array;
    }
}

if (! function_exists('role_permissions')) {
    function role_permissions($role = 'admin'): array
    {
        if ($role === 'admin') {
            return admin_roles();
        }
    }
}

if (! function_exists('create_permissions')) {
    function create_permissions(array $defaultPermissions, $role, string $guard_name = 'admin'): void
    {
        foreach ($defaultPermissions as $defaultPermission) {
            $permission = Permission::updateOrCreate(['name' => $defaultPermission, 'guard_name' => $guard_name]);
            $role->givePermissionTo($permission);
        }
    }
}

if (! function_exists('permission_to_array')) {
    function permission_to_array(array $permissions, string $role = 'admin'): array
    {
        if (empty($permissions)) {
            return [];
        }

        $roles_array = [];

        foreach ($permissions as $permission) {
            [$access, $role_name] = explode('_', $permission);
            if ($access !== 'access') {
                $index = collect(role_permissions($role))->pluck('route_prefix')->search($role_name) + 1;
                if (empty($roles_array)) {
                    $roles_array[$index] = [$access];
                } else {
                    if (array_key_exists($index, $roles_array)) {
                        $roles_array[$index] = [...$roles_array[$index], $access];
                    } else {
                        $roles_array[$index] = [$access];
                    }
                }
            }
        }

        return $roles_array;
    }
}
