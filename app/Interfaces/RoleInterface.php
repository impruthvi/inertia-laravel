<?php

namespace App\Interfaces;

use Spatie\Permission\Models\Role;

interface RoleInterface
{

    public function store(array $attributes): Role;

    public function find(string $id): ?Role;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}
