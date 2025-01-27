<?php
// i just write missing coverage test cases for UserRepository
declare(strict_types=1);


use App\Models\User;
use App\Interfaces\UserInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can get all users without pagination', function () {
    User::factory(20)->create();

    $userRepository = app(UserInterface::class);

    $users = $userRepository->get(
        select: ['id', 'name', 'email', 'created_at'],
        filters: [],
        paginate: false
    );

    expect($users->count())->toBe(20);
});