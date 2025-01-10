<?php

namespace App\Pipelines\User;

use Closure;

class SearchPipeline
{
    public function __construct(protected $filter) {}

    public function handle($users, Closure $next)
    {
        $search_keyword = isset($this->filter['search'])
            ? $this->filter['search']
            : null;

        if (! empty($search_keyword)) {
            $users->where(function ($q) use ($search_keyword) {
                $q->where('name', 'like', '%' . $search_keyword . '%')
                    ->orWhere('email', 'like', '%' . $search_keyword . '%');
            });
        }

        return $next($users);
    }
}
