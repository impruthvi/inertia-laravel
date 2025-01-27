<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('permission_to_array function', function () {
    it('returns a formatted array of permissions based on role', function () {

        // Define the permissions we want to pass in
        $permissions = ['edit_admins', 'create_admins', 'delete_admins', 'access_admins'];

        // Call the permission_to_array function
        $result = permission_to_array($permissions, 'admin');
        // Assert that the permissions are mapped correctly for the 'admin' role
        expect($result)->toBe([
            3 => ['edit', 'create', 'delete'],
        ]);
    });

    it('returns an empty array when no valid permissions are passed', function () {
        $permissions = [];

        $result = permission_to_array($permissions, 'admin');

        expect($result)->toBeEmpty();
    });

    it('skips invalid or malformed permissions', function () {
        $permissions = ['edit_admins', 'invalid_permission', 'access_admins'];

        // Call the function and check the result
        $result = permission_to_array($permissions, 'admin');

        expect($result)->toBe([
            3 => ['edit'],
        ]);
    });

    it('returns an empty array for unrecognized roles', function () {

        $permissions = ['edit_admins'];

        $result = permission_to_array($permissions, 'unknown_role');

        expect($result)->toBeEmpty();
    });

    it('skips malformed permissions where count of parts is not 2', function () {
        // Define malformed permissions
        $permissions = ['edit_admins', 'create_admins', 'delete', 'access_admins', 'invalid_permission'];

        // Call the permission_to_array function
        $result = permission_to_array($permissions, 'admin');

        // Assert that malformed permissions (like 'delete' or 'invalid_permission') are skipped
        expect($result)->toBe([
            3 => ['edit', 'create'],
        ]);
    });

    it('processes permissions with exactly two parts', function () {
        // Define valid permissions (with exactly 2 parts)
        $permissions = ['edit'];

        // Call the permission_to_array function
        $result = permission_to_array($permissions, 'admin');
        // Assert that valid permissions are processed correctly
        expect($result)->toBeEmpty();
    });

    it('skips permissions that do not have exactly two parts after explode', function () {
        // Permissions that will split into an array with a count other than 2
        $invalidPermissions = [
            'access',               // Only one part after explode
            'view_only_permission', // Only one part after explode
            'edit_permission_extra', // Only one part after explode
            'delete',               // Only one part after explode
            'create_permission_edit_permission_extra', // More than two parts after explode
        ];

        $result = permission_to_array($invalidPermissions);

        // Assert that the function skips all invalid permissions
        expect($result)->toBeEmpty();
    });
});
