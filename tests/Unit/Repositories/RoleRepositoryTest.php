<?php

// i just write missing coverage test cases for UserRepository
declare(strict_types=1);

use App\Interfaces\RoleInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can get error when we pass permission as string', function () {
    User::factory(20)->create();

    $roleRepository = app(RoleInterface::class);

    $roleRepository->store([
        'display_name' => 'test',
        'permissions' => 'test',
    ]);
})->throws(InvalidArgumentException::class, "The 'permissions' attribute must be an array.");
