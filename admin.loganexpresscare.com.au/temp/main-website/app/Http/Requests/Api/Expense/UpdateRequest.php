<?php

namespace App\Http\Requests\Api\Expense;

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
            'expense_category_id' => 'required',
            'date_time' => 'required',
            'amount' => 'required|numeric',
            'expense_type' => 'required',
            'payment_status' => 'required',
            'status' => 'required|in:approved,rejected'
        ];

        if ($this->status == 'approved') {
            if ($this->payment_status == '1') {
                $rules['account_id'] = 'required';
                $rules['payment_date'] = 'required';
            } else {
                $rules['payroll_month'] = 'required';
                $rules['payroll_year'] = 'required';
            }

            if ($this->expense_type == 'company_claims') {
                $rules['payee_id'] = 'required';
            } else if ($this->expense_type == 'employee_claims') {
                $rules['user_id'] = 'required';
            }
        }

        return $rules;
    }
}
