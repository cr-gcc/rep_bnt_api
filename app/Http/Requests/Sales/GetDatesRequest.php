<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class GetDatesRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'start_date' => ['required', 'date'],
			'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
		];
	}

	public function messages()
	{
		return [
			'start_date.required' => 'La fecha de inicio es obligatoria.',
			'start_date.date'     => 'La fecha de inicio debe ser una fecha válida.',
			'end_date.required'   => 'La fecha final es obligatoria.',
			'end_date.date'       => 'La fecha final debe ser una fecha válida.',
			'end_date.after'      => 'La fecha final debe ser posterior a la fecha de inicio.',
		];
	}
}
