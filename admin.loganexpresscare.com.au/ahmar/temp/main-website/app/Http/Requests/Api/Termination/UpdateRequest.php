<?php

namespace App\Http\Requests\Api\Termination;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'user_id' => 'required',
			'title' => 'required',
			'description' => 'required',
			'start_date' => 'required',
			'end_date' => 'required'
		];

		if ($this->letterhead_template_id && $this->letterhead_template_id != '') {
			$rules['letterhead_title'] = 'required';
			$rules['letterhead_description'] = 'required';
		};

		return $rules;
	}
}
