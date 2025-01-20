<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    /**
     * @param  array<int, string>  $select
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<User>|Collection<int, User>
     */
    public function get(
        array $select = [],
        array $filters = [],
        bool $paginate = true
    ): LengthAwarePaginator|Collection;

    /**
     * @param  array<int, string>  $select
     */
    public function find(string $id, array $select = []): ?User;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ?User;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): ?User;

    public function delete(string $id): bool;
}
