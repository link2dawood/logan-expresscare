<?php

namespace App\Http\Requests\Api\Attendance;

use Illuminate\Foundation\Http\FormRequest;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\Validator;
// use Carbon\Carbon;

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

    //     public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         $userXid = $this->input('user_id');
    //         $userId = $this->getIdFromHash($userXid);
    //         $date = $this->input('date');
    //         $clockInTimeInput = $this->input('clock_in_time');
    //         $clockOutTimeInput = $this->input('clock_out_time');

    //         // 🧠 Fetch user's shift from DB
    //         $users = DB::table('users')->with('shift')
    //             ->find($userId);
    //         $userShift = $users->shift;

    //         if ($userShift) {
    //             $shiftDate = Carbon::parse($date)->startOfDay();

    //             $shiftClockIn = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $userShift->clock_in_time);
    //             $shiftClockOut = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $userShift->clock_out_time);

    //             if (!$shiftClockOut->gt($shiftClockIn)) {
    //                 $shiftClockOut->addDay(); // Crosses midnight
    //             }

    //             $minAllowedClockIn = $shiftClockIn->copy()->subMinutes($userShift->early_clock_in_time);
    //             $maxAllowedClockOut = $shiftClockOut->copy()->addMinutes($userShift->allow_clock_out_till);

    //             // Parse user's input clock times
    //             $inputClockIn = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $clockInTimeInput);
    //             $inputClockOut = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $clockOutTimeInput);

    //             // Adjust if before shift range
    //             if ($inputClockIn->lt($minAllowedClockIn)) {
    //                 $tryNextDay = $inputClockIn->copy()->addDay();
    //                 if ($tryNextDay->lte($maxAllowedClockOut)) {
    //                     $inputClockIn = $tryNextDay;
    //                 }
    //             }

    //             if ($inputClockOut->lt($minAllowedClockIn)) {
    //                 $tryNextDay = $inputClockOut->copy()->addDay();
    //                 if ($tryNextDay->lte($maxAllowedClockOut)) {
    //                     $inputClockOut = $tryNextDay;
    //                 }
    //             }

    //             // Validate time ranges
    //             if ($inputClockIn->lt($minAllowedClockIn) || $inputClockIn->gt($maxAllowedClockOut)) {
    //                 $validator->errors()->add('clock_in_time', 'Clock-in time must be within allowed shift time.');
    //             }

    //             if ($inputClockOut->lt($minAllowedClockIn) || $inputClockOut->gt($maxAllowedClockOut)) {
    //                 $validator->errors()->add('clock_out_time', 'Clock-out time must be within allowed shift time.');
    //             }
    //         }
    //     });
    // }
}
