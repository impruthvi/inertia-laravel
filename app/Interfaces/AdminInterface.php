<?php

declare(strict_types=1);

namespace App\Interfaces;


use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminInterface
{
    /**
     * @param array<int, string> $select
     * @param array<string, mixed> $filters
     * @param bool $paginate
     * @return LengthAwarePaginator<Admin>|Collection<int, Admin>|null
     */
    public function get(array $select = [], array $filters = [], bool $paginate = true): LengthAwarePaginator|Collection|null;

    /**
     * @param string $id
     * @param array<int, string> $select
     * @return Admin|null
     */
    public function find(string $id, array $select = []): Admin|null;

    /**
     * @param array<string, mixed> $attributes
     * @return Admin
     */
    public function store(array $attributes): Admin;

    /**
     * @param string $id
     * @param array<string, mixed> $attributes
     * @return bool
     */
    public function update(string $id, array $attributes): bool;

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;
}
