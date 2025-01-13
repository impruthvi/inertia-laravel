<?php

namespace App\Rules;

use App\Enums\AdminRoleEnum;
use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class UniqueAdminRoleName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Auth::user()->role === AdminRoleEnum::ADMIN->value) {
            $role = Role::where('display_name', $value)->whereHas('createdBy', function ($query) {
                $query->where('role', AdminRoleEnum::ADMIN->value);
            });

            if (request()->route('role')) {
                $role->where('id', '!=', request()->route('role'));
            }
            $role = $role->first();

            if ($role) {
                $fail('messages.unique_admin_role')->translate();
            }
        }
    }
}
