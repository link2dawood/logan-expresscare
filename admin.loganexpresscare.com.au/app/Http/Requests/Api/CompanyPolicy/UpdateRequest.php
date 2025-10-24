<?php

namespace App\Http\Requests\Api\CompanyPolicy;

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
			'location_id'    => 'required',
			'title'    => 'required',
			'description'    => 'required',
		];

		if ($this->method_type == 'upload') {
			$rules['policy_document'] = 'required';
		}

		if ($this->method_type == 'create') {
			$rules['letter_description'] = 'required';
		}

		return $rules;
	}
}
