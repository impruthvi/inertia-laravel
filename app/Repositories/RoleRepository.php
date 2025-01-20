<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use App\Models\Role;
use App\Pipelines\Role\SearchPipeline;
use App\Pipelines\Role\SortPipeline;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class RoleRepository implements RoleInterface
{

    /**
     * @param array<int, string> $select
     * @param array<string, mixed> $filters
     * @param bool $paginate
     * @return LengthAwarePaginator<Role>|Collection<int, Role>|null
     */
    public function get(array $select = ['id', 'name', 'display_name', 'guard_name', 'portal', 'is_common_role'], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
    {
        // Start building the query
        $query = Role::select($select);
        $record_per_page = filter_var(config('utility.record_per_page', 10), FILTER_VALIDATE_INT) ?: 10;
        $roles = app(Pipeline::class)
            ->send($query)
            ->through([
                new SearchPipeline($filters),
                new SortPipeline($filters),
            ])
            ->thenReturn();


        /** @var \Illuminate\Database\Eloquent\Builder<Role> $roles */
        $roles->excludeSuperRole();

        if ($paginate) {
            return $roles->paginate($record_per_page);
        }

        return $roles->get();
    }

    /**
     * @param array<mixed> $attributes
     * @return Role
     * @throws \InvalidArgumentException if permissions are not iterable
     */
    public function store(array $attributes): Role
    {
        $role = Role::create(['name' => (string) Str::uuid(), 'display_name' => $attributes['display_name']]);
        // Validate 'permissions' key
        if (!isset($attributes['permissions']) || !is_array($attributes['permissions'])) {
            throw new \InvalidArgumentException("The 'permissions' attribute must be an array.");
        }
        foreach ($attributes['permissions'] as $permissionName) {
            $permission = Permission::updateOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        /** @var Role $role */
        return $role;
    }


    /**
     * @param string $id
     * @return Role|null
     */
    public function find(string $id): ?Role
    {
        // return Role::with(['favorite', 'permissions'])->visibility()->withTrashed()->find($id);
        return Role::find($id);
    }
    /**
     * @param string $id
     * @param array<string, mixed> $attributes
     * @return bool
     */
    public function update(string $id, array $attributes): bool
    {
        $role = $this->find($id);

        if (!$role) {
            // Handle the case when the role is not found
            return false; // Or throw an exception
        }

        // Sync permissions
        if (isset($attributes['permissions']) && is_array($attributes['permissions'])) {
            $role->syncPermissions($attributes['permissions']);
        }

        // Update the display name
        return $role->update(['display_name' => $attributes['display_name']]) > 0;
    }


    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        // return Role::withTrashed()->findOrFail($id)->delete() > 0;
        return Role::findOrFail($id)->delete() > 0;
    }
}
