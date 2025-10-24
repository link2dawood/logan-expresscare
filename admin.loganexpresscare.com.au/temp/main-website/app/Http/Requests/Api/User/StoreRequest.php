<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Mockery\Undefined;
use App\Models\Role;
use App\Classes\Common;

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
        $loggedUser = auth('api')->user();

        $rules = [
            'employee_number'    => [
                'required',
                'string',
                Rule::unique('users', 'employee_number')->where(function ($query) use ($loggedUser) {
                    return $query->where('user_type', 'staff_members')
                        ->where('company_id', $loggedUser->company_id);
                })
            ],
            'name' => 'required',

            'email'    => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('user_type', 'staff_members')
                        ->orWhere('user_type', 'super_admins');
                })
            ],
            'status' => 'required',
            'gender' => 'required',
            'joining_date' => 'required',
        ];

        if ($this->allow_login == 1) {
            $rules['password'] = 'required|min:8';
        }

        if ($this->is_manager == 1) {
            $rules['role_id'] = 'required';
        }

        if ($this->role_id != null) {
            $roleId = Common::getIdFromHash($this->role_id);
            $roleName = Role::where('id', $roleId)
                ->select('name')->first();

            if ($roleName['name'] != 'admin') {

                $rules['visibility'] = 'required';
            };
        };

        return $rules;
    }
}
