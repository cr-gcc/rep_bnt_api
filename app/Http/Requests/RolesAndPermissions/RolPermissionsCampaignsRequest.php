<?php

namespace App\Http\Requests\RolesAndPermissions;

use Illuminate\Foundation\Http\FormRequest;

class RolPermissionsCampaignsRequest extends FormRequest
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
            'role_id' => ['required', 'integer'],
						'permissions_id' => ['required', 'array'],
						'permissions_id.*' => ['integer', 'exists:permissions,id'],
            'campaigns_id' => ['required', 'array'],
            'campaigns_id.*' => ['integer', 'exists:campaigns,id'],
        ];
    }

		public function messages(): array
		{
			return [
				'role_id.required' => 'El rol es obligatorio.',
				'permissions_id.required' => 'El/los permiso/s es obligatorio.',
				'permissions_id.array' => 'El permiso debe ser un array.',
				'permissions_id.*.integer' => 'El permiso debe ser un número entero.',
				'permissions_id.*.exists' => 'El permiso no existe.',
				'campaigns_id.required' => 'Las campañas son obligatoria.',
				'campaigns_id.array' => 'Las campañas deben ser un array.',
				'campaigns_id.*.integer' => 'Las campañas deben ser un número entero.',
				'campaigns_id.*.exists' => 'La/las campaña/s no existe/n.',
			];
		}
}
