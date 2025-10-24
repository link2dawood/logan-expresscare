<?php

namespace App\Http\Requests\Api\Payroll;

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
            'payroll_status' => 'required',
            'custom_earnings.*.name' => 'required|string|min:1',
            'custom_earnings.*.monthly' => 'required|numeric|min:0.01',
            'custom_deductions.*.name' => 'required|string|min:1',
            'custom_deductions.*.monthly' => 'required|numeric|min:0.01',
            'additional_earnings.*.name' => 'required|string|min:1',
            'additional_earnings.*.monthly' => 'required|numeric|min:0.01'
        ];

        if ($this->payroll_status == "paid") {
            $rules['payment_date'] = 'required';
            $rules['account_id'] = 'required';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'custom_earnings.*.name.required' => 'The earning name is required.',
            'custom_earnings.*.name.min' => 'The earning name must be at least 1 character.',
            'custom_earnings.*.monthly.required' => 'The earning amount is required.',
            'custom_earnings.*.monthly.numeric' => 'The earning amount must be a number.',
            'custom_earnings.*.monthly.min' => 'The earning amount must be greater than 0.',

            'custom_deductions.*.name.required' => 'The deduction name is required.',
            'custom_deductions.*.name.min' => 'The deduction name must be at least 1 character.',
            'custom_deductions.*.monthly.required' => 'The deduction amount is required.',
            'custom_deductions.*.monthly.numeric' => 'The deduction amount must be a number.',
            'custom_deductions.*.monthly.min' => 'The deduction amount must be greater than 0.',

            'additional_earnings.*.name.required' => 'The additional earning name is required.',
            'additional_earnings.*.name.min' => 'The additional earning name must be at least 1 character.',
            'additional_earnings.*.monthly.required' => 'The additional earning amount is required.',
            'additional_earnings.*.monthly.numeric' => 'The additional earning amount must be a number.',
            'additional_earnings.*.monthly.min' => 'The additional earning amount must be greater than 0.'
        ];
    }
}
