<?php

declare(strict_types=1);

namespace App\Pipelines\User;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class SortPipeline
{
    /**
     * @param  array<string, mixed>  $filter
     */
    public function __construct(protected array $filter) {}

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $users
     * @param  Closure(Builder<TModel>): Builder<TModel>  $next
     * @return Builder<TModel>
     */
    public function handle(Builder $users, Closure $next): Builder
    {
        $filter = $this->filter;

        if (! empty($filter['sort']) && is_array($filter['sort'])) {
            $allowedSortFields = ['name', 'email', 'created_at'];
            $allowedSortDirections = ['asc', 'desc'];

            foreach ($filter['sort'] as $field => $direction) {
                if (
                    is_string($field) &&
                    in_array($field, $allowedSortFields, true) &&
                    is_string($direction) &&
                    in_array(mb_strtolower($direction), $allowedSortDirections, true)
                ) {
                    $users->orderBy($field, mb_strtolower($direction));
                }
            }
        } else {
            $users->orderBy('id');
        }

        return $next($users);
    }
}
