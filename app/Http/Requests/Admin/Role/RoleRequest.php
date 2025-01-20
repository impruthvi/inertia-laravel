<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Role;

use App\Enums\AdminRoleEnum;
use App\Rules\UniqueAdminRoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255', new UniqueAdminRoleName],
            'roles' => ['required', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'display_name.required' => trans('validation.required', ['attribute' => 'role name']),
            'roles.required' => trans('validation.select_one'),
            'roles.array' => trans('validation.select_one'),
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $this->replace([
            'display_name' => $this->display_name,
            'permissions' => $this->getPermissions(),
        ]);
    }

    /**
     * Get permissions based on the roles selected.
     *
     * @return array<string>
     */
    private function getPermissions(): array
    {
        $permissions = [];

        // Ensure $this->roles is an array and not empty before iterating
        if (is_array($this->roles) && $this->roles !== []) {
            foreach ($this->roles as $key => $permission_prefixes) {
                if ($permission_prefixes) {
                    $rolePermissions = role_permissions($this->getRoleName());
                    if (array_key_exists($key - 1, $rolePermissions)) {
                        // Ensure correct string concatenation
                        $permissions[] = 'access_'.$rolePermissions[$key - 1]['route_prefix'];
                        // @phpstan-ignore foreach.nonIterable
                        foreach ($permission_prefixes as $permission_prefix) {
                            if (in_array($permission_prefix, $rolePermissions[$key - 1]['permissions'])) {
                                // @phpstan-ignore-next-line
                                $permissions[] = $permission_prefix.'_'.$rolePermissions[$key - 1]['route_prefix'];
                            }
                        }
                    }
                }
            }
        }

        return $permissions;
    }

    /**
     * Get the role name of the authenticated user.
     */
    private function getRoleName(): string
    {
        /** @var \App\Models\Admin $user */
        $user = Auth::user();

        if (property_exists($user, 'role') && $user->role !== null) {
            return (string) $user->role;
        }

        return AdminRoleEnum::ADMIN->value;
    }
}
