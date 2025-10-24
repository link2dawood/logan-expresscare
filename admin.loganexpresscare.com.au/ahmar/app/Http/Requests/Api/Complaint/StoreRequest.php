<?php

namespace App\Http\Requests\Api\Complaint;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
			'date_time' => 'required',
			'from_user_id' => 'required',
			'to_user_id' => 'required'
		];

		if ($this->letterhead_template_id && $this->letterhead_template_id != '') {
			$rules['letterhead_title'] = 'required';
			$rules['letterhead_description'] = 'required';
		};

		return $rules;
	}
}
