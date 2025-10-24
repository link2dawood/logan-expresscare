<?php

namespace App\Http\Requests\Api\Leave;

use Illuminate\Foundation\Http\FormRequest;

class RemainingLeavesRequest extends FormRequest
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
            'year' => 'required',
            'user_id' => 'required',
        ];

        return $rules;
    }
}
