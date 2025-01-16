<?php

namespace App\Pipelines\Role;

use Closure;

class SortPipeline
{
    public function __construct(protected $filter) {}

    public function handle($roles, Closure $next)
    {
        $filter = $this->filter;
        if (! empty($filter['sort'])) {
            $allowedSortFields = ['display_name'];
            $allowedSortDirections = ['asc', 'desc'];

            foreach ($filter['sort'] as $field => $direction) {
                if (in_array($field, $allowedSortFields) && in_array(strtolower($direction), $allowedSortDirections)) {
                    $roles->orderBy($field, $direction);
                }
            }
        } else {
            $roles->orderBy('id');
        }

        return $next($roles);
    }
}
