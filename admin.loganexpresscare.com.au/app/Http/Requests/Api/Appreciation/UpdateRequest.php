<?php

namespace App\Http\Requests\Api\Appreciation;

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
            'date' => 'required',
            'user_id' => 'required',
        ];

        if ($this->price_amount && (float) $this->price_amount > 0) {
            $rules['account_id'] = 'required';
        };

        if ($this->letterhead_template_id && $this->letterhead_template_id != '') {
            $rules['letterhead_title'] = 'required';
            $rules['letterhead_description'] = 'required';
        };

        return $rules;
    }
}