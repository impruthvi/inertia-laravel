<?php

namespace App\Interfaces;


use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminInterface
{
    public function get(array $select = [],array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null;
    public function find(int $id, array $select = []): Admin|null;
    public function store(array $attributes): Admin;
    public function update(int $id, array $attributes): bool;
    public function delete(int $id): bool;
}
