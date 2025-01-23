<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleInterface
{
    /**
     * @param  array<int, string>  $select
     * @param  array<string, mixed>  $filters
     * @param  bool  $paginate
     * @return LengthAwarePaginator<Role>|Collection<int, Role>
     */
    public function get(array $select = [], array $filters = [], $paginate = true): LengthAwarePaginator|Collection;

    /**
     * @param  array<mixed>  $attributes
     */
    public function store(array $attributes): Role;

    public function find(string $id): ?Role;

    /**
     * @param  array<mixed>  $attributes
     */
    public function update(Role $id, array $attributes): bool;

    public function delete(string $id): bool;
}
