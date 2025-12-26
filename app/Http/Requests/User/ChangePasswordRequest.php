<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'password' => 'required|string|min:8',
						'password_confirmation' => 'required|string|min:8|same:password',
        ];
    }

		public function messages(): array
		{
			return [
				'password.required' => 'El campo contraseña es obligatorio',
				'password_confirmation.required' => 'El campo confirmación de contraseña es obligatorio',
				'password_confirmation.same' => 'Las contraseñas no coinciden',
			];
		}
}
