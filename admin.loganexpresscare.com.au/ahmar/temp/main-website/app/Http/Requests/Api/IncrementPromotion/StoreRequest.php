<?php

namespace App\Http\Requests\Api\IncrementPromotion;

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
            'type' => 'required',
            'date' => 'required',
            'description' => 'required',
        ];

        if ($this->type == "increment" || $this->type == "decrement") {
            $rules['net_salary'] = 'required';
        }

        if ($this->type == "promotion") {
            $rules['promoted_designation_id'] = 'required';
            $rules['current_designation_id'] = 'required';
        }

        if ($this->type == "increment_promotion" || $this->type == "decrement_demotion") {
            $rules['promoted_designation_id'] = 'required';
            $rules['current_designation_id'] = 'required';
            $rules['net_salary'] = 'required';
        }

        if ($this->letterhead_template_id && $this->letterhead_template_id != '') {
            $rules['letterhead_title'] = 'required';
            $rules['letterhead_description'] = 'required';
        };


        return $rules;
    }
}
