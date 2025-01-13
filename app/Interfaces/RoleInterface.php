<?php

namespace App\Interfaces;

use Spatie\Permission\Models\Role;

interface RoleInterface
{

    public function store(array $attributes): Role;
}
