<?php

namespace App\Http\Requests\Api\Leave;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendaceRequest extends FormRequest
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
            'department_id' => 'required',
            'employee_id' => 'required',
            'half_day' => 'required',
            'clock_in' => 'required',
            'clock_out' => 'required',
        ];

        return $rules;
    }
}
