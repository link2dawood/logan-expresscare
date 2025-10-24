<?php

namespace App\Http\Requests\Api\Shift;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Validator;
use Carbon\Carbon;

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
            'name' => 'required',
            'clock_in_time' => 'required',
            'clock_out_time' => 'required',
            'self_clocking' => 'required',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->clock_in_time && $this->clock_out_time) {
                $clockIn = Carbon::parse($this->clock_in_time);
                $clockOut = Carbon::parse($this->clock_out_time);

                // Get margins (default to 0 if null)
                $earlyClockIn = (int) ($this->early_clock_in_time ?? 0);
                $allowClockOutTill = (int) ($this->allow_clock_out_till ?? 0);

                // Adjust clock-in and clock-out
                $adjustedClockIn = $clockIn->copy()->subMinutes($earlyClockIn);
                $adjustedClockOut = $clockOut->copy()->addMinutes($allowClockOutTill);
                // Handle overnight shift
                if ($adjustedClockOut->lessThanOrEqualTo($adjustedClockIn)) {
                    $adjustedClockOut->addDay();
                }
                
                $durationInSeconds = $adjustedClockIn->diffInSeconds($adjustedClockOut);
             
                if ($durationInSeconds >= 86399 || $durationInSeconds <= 0) {
                    $validator->errors()->add('shift_duration', 'Total shift duration including early clock in time and allow clock out time must be less than 24 hours.');
                }
            }
        });
    }
}
