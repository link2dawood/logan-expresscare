<?php

namespace App\Http\Requests\Api\Leave;

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
        $rules  = [
            'start_date' => 'required',
            'end_date' => 'required',
            'is_half_day' => 'required',
            'reason' => 'required',
            'leave_type_id' => 'required',
        ];

        $loggedUser = user();
        if ($loggedUser->ability('admin', 'leaves_approved_rejected')) {
            $rules['status'] = 'required|in:pending,approved,rejected';
        }

        if ($loggedUser->ability('admin', 'leaves_edit')) {
            $rules['user_id'] = 'required';
        }

        return $rules;
    }
}
