<?php

declare(strict_types=1);

namespace App\Pipelines\Admin;

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
        $searchKeyword = $this->filter['search'] ?? null;

        if (!empty($searchKeyword) && is_string($searchKeyword)) {
            $roles->where(function (Builder $q) use ($searchKeyword) {
                $q->where('name', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('email', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('role', 'like', '%' . $searchKeyword . '%');
            });
        }

        return $next($roles);
    }
}
