<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleInterface
{

    /**
     * @param array<int, string> $select
     * @param array<string, mixed> $filters
     * @param bool $paginate
     * @return LengthAwarePaginator<Role>|Collection<int, Role>|null
     */
    public function get(array $select = [], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null;

    /**
     * @param array<mixed> $attributes
     * @return Role
     */
    public function store(array $attributes): Role;

    /**
     * @param string $id
     * @return Role|null
     */
    public function find(string $id): ?Role;

    /**
     * @param string $id
     * @param array<mixed> $attributes
     * @return bool
     */
    public function update(string $id, array $attributes): bool;

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;
}
