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

    /**
     * Retrieve a collection of users based on the provided filters and selection criteria.
     *
     * @param array $select The columns to select from the users table. Default is ['name', 'email', 'created_at'].
     * @param array $filters The filters to apply to the query.
     * @param bool $paginate Whether to paginate the results. Default is true.
     * 
     * @return LengthAwarePaginator|Collection|null The paginated collection of users or a collection of users.
     */
    public function get(array $select = ['id', 'name', 'email', 'created_at'], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
    {
        // Start building the query
        $query = User::select($select);
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
