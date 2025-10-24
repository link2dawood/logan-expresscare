<?php

namespace App\Http\Requests\Api\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeResponse extends FormRequest
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

        $rules = [];

        if (count($this->feedback_form_fields) > 0) {
            $feedbackFormFields = $this->feedback_form_fields;
            foreach ($feedbackFormFields as $fields) {

                if (count($fields) <= 4 || $fields['reply'] == null || $fields['reply'] == '') {
                    $rules['answer'] = 'required';
                }
            }
        }


        return $rules;
    }
}