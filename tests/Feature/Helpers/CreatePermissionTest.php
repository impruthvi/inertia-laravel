<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

describe('create_permissions function', function () {
    it('creates permissions and assigns them to a role', function () {
        // Arrange
        $defaultPermissions = ['view_users', 'edit_users', 'delete_users'];
        $role = Role::create(['name' => 'Admin', 'guard_name' => 'admin']);
        $guardName = 'admin';

        // Act
        create_permissions($defaultPermissions, $role, $guardName);

        // Assert
        foreach ($defaultPermissions as $permissionName) {
            $this->assertDatabaseHas('permissions', [
                'name' => $permissionName,
                'guard_name' => $guardName,
            ]);

            $this->assertTrue($role->hasPermissionTo($permissionName));
        }
    });

    it('does not duplicate existing permissions', function () {
        // Arrange
        $existingPermission = Permission::create([
            'name' => 'view_users',
            'guard_name' => 'admin',
        ]);

        $defaultPermissions = ['view_users', 'edit_users'];
        $role = Role::create(['name' => 'Editor', 'guard_name' => 'admin']);
        $guardName = 'admin';

        // Act
        create_permissions($defaultPermissions, $role, $guardName);

        // Assert
        $this->assertDatabaseCount('permissions', 2); // No duplicate for 'view_users'
        $this->assertTrue($role->hasPermissionTo('view_users'));
        $this->assertTrue($role->hasPermissionTo('edit_users'));
    });
});
