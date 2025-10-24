<?php

namespace App\Http\Requests\Api\Attendance;

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
            'user_id' => 'required',
            'date' => 'required',
            'clock_in_time' => 'required',
            'clock_out_time' => 'required',
        ];

        if ($this->is_half_day == 1) {
            $rules['leave_type_id'] = 'required';
            $rules['reason'] = 'required';
        }

        return $rules;
    }
}
