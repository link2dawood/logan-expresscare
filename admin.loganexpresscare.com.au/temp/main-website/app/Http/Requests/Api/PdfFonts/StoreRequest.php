<?php

namespace App\Http\Requests\Api\PdfFonts;

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
            'use_otl' => 'required|integer|in:0,1,255',
            'user_kashida' => 'required|integer|between:0,100',
            'file' => 'required'
        ];

        return $rules;
    }
}
