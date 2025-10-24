<?php

namespace App\Http\Requests\Api\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
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
            'status' => 'required|in:approved,rejected'
        ];

        if ($this->status == 'approved') {
            if ($this->payment_status == "1") {
                $rules['payment_date'] = 'required';
                $rules['account_id'] = 'required';
            } else {
                $rules['payroll_month'] = 'required';
                $rules['payroll_year'] = 'required';
            }
        }


        return $rules;
    }
}
