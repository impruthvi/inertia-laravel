<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AdminRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:strict', 'max:255', 'unique:admins,email,' . $this->id],
            'role_id' => ['required'],
            'custom_permission' => ['required', 'array'],
            'custom_permission.*' => ['required'],
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
            'role_id.required' => trans('validation.required', ['attribute' => 'role permission']),
            'custom_permission.required' => trans('validation.required', ['attribute' => 'permission']),
        ];
    }

    

    public function validated($key = null, $default = null)
    {
        $user = Auth::user();
        $validateData = data_get($this->validator->validated(), $key, $default);

        $validateData += [
            'role' => $user->role,
            'custom_permissions' => array_to_permission($this->custom_permission, $user->role),
            'name' => $this->first_name . ' ' . $this->last_name,
        ];

        return $validateData;
    }
}
