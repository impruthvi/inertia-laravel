<?php

declare(strict_types=1);

namespace App\Pipelines\User;

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
     * @param Builder<TModel> $users
     * @param Closure(Builder<TModel>): Builder<TModel> $next
     * @return Builder<TModel>
     */
    public function handle(Builder $users, Closure $next): Builder
    {
        $searchKeyword = $this->filter['search'] ?? null;

        if (!empty($searchKeyword) && is_string($searchKeyword)) {
            $users->where(function (Builder $q) use ($searchKeyword) {
                $q->where('name', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('email', 'like', '%' . $searchKeyword . '%');
            });
        }

        return $next($users);
    }
}
