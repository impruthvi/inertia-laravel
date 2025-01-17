<?php

namespace App\Repositories;

use App\Interfaces\AdminInterface;
use App\Models\Admin;
use App\Pipelines\Admin\SearchPipeline;
use App\Pipelines\Admin\SortPipeline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class AdminRepository implements AdminInterface
{
    public function get(array $select = ['id', 'name', 'email', 'role','created_at'], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
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

    public function store(array $attributes): Admin
    {
        $attributes['password'] = generatePassword(Admin::ADMIN_DEFAULT_PASSWORD);

        $admin = Admin::create($attributes);

        $admin->syncPermissions($attributes['custom_permissions']);

        return $admin;
    }

    public function delete(int $id): bool
    {
        return Admin::findOrFail($id)->delete() > 0;
    }
}
