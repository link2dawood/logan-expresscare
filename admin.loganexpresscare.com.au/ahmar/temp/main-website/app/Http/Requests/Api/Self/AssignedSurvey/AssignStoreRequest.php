<?php

namespace App\Http\Requests\Api\Self\AssignedSurvey;

use Illuminate\Foundation\Http\FormRequest;

class AssignStoreRequest extends FormRequest
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

        if (count($this->feedback) > 0) {
            $feedback = $this->feedback;
            $feedbackFormFields = $feedback['feedback_form_fields'];

            foreach ($feedbackFormFields as $fields) {
                if ($fields && isset($fields['reply'])) {
                    if ($fields['reply'] == '') {
                        $rules['reply'] = 'required';
                    }
                } else {
                    $rules['reply'] = 'required';
                }
            }
        }

        return $rules;
    }
}
