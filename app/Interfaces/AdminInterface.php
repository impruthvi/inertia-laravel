<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminInterface
{
    /**
     * @param  array<int, string>  $select
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<Admin>|Collection<int, Admin>
     */
    public function get(array $select = [], array $filters = [], bool $paginate = true): LengthAwarePaginator|Collection;

    /**
     * @param  array<int, string>  $select
     */
    public function find(string $id, array $select = []): ?Admin;

    /**
     * @param  array<mixed>  $attributes
     */
    public function store(array $attributes): Admin;

    /**
     * @param  array<mixed>  $attributes
     */
    public function update(Admin $admin, array $attributes): bool;

    public function delete(string $id): bool;
}
