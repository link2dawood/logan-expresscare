<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Role;
use App\Models\StaffMember;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;

class UserImport implements ToArray, WithHeadingRow
{

    public function array(array $users)
    {
        DB::transaction(function () use ($users) {
            $userType = "staff_members";

            foreach ($users as $user) {
                $requiredFields = ['name', 'email', 'phone', 'allow_login', 'status', 'joining_date'];
                foreach ($requiredFields as $field) {
                    if (!array_key_exists($field, $user)) {
                        throw new ApiException("Field '{$field}' missing from header.");
                    }
                }

                if (!isset($user['name']) || trim($user['name']) === '') {
                    continue;
                }

                $empNum = isset($user['employee_number']) ? trim($user['employee_number']) : null;

                $email = isset($user['email']) ? trim($user['email']) : null;
                $phone = isset($user['phone']) ? trim($user['phone']) : null;
                $name  = isset($user['name']) ? trim($user['name']) : null;

                $exists = StaffMember::withoutGlobalScope('type')
                    ->where('user_type', $userType)
                    ->where(function ($query) use ($empNum, $email, $phone) {
                        if ($empNum) {
                            $query->orWhere('employee_number', $empNum);
                        }
                        if ($email) {
                            $query->orWhere('email', $email);
                        }
                        if ($phone) {
                            $query->orWhere('phone', $phone);
                        }
                    })
                    ->exists();

                if ($exists) {
                    continue;
                }


                $status = (isset($user['status']) && trim($user['status']) != '') ? trim($user['status']) : 'active';
                $joining_date = (isset($user['joining_date']) && trim($user['joining_date']) != '')
                    ? date('Y-m-d', strtotime($user['joining_date']))
                    : null;
                $gender = (isset($user['gender']) && trim($user['gender']) != '') ? trim($user['gender']) : 'female';
                $allowLogin = isset($user['allow_login']) ? filter_var($user['allow_login'], FILTER_VALIDATE_BOOLEAN) : false;

                $password = null;

                if ($allowLogin) {
                    if (isset($user['password']) && trim($user['password']) !== '') {
                        $password = trim($user['password']);
                    } else {
                        $password = '12345678';
                    }
                }


                $userModel = new StaffMember();
                $userModel->user_type = $userType;
                $userModel->name = $name;
                $userModel->email = $email;
                $userModel->phone = $phone;
                $userModel->status = $status;
                $userModel->password = $password;
                $userModel->gender = $gender;
                $userModel->allow_login = $allowLogin;
                $userModel->joining_date = $joining_date;
                $employeeNumber = (isset($user['employee_number']) && trim($user['employee_number']) != '')
                    ? trim($user['employee_number'])
                    : null;
                $userModel->save();

                if ($employeeNumber) {
                    $userModel->employee_number = $employeeNumber;
                    $userModel->save();
                } else {
                    $company = Company::find($userModel->company_id);
                    $currentNumber = (int) $company->employee_id_start;
                    $generatedEmployeeNumber = $company->employee_id_prefix . '-' . str_pad($currentNumber, $company->employee_id_digits, '0', STR_PAD_LEFT);
                    $userModel->employee_number = $generatedEmployeeNumber;
                    $userModel->save();

                    $company->employee_id_start = $currentNumber + 1;
                    $company->save();
                }
            }
        });
    }
}
