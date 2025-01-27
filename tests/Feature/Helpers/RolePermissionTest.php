<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('role_permissions function', function () {
    it('returns an array of permissions for valid role', function () {
        $permissions = role_permissions('admin');
        expect($permissions)->toBe(admin_roles());
    });

    it('returns an empty array for an invalid role', function () {
        $permissions = role_permissions('invalid_role');
        expect($permissions)->toBe([]);
    });
});
