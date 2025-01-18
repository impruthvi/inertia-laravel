<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    /**
     * @param array<int, string> $select
     * @param array<string, mixed> $filters
     * @param bool $paginate
     * @return LengthAwarePaginator<User>|Collection<int, User>|null
     */
    public function get(
        array $select = [],
        array $filters = [],
        bool $paginate = true
    ): LengthAwarePaginator|Collection|null;

    /**
     * @param string $id
     * @param array<int, string> $select
     * @return User|null
     */
    public function find(string $id, array $select = []): User|null;

    /**
     * @param array<string, mixed> $data
     * @return User|null
     */
    public function create(array $data): User|null;

    /**
     * @param string $id
     * @param array<string, mixed> $data
     * @return User|null
     */
    public function update(string $id, array $data): User|null;

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;
}
