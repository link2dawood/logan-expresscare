<?php

namespace App\Http\Requests\Api\HrmSettings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHrmSettingsRequest extends FormRequest
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
        return [
            'clock_in_time' => 'required',
            'clock_out_time' => 'required',
            'leave_start_month' => 'required',
            'self_clocking' => 'required'
        ];
    }
}
