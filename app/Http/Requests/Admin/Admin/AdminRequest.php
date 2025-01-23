<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class AdminRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            // @phpstan-ignore-next-line
            'email' => ['required', 'string', 'email:strict', 'max:255', 'unique:admins,email,'.$this->id],
            'role_id' => ['required'],
            'custom_permission' => ['required', 'array'],
            'custom_permission.*' => ['required'],
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
            'role_id.required' => trans('validation.required', ['attribute' => 'role permission']),
            'custom_permission.required' => trans('validation.required', ['attribute' => 'permission']),
        ];
    }

    public function validated($key = null, $default = null)
    {
        /**
         * @var \App\Models\Admin $user
         */
        $user = auth('admin')->user();
        $validateData = data_get($this->validator->validated(), $key, $default);

        return (array) $validateData + [
            'role' => $user->role,
            // @phpstan-ignore-next-line
            'custom_permissions' => array_to_permission($this->custom_permission, $user->role),
        ];
    }
}
