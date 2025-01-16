<?php

namespace App\Pipelines\Role;

use Closure;

class SearchPipeline
{
    public function __construct(protected $filter) {}

    public function handle($roles, Closure $next)
    {
        $search_keyword = isset($this->filter['search'])
            ? $this->filter['search']
            : null;

        if (! empty($search_keyword)) {
            $roles->where(function ($q) use ($search_keyword) {
                $q->where('display_name', 'like', '%' . $search_keyword . '%');
            });
        }

        return $next($roles);
    }
}
