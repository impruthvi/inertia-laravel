<?php

namespace App\Repositories;

use App\Interfaces\AdminInterface;
use App\Interfaces\RoleInterface;
use App\Models\Admin;
use App\Pipelines\Admin\SearchPipeline;
use App\Pipelines\Admin\SortPipeline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class AdminRepository implements AdminInterface
{
    public function get(array $select = ['id', 'name', 'email', 'role', 'created_at'], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
    {
        // Start building the query
        $query = Admin::select($select);
        $record_per_page = config('utility.record_per_page');

        $admins = app(Pipeline::class)
            ->send($query)
            ->through([
                new SearchPipeline($filters),
                new SortPipeline($filters),
            ])
            ->thenReturn();

        $admins->visibility();

        // Apply pagination
        if ($paginate) {
            return $admins->paginate($record_per_page)->appends($filters);
        }

        return $admins->get();
    }

    public function find(int $id, array $select = ['*']): Admin|null
    {
        return Admin::select($select)->find($id);
    }


    public function store(array $attributes): Admin
    {
        $attributes['password'] = generatePassword(Admin::ADMIN_DEFAULT_PASSWORD);

        $admin = Admin::create($attributes);

        $admin->syncPermissions($attributes['custom_permissions']);

        return $admin;
    }

    public function update(int $id, array $attributes): bool
    {
        $admin = Admin::findOrFail($id);

        $role = app(RoleInterface::class)->find($admin->role_id);

        if ($role) {
            $admin->removeRole($role->id);
        }

        $admin->syncPermissions($attributes['custom_permissions']);

        $updateFields = $this->getFieldsForUpdate($attributes, $admin);

        $done = $admin->update($updateFields) > 0;

        return $done;
    }

    public function delete(int $id): bool
    {
        return Admin::findOrFail($id)->delete() > 0;
    }



    private function getFieldsForUpdate($attributes, Admin $admin)
    {
        $firstName = isset($attributes['first_name']) ? $attributes['first_name'] : $admin->first_name;
        $lastName = isset($attributes['last_name']) ? $attributes['last_name'] : $admin->last_name;
        $name = $firstName . ' ' . $lastName;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $name,
            'email' => isset($attributes['email']) ? $attributes['email'] : $admin->email,
        ];
    }
}
