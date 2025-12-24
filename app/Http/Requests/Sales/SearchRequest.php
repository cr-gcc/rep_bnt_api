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
			'sale_date' => ['nullable', 'date'],
			'campaigns' => ['required', 'array'],
			'status' => ['required', 'array'],
		];
	}

	public function messages(): array
	{
		return [
			'sale_date' => 'La fecha de venta debe ser una fecha válida.',
			'campaigns.required' => 'Debe seleccionar al menos una campaña.',
			'campaigns.array' => 'Las campañas deben enviarse como un arreglo.',
			'status.required' => 'Debe seleccionar al menos un estatus.',
			'status.array' => 'Los estatus deben enviarse como un arreglo.'
		];
	}
}
