<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\AdminRoleEnum;
use App\Interfaces\AdminInterface;
use App\Interfaces\RoleInterface;
use App\Models\Admin;
use App\Pipelines\Admin\SearchPipeline;
use App\Pipelines\Admin\SortPipeline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

final class AdminRepository implements AdminInterface
{
    /**
     * @param  array<int, string>  $select
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<Admin>|Collection<int, Admin>
     */
    public function get(array $select = ['id', 'fist_name', 'last_name', 'email', 'role', 'created_at'], array $filters = [], bool $paginate = true): LengthAwarePaginator|Collection
    {
        $query = Admin::with('permissions')->select($select);
        $recordPerPage = filter_var(config('utility.record_per_page', 10), FILTER_VALIDATE_INT) ?: 10;

        $admins = app(Pipeline::class)
            ->send($query)
            ->through([
                new SearchPipeline($filters),
                new SortPipeline($filters),
            ])
            ->thenReturn();

        /** @var \Illuminate\Database\Eloquent\Builder<Admin> $admins */
        $admins->visibility();

        return $paginate
            ? $admins->paginate($recordPerPage)->appends($filters)
            : $admins->get();
    }

    /**
     * @param  array<int, string>  $select
     */
    public function find(string $id, array $select = ['*']): ?Admin
    {
        return Admin::select($select)->find($id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(array $attributes): Admin
    {
        $attributes['password'] = generatePassword(Admin::ADMIN_DEFAULT_PASSWORD);

        $admin = Admin::create([
            'first_name' => $attributes['first_name'],
            'last_name' => $attributes['last_name'],
            'email' => $attributes['email'],
            'password' => bcrypt($attributes['password']),
            'role' => AdminRoleEnum::ADMIN->value,
            'role_id' => $attributes['role_id'],
        ]);

        $permissions = is_array($attributes['custom_permissions']) ? $attributes['custom_permissions'] : [];
        $admin->syncPermissions($permissions);

        return $admin;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(string $id, array $attributes): bool
    {
        $admin = Admin::findOrFail($id);

        $role = app(RoleInterface::class)->find((string) $admin->role_id);

        if ($role) {
            $admin->removeRole($role->id);
        }

        $permissions = is_array($attributes['custom_permissions']) ? $attributes['custom_permissions'] : [];
        $admin->syncPermissions($permissions);

        $updateFields = $this->getFieldsForUpdate($attributes, $admin);

        return $admin->update($updateFields) > 0;
    }

    public function delete(string $id): bool
    {
        return Admin::findOrFail($id)->delete() > 0;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function getFieldsForUpdate(array $attributes, Admin $admin): array
    {
        $firstName = $attributes['first_name'] ?? $admin->first_name;
        $lastName = $attributes['last_name'] ?? $admin->last_name;
        // TODO: Change the name field to nullable in the database
        $name = 'WILL CHANGE NULLABLE';

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $name,
            'email' => $attributes['email'] ?? $admin->email,
        ];
    }
}
