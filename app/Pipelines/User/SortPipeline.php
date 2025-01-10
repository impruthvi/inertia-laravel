<?php

namespace App\Pipelines\User;

use Closure;

class SortPipeline
{
    public function __construct(protected $filter) {}

    public function handle($users, Closure $next)
    {
        $filter = $this->filter;
        if (! empty($filter['sort'])) {
            $allowedSortFields = ['name', 'email', 'created_at'];
            $allowedSortDirections = ['asc', 'desc'];

            foreach ($filter['sort'] as $field => $direction) {
                if (in_array($field, $allowedSortFields) && in_array(strtolower($direction), $allowedSortDirections)) {
                    $users->orderBy($field, $direction);
                }
            }
        } else {
            $users->orderBy('id');
        }

        return $next($users);
    }
}
