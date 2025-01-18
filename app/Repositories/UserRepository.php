<?php

declare(strict_types=1);

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
     * @param array<int, string> $select
     * @param array<string, mixed> $filters
     * @param bool $paginate
     * @return LengthAwarePaginator<User>|Collection<int, User>|null
     */
    public function get(array $select = ['id', 'name', 'email', 'created_at'], array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
    {
        // Start building the query
        $query = User::select($select);
        $record_per_page = filter_var(config('utility.record_per_page', 10), FILTER_VALIDATE_INT) ?: 10;
        $users = app(Pipeline::class)
            ->send($query)
            ->through([
                new SearchPipeline($filters),
                new SortPipeline($filters),
            ])
            ->thenReturn();

        /** @var \Illuminate\Database\Eloquent\Builder<User> $users */
        if ($paginate) {
            return $users->paginate($record_per_page)->appends($filters);
        }

        return $users->get();
    }

    /**
     * @param string $id
     * @param array<int, string> $select
     * @return User|null
     */
    public function find(string $id, array $select = ['id', 'name', 'email', 'created_at']): User|null
    {
        return User::select($select)->find($id);
    }

    /**
     * @param array<string, mixed> $data
     * @return User|null
     */
    public function create(array $data): User|null
    {
        return User::create($data);
    }

    /**
     * @param string $id
     * @param array<string, mixed> $data
     * @return User|null
     */
    public function update(string $id, array $data): User|null
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
     * @param string $id The ID of the user to delete.
     * 
     * @return bool True if the user was deleted, false otherwise.
     */
    public function delete(string $id): bool
    {
        return User::destroy($id) > 0;
    }
}
