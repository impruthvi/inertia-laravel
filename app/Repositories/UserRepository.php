<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserInterface
{
    public function get(array $select = ['name', 'email', 'created_at'],array $filters = [], $paginate = true): LengthAwarePaginator|Collection|null
    {
        // Start building the query
        $query = User::query();
        $record_per_page = config('utility.record_per_page');

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply sorting
        if (!empty($filters['sort'])) {
            $allowedSortFields = ['name', 'email', 'created_at'];
            $allowedSortDirections = ['asc', 'desc'];

            foreach ($filters['sort'] as $field => $direction) {
                if (in_array($field, $allowedSortFields) && in_array(strtolower($direction), $allowedSortDirections)) {
                    $query->orderBy($field, $direction);
                }
            }
        }

        // Apply pagination
        if ($paginate) {
            return $query->paginate($record_per_page)->appends($filters);
        }

        $users = $query->get();

        return $users;
    }
}
