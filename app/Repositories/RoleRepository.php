<?php

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

    public function get(array $select = [''], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
    {
        // Start building the query
        $query = Role::select($select);
        $record_per_page = config('utility.record_per_page');

        $roles = app(Pipeline::class)
            ->send($query)
            ->through([
                new SearchPipeline($filters),
                new SortPipeline($filters),
            ])
            ->thenReturn();

            $roles->excludeSuperRole();

        if ($paginate) {
            return $roles->paginate($record_per_page);
        }

        return $roles->get();
    }


    public function store(array $attributes): Role
    {
        $role = Role::create(['name' => Str::uuid(), 'display_name' => $attributes['display_name']]);

        foreach ($attributes['permissions'] as $permissionName) {
            $permission = Permission::updateOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        return $role;
    }

    public function find(string $id): ?Role
    {
        // return Role::with(['favorite', 'permissions'])->visibility()->withTrashed()->find($id);
        return Role::find($id);
    }

    public function update(int $id, array $attributes): bool
    {
        $role = $this->find($id);

        $role->syncPermissions($attributes['permissions']);

        // if (auth()->user()->role == AdminRoleEnum::PRACTICE->value || auth()->user()->role == AdminRoleEnum::PROVIDER->value) {
        //     return true;
        // }

        return $role->update(['display_name' => $attributes['display_name']]) > 0;
    }

    public function delete(int $id): bool
    {
        // return Role::withTrashed()->findOrFail($id)->delete() > 0;
        return Role::findOrFail($id)->delete() > 0;
    }
}
