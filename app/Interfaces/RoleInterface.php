<?php

namespace App\Interfaces;

use Spatie\Permission\Models\Role;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleInterface
{

    public function get(array $select = [], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null;

    public function store(array $attributes): Role;

    public function find(string $id): ?Role;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}
