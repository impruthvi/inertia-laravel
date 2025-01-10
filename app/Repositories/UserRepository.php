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

    /**
     * Retrieve a user based on the provided ID and selection criteria.
     *
     * @param int $id The ID of the user to retrieve.
     * @param array $select The columns to select from the users table. Default is ['id', 'name', 'email', 'created_at'].
     * 
     * @return User|null The user or null if not found.
     */
    public function find(int $id, array $select = ['id', 'name', 'email', 'created_at']): User|null
    {
        return User::select($select)->find($id);
    }

    /**
     * Create a new user with the provided data.
     *
     * @param array $data The data to create the user with.
     * 
     * @return User|null The created user or null if creation failed.
     */
    public function create(array $data): User|null
    {
        return User::create($data);
    }

    /**
     * Update a user based on the provided ID and data.
     *
     * @param int $id The ID of the user to update.
     * @param array $data The data to update the user with.
     * 
     * @return User|null The updated user or null if update failed.
     */
    public function update(int $id, array $data): User|null
    {
        $user = User::find($id);

        if ($user) {
            $user->update($data);
            return $user;
        }

        return null;
    }

    /**
     * Delete a user based on the provided ID.
     *
     * @param int $id The ID of the user to delete.
     * 
     * @return bool True if the user was deleted, false otherwise.
     */
    public function delete(int $id): bool
    {
        return User::destroy($id);
    }
}
