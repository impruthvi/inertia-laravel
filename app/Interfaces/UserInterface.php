<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    public function get(array $select = [],array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null;
    public function find(int $id, array $select = []): User|null;
    public function create(array $data): User|null;
    public function update(int $id, array $data): User|null;
    public function delete(int $id): bool;
}
