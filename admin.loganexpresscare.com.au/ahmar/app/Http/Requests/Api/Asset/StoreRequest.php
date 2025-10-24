<?php

namespace App\Http\Requests\Api\Asset;

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
            'name' => 'required',
            'asset_type_id' => 'required',
            'status' => 'required',
            'location_id' => 'required',
        ];

        if ($this->has('price') && ($this->price != null && $this->price > "0")) {
            $rules['account_id'] = 'required';
        };

        return $rules;
    }
}
