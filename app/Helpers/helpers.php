<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

if (! function_exists('admin_roles')) {
    /**
     * @return array<int, array{id: int, name: string, permissions: array<string>, route_prefix: string}>
     */
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
            [
                'id' => 3,
                'name' => 'Admins',
                'permissions' => ['add', 'edit', 'view', 'delete'],
                'route_prefix' => 'admins',
            ]
        ];
    }
}

if (!function_exists('generatePassword')) {
    function generatePassword(string $string = ''): string
    {
        return Hash::make($string);
    }
}

if (! function_exists('get_ability')) {
    function get_ability(string $access): string
    {
        $route = request()->route();
        if ($route === null) {
            return '';
        }

        $routeName = $route->getName();
        if ($routeName === null) {
            return '';
        }

        $routeNameArray = explode('.', $routeName);
        $count = count($routeNameArray);

        return match ($count) {
            1, 2 => $access . '_' . $routeNameArray[0],
            3 => $access . '_' . $routeNameArray[1],
            default => ''
        };
    }
}

if (! function_exists('get_system_permissions')) {
    /**
     * @param array<array{route_prefix: string, permissions: array<string>}> $permissions
     * @return array<string>
     */
    function get_system_permissions(array $permissions): array
    {
        $permissions_array = [];

        foreach ($permissions as $permission) {
            $permissions_array[] = 'access_' . $permission['route_prefix'];
            foreach ($permission['permissions'] as $access) {
                $permissions_array[] = $access . '_' . $permission['route_prefix'];
            }
        }

        return $permissions_array;
    }
}

if (! function_exists('role_permissions')) {
    /**
     * @param string $role
     * @return array<int, array{id: int, name: string, permissions: array<string>, route_prefix: string}>
     */
    function role_permissions(string $role = 'admin'): array
    {
        if ($role === 'admin') {
            return admin_roles();
        }
        return [];
    }
}

if (! function_exists('create_permissions')) {
    /**
     * @param array<string> $defaultPermissions
     * @param Role $role
     * @param string $guard_name
     * @return void
     */
    function create_permissions(array $defaultPermissions, Role $role, string $guard_name = 'admin'): void
    {
        foreach ($defaultPermissions as $defaultPermission) {
            $permission = Permission::updateOrCreate([
                'name' => $defaultPermission,
                'guard_name' => $guard_name
            ]);
            $role->givePermissionTo($permission);
        }
    }
}

if (! function_exists('permission_to_array')) {
    /**
     * @param array<string> $permissions
     * @param string $role
     * @return array<int, array<string>>
     */
    function permission_to_array(array $permissions, string $role = 'admin'): array
    {
        if (empty($permissions)) {
            return [];
        }

        $roles_array = [];
        $rolePermissions = collect(role_permissions($role));

        foreach ($permissions as $permission) {
            $parts = explode('_', $permission);
            if (count($parts) !== 2) {
                continue;
            }

            [$access, $role_name] = $parts;
            if ($access === 'access') {
                continue;
            }

            $index = $rolePermissions->pluck('route_prefix')->search($role_name);
            if ($index === false) {
                continue;
            }

            $index++;
            if (!isset($roles_array[$index])) {
                $roles_array[$index] = [];
            }
            $roles_array[$index][] = $access;
        }

        return $roles_array;
    }
}

if (! function_exists('array_to_permission')) {
    /**
     * @param array<int, array<string>> $permissions
     * @param string $role
     * @return array<string>
     */
    function array_to_permission(array $permissions, string $role = 'admin'): array
    {
        if (empty($permissions)) {
            return [];
        }

        $permissions_array = [];
        $rolePermissions = role_permissions($role);

        foreach ($permissions as $key => $permission_prefixes) {
            $index = $key - 1;
            if (!isset($rolePermissions[$index])) {
                continue;
            }

            $currentRole = $rolePermissions[$index];
            $permissions_array[] = 'access_' . $currentRole['route_prefix'];

            foreach ($permission_prefixes as $prefix) {
                if (in_array($prefix, $currentRole['permissions'], true)) {
                    $permissions_array[] = $prefix . '_' . $currentRole['route_prefix'];
                }
            }
        }

        return $permissions_array;
    }
}
