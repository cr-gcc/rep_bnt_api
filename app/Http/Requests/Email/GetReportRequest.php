<?php

namespace App\Http\Requests\Email;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GetReportRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Reglas base
	 */
	public function rules(): array
	{
		return [
			'start_date' => ['required', 'date'],
			'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
			'type'       => ['required', 'string', 'in:list,totals'],
			'schedule'   => ['required', 'string', 'in:0,24,48'],
			'campaigns'  => ['required', 'array'],
			'campaigns.*' => ['required', 'integer'],
		];
	}

	/**
	 * Validaciones condicionales
	 */
	public function withValidator(Validator $validator): void
	{
		$validator->sometimes(
			'campaigns.*',
			'exists:campaigns,id',
			function ($input) {
				// Solo valida exists si el valor es mayor a 0
				return collect($input->campaigns)
					->filter(fn($v) => (int) $v > 0)
					->isNotEmpty();
			}
		);
	}

	public function messages(): array
	{
		return [
			'start_date.required' => 'La fecha de inicio es obligatoria.',
			'start_date.date'     => 'La fecha de inicio debe ser una fecha válida.',
			'end_date.required'   => 'La fecha final es obligatoria.',
			'end_date.date'       => 'La fecha final debe ser una fecha válida.',
			'end_date.after_or_equal' => 'La fecha final debe ser posterior o igual a la fecha de inicio.',
			'type.required'            => 'El tipo es obligatorio.',
			'type.in'                  => 'El tipo debe ser Listado o Totales.',
			'schedule.required'   => 'El horario es obligatorio.',
			'schedule.string'     => 'El horario debe ser una cadena de texto.',
			'schedule.in'         => 'El horario debe ser 0, 24 o 48 horas.',
			'campaigns.required'  => 'Las campañas son obligatorias.',
			'campaigns.array'     => 'Las campañas deben ser un arreglo.',
			'campaigns.*.integer' => 'Cada campaña debe ser un ID entero.',
			'campaigns.*.exists'  => 'La campaña seleccionada no existe en la base de datos.',
		];
	}
}
