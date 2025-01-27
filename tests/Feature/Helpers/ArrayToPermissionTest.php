<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
describe('array_to_permission function', function () {
    it('returns empty array when input permissions are empty', function () {
        $result = array_to_permission([]);

        expect($result)->toBe([]);
    });

    it('correctly converts single permission group', function () {
        $permissions = [
            1 => ['add', 'edit'],
        ];

        $result = array_to_permission($permissions);
        expect($result)
            ->toBeArray()
            ->toContain('access_users')
            ->toContain('add_users')
            ->toContain('edit_users')
            ->toHaveCount(3);
    });

    it('correctly converts multiple permission groups', function () {
        $permissions = [
            1 => ['add', 'view'],
            2 => ['view', 'edit'],
            3 => ['view'],
        ];

        $result = array_to_permission($permissions);

        expect($result)
            ->toBeArray()
            ->toContain('access_users')
            ->toContain('add_users')
            ->toContain('view_users')
            ->toContain('access_roles')
            ->toContain('view_roles')
            ->toContain('edit_roles')
            ->toContain('access_admins')
            ->toContain('view_admins')
            ->toHaveCount(8);
    });

    it('skips invalid permission prefixes', function () {
        $permissions = [
            1 => ['add', 'view', 'invalid_permission'],
        ];

        $result = array_to_permission($permissions);

        expect($result)
            ->toBeArray()
            ->toContain('access_users')
            ->toContain('add_users')
            ->toContain('view_users')
            ->not->toContain('invalid_permission_users')
            ->toHaveCount(3);
    });

    it('skips invalid permission group indexes', function () {
        $permissions = [
            99 => ['create', 'read'],
        ];

        $result = array_to_permission($permissions);

        expect($result)->toBe([]);
    });
});
