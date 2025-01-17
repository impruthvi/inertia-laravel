<?php

namespace App\Http\Requests\Admin\Role;

use App\Enums\AdminRoleEnum;
use App\Rules\UniqueAdminRoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoleRequest extends FormRequest
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
     * @return array
     */
    public function messages()
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

    private function getPermissions(): array
    {
        $permissions = [];
        foreach ($this->roles as $key => $permission_prefixes) {
            if ($permission_prefixes) {
                if (array_key_exists(($key - 1), role_permissions($this->getRoleName()))) {
                    array_push($permissions, 'access_' . role_permissions($this->getRoleName())[$key - 1]['route_prefix']);
                    foreach ($permission_prefixes as $permission_prefix) {
                        if (in_array($permission_prefix, role_permissions($this->getRoleName())[$key - 1]['permissions'])) {
                            array_push($permissions, $permission_prefix . '_' . role_permissions($this->getRoleName())[$key - 1]['route_prefix']);
                        }
                    }
                }
            }
        }

        return $permissions;
    }

    private function getRoleName()
    {

        /**
         * 
         */
        switch (Auth::user()->role) {

            default:
                return AdminRoleEnum::ADMIN->value;
        }
    }
}
