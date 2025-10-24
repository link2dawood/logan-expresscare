<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\Role;
use App\Classes\Common;
use App\Models\User;

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
        $loggedUser = auth('api')->user();
        $convertedId = Hashids::decode($this->route('user'));
        $id = $convertedId[0];

        $rules = [
            'name' => 'required',
            'email'    => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('user_type', 'staff_members')
                        ->orWhere('user_type', 'super_admins');
                })->ignore($id)
            ],
            'status' => 'required',
            'gender' => 'required',
            'employee_number'    => [
                'required',
                'string',
                Rule::unique('users', 'employee_number')->where(function ($query) use ($loggedUser) {
                    return $query->where('user_type', 'staff_members')
                        ->where('company_id', $loggedUser->company_id);
                })->ignore($id)
            ],
        ];

        if ($this->password != '') {
            $rules['password'] = 'required|min:8';
        }

        if ($this->allow_login == 1) {
            $user = User::find($id);
            if ($user->password == "") {
                $rules['password'] = 'required|min:8';
            }
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
