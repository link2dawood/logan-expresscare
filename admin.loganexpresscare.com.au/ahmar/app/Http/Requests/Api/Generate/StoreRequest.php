<?php

namespace App\Http\Requests\Api\Generate;

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
            'user_id' => 'required',
            'letterhead_template_id' => 'required',
        ];

        if ($this->letterhead_template_id && $this->letterhead_template_id != '') {
            $rules['title'] = 'required';
            $rules['description'] = 'required';
        };

        return $rules;
    }
}
