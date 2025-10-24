<?php

namespace App\Http\Requests\Api\Deposit;

use Illuminate\Foundation\Http\FormRequest;

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
            'account_id' => 'required',
            'amount' => 'required',
            'date_time' => 'required',
            'payer_id' => 'required',
            'deposit_category_id' => 'required',
        ];

        return $rules;
    }
}