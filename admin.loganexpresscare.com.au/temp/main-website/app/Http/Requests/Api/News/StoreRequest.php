<?php

namespace App\Http\Requests\Api\News;

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
            'title' => 'required',
            'description' => 'required',
        ];

        if ($this->has('visible_to_all') && $this->visible_to_all == 0) {
            if (count($this->user_id) == 0) {
                $rules['user_id'] = 'required';
            }
        }

        return $rules;
    }
}
