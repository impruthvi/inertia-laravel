<?php

namespace App\Rules;

use App\Enums\AdminRoleEnum;
use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class UniqueAdminRoleName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        // Check if the user exists and has the "role" property
        if ($user && property_exists($user, 'role') && $user->role === AdminRoleEnum::ADMIN->value) {
            /** @var Builder<Role> $roleQuery */
            $roleQuery = Role::query();

            $roleQuery->where('display_name', $value)
                ->whereHas('createdBy', function (Builder $query) {
                    $query->where('role', AdminRoleEnum::ADMIN->value);
                });

            // Exclude the current role if updating
            if (request()->route('role')) {
                $roleQuery->where('id', '!=', request()->route('role'));
            }

            $role = $roleQuery->first();

            if ($role) {
                $fail(__('messages.unique_admin_role'));
            }
        }
    }
}
