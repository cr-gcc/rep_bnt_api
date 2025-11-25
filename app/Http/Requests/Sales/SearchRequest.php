<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'start_date_sale' => ['nullable', 'date', 'required_with:end_date_sale'],
			'end_date_sale' => ['nullable', 'date', 'after_or_equal:start_date_sale', 'required_with:start_date_sale'],
			'start_date_val' => ['nullable', 'date', 'required_with:end_date_val'],
			'end_date_val' => ['nullable', 'date', 'after_or_equal:start_date_val', 'required_with:start_date_val'],
			'campaigns' => ['required', 'array'],
			'status' => ['required', 'array'],
			'sig' => ['nullable', 'string', 'required_without:policy'],
			'policy' => ['nullable', 'string', 'required_without:sig'],
		];
	}

	public function messages(): array
	{
		return [
			'start_date_sale.date' => 'La fecha de inicio de venta debe ser una fecha válida.',
			'end_date_sale.date' => 'La fecha de fin de venta debe ser una fecha válida.',
			'end_date_sale.after_or_equal' => 'La fecha de fin de venta debe ser igual o posterior a la fecha de inicio.',
			'start_date_sale.required_with' => 'La fecha de inicio de venta es obligatoria cuando hay fecha de fin.',
			'end_date_sale.required_with' => 'La fecha de fin de venta es obligatoria cuando hay fecha de inicio.',

			'start_date_val.date' => 'La fecha de inicio de validación debe ser una fecha válida.',
			'end_date_val.date' => 'La fecha de fin de validación debe ser una fecha válida.',
			'end_date_val.after_or_equal' => 'La fecha de fin de validación debe ser igual o posterior a la fecha de inicio.',
			'start_date_val.required_with' => 'La fecha de inicio de validación es obligatoria cuando hay fecha de fin.',
			'end_date_val.required_with' => 'La fecha de fin de validación es obligatoria cuando hay fecha de inicio.',

			'campaigns.required' => 'Debe seleccionar al menos una campaña.',
			'campaigns.array' => 'Las campañas deben enviarse como un arreglo.',

			'status.required' => 'Debe seleccionar al menos un estatus.',
			'status.array' => 'Los estatus deben enviarse como un arreglo.',

			'sig.required_without' => 'Debe ingresar un SIG o una póliza.',
			'policy.required_without' => 'Debe ingresar un SIG o una póliza.',
		];
	}
}
