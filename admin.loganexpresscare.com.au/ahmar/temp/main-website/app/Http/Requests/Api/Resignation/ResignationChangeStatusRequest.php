<?php

namespace App\Http\Requests\Api\Resignation;

use Illuminate\Foundation\Http\FormRequest;

class ResignationChangeStatusRequest extends FormRequest
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
            'status' => 'required',
		];
        if($this->status == 'approved'){
            $rules['end_date'] ='required';
        }
		return $rules;
	}
}
