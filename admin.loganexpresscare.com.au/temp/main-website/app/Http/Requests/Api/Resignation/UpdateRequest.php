<?php

namespace App\Http\Requests\Api\Resignation;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Validator;
use Carbon\Carbon;

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
			'status' => 'required',
			'start_date' => 'required',
			'end_date' => 'required'
		];

		if ($this->letterhead_template_id && $this->letterhead_template_id != '') {
			$rules['letterhead_title'] = 'required';
			$rules['letterhead_description'] = 'required';
		};

		return $rules;
	}

	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			if ($this->status === 'approved' && $this->start_date && $this->end_date) {
				$startDate = Carbon::parse($this->start_date);
				$endDate = Carbon::parse($this->end_date);

				if ($endDate->lt($startDate)) {
					$validator->errors()->add('last_date', 'Last Working Date must be later date than Resignation Date');
				}
			}
		});
	}
}
