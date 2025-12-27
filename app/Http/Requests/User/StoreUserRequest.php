<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users',
            'user' => 'required|string|max:255|unique:users',
            'birth_date' => 'required|date',
						'role_id' => 'required|exists:roles,id',
        ];
    }

		public function messages(): array
		{
			return [
				'name.required' => 'El nombre es obligatorio',
				'last_name.required' => 'El apellido es obligatorio',
				'email.email' => 'El email debe ser un email valido',
				'email.max' => 'El email debe tener maximo 255 caracteres',
				'email.unique' => 'El email ya se encuentra registrado',
				'user.required' => 'El usuario es obligatorio',
				'user.max' => 'El usuario debe tener maximo 255 caracteres',
				'user.unique' => 'El usuario ya se encuentra registrado',
				'birth_date.required' => 'La fecha de nacimiento es obligatoria',
				'role_id.required' => 'El rol es obligatorio',
			];
		}
}
