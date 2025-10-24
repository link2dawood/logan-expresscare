<?php

namespace App\Http\Requests\Api\Feedback;

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
			'title' => 'required',
			'last_date' => 'required',
		];

		if ($this->has('visible_to') && $this->visible_to == 0) {
			if (count($this->user_id) == 0) {
				$rules['user_id'] = 'required';
			}
		}
		if (count($this->feedback_form_fields) == 0) {
			$rules['feedback_form_fields'] = 'required';
		}

		return $rules;
	}
}
