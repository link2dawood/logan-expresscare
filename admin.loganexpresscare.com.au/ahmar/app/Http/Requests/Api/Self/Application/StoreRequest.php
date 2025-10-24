<?php

namespace App\Http\Requests\Api\Self\Application;

use App\Models\Opening;
use App\Classes\Common;
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
            'applicant_name' => 'required',
            'contact_email' => 'required',
            'opening_id' => 'required',
            'phone_number' => 'required',
        ];

        if ($this->has('opening_id')) {
            $openingId = Common::getIdFromHash($this->opening_id);
            $opening = Opening::find($openingId);

            if (isset($opening)) {

                if ($opening['gender'] == true) {
                    if ($this->has('gender') && ($this->gender == null || $this->gender == "")) {
                        $rules['gender'] = 'required';
                    }
                }

                if ($opening['date_of_birth'] == true) {
                    if ($this->has('date_of_birth') && ($this->date_of_birth == null || $this->date_of_birth == "")) {
                        $rules['date_of_birth'] = 'required';
                    }
                }

                if ($opening['resume'] == true) {
                    if ($this->has('resume') && ($this->resume == null || $this->resume == "")) {
                        $rules['resume'] = 'required';
                    }
                }

                if ($opening['profile_image'] == true) {
                    if ($this->has('image') && ($this->image == null || $this->image == "")) {
                        $rules['image'] = 'required';
                    }
                }

                if ($opening['address'] == true) {
                    if ($this->has('address') && ($this->address == null || $this->address == "")) {
                        $rules['address'] = 'required';
                    }
                }

                if ($opening['cover_letter'] == true) {
                    if ($this->has('cover_letter') && ($this->cover_letter == null || $this->cover_letter == "")) {
                        $rules['cover_letter'] = 'required';
                    }
                }

                if ($this->has('data_question') && (count($this->data_question) > 0)) {
                    $repliedQuestions = $this->data_question;
                    foreach ($repliedQuestions as $question) {
                        if ($question['required'] == 1) {
                            if (!isset($question['reply'])) {
                                $rules[$question['name']] = 'required';
                            }
                        }
                    }
                }
            }
        }

        return $rules;
    }
}
