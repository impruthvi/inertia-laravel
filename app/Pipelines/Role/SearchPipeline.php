<?php

namespace App\Pipelines\Role;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SearchPipeline
{
    /**
     * @param array<string, mixed> $filter
     */
    public function __construct(protected array $filter) {}

    /**
     * @template TModel of Model
     * @param Builder<TModel> $roles
     * @param Closure(Builder<TModel>): Builder<TModel> $next
     * @return Builder<TModel>
     */
    public function handle(Builder $roles, Closure $next): Builder
    {
        // Ensure 'search' is a string if set
        $search_keyword = isset($this->filter['search']) && is_string($this->filter['search'])
            ? $this->filter['search']
            : null;

        if (!empty($search_keyword)) {
            $roles->where(function ($q) use ($search_keyword) {
                // Perform the search query
                $q->where('display_name', 'like', '%' . $search_keyword . '%');
            });
        }

        return $next($roles);
    }
}
