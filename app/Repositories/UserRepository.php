<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\User;
use App\Pipelines\User\SearchPipeline;
use App\Pipelines\User\SortPipeline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class UserRepository implements UserInterface
{
    public function get(array $select = ['name', 'email', 'created_at'], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
    {
        // Start building the query
        $query = User::query();
        $record_per_page = config('utility.record_per_page');

        $users = app(Pipeline::class)
            ->send($query)
            ->through([
                new SearchPipeline($filters),
                new SortPipeline($filters),
            ])
            ->thenReturn();

        // Apply pagination
        if ($paginate) {
            return $users->paginate($record_per_page)->appends($filters);
        }

        return $users->get();
    }
}
