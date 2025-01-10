<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    public function get(array $select = [],array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null;
}
