<?php

namespace App\Traits;

use App\Classes\Common;
use App\Classes\Notify;
use App\Classes\Payrolls;
use App\Http\Requests\Api\User\ImportRequest;
use App\Http\Requests\Api\User\SalaryUpdateRequest;
use App\Imports\UserImport;
use App\Models\Company;
use App\Models\Feedback;
use App\Models\FeedbackUser;
use App\Models\Role;
use App\Models\UserDocument;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

trait UserTraits
{
    public $userType = "";

    public function modifyIndex($query)
    {
        $request = request();
        $user    = user();

        // User don't have any role
        if (! $user->role || ! $user->is_manager) {
            throw new ApiException("Don't have valid permission");
        }

        // If user is not admin then
        // Users lists will be based on his visibility
        // don't show any user if deperatment, location or report_to is null assigned to user
        if ($user->role->name != 'admin') {
            if ($user->visibility == 'department') {
                $query = $query->where('users.department_id', $user->department_id);
            } else if ($user->visibility == 'location') {
                $query = $query->where('users.location_id', $user->location_id);
            } else if ($user->visibility == 'manager') {
                $query = $query->where('users.report_to', $user->id);
            }
        }

        // User Status Filter
        if ($request->has('status') && $request->status != "all") {
            $userStatus = $request->status;
            $query      = $query->where('users.status', $userStatus);
        }

        // User Shift Filter
        if ($request->has('shift') && $request->shift != '') {
            $shiftId = $this->getIdFromHash($request->shift);
            $query   = $query->where('users.shift_id', $shiftId);
        }

        // User Location Filter
        if ($request->has('location') && $request->location != '') {
            $locationId = $this->getIdFromHash($request->location);
            $query      = $query->where('users.location_id', $locationId);
        }

        // User Department Filter
        if ($request->has('department') && $request->department != '') {
            $departmentId = $this->getIdFromHash($request->department);
            $query        = $query->where('users.department_id', $departmentId);
        }

        // User Designation Filter
        if ($request->has('designation') && $request->designation != '') {
            $designationId = $this->getIdFromHash($request->designation);
            $query         = $query->where('users.designation_id', $designationId);
        }
        $query = $query->where('users.user_type', $this->userType);

        if (!$request->has('fields')) {
            $query = $query->with(['location:id,name', 'designation:id,name', 'department:id,name', 'shift:id,name,clock_in_time,clock_out_time,early_clock_in_time,allow_clock_out_till']);
        }

        return $query;
    }

    public function storing($user)
    {

        $loggedUser = user();
        $request    = request();

        if ($user->user_type != $this->userType) {
            throw new ApiException("Don't have valid permission");
        }

        if ($request->has('annual_ctc') && $request->annual_ctc != '' && $request->annual_ctc != 0) {
            if ($user->user_type == 'staff_members' && $loggedUser->ability('admin', 'salary_settings')) {
                $salaryGroupId = $request->has('salary_group_id') && $request->salary_group_id ? $this->getIdFromHash($request->salary_group_id) : null;
                $user->salary_group_id = $salaryGroupId;
            } else {
                throw new ApiException("Don't have valid permission for Salary Setting");
            }
        }

        if ($loggedUser->ability('admin', 'assign_role')) {
            if ($user->user_type == 'staff_members') {
                $user->role_id = $loggedUser->ability('admin', 'assign_role') && $request->has('role_id') && $request->role_id ? $this->getIdFromHash($request->role_id) : null;
            }

            $user->is_manager = $request->has('is_manager') && ($request->is_manager == 0 || $request->is_manager == 1) ? $request->is_manager : 0;
            $user->visibility = $request->has('visibility') && $request->is_manager == 1 && $request->visibility != '' ? $request->visibility : 'individual';
        } else {
            $user->is_manager = 0;
            $user->visibility = 'individual';
            $user->role_id    = null;
        }

        return $user;
    }

    public function stored($user)
    {
        $request = request();
        $this->saveAndUpdateRole($user);
        // Update all Assign Survey for newly created user
        $this->assignSurveys($user);

        // Updating Company Total Users
        Common::calculateTotalUsers($user->company_id, true);
        $this->increaseEmployeeId($user);
        $this->storeUserDocuments($user);

        // salary details update function
        Payrolls::updateEmployeeSalary($user->id, $user->calculation_type, $user->basic_salary, $user->monthly_amount, $user->annual_amount, $user->annual_ctc, $user->ctc_value, $user->net_salary, $user->special_allowances, $request->salary_components, $request->salary_group_id);

        // Notifying to User
        $user->user_password = $request->has('password') && $request->password ? $request->password : '';
        Notify::send('employee_welcome_mail', $user);
    }

    public function assignSurveys($user)
    {
        $today = Carbon::now();

        $allSurveys = Feedback::where('visible_to', 1)
            ->whereDate('feedbacks.last_date', '>', $today->format('Y-m-d h:i:s'))
            ->get();
        foreach ($allSurveys as $allSurvey) {
            $feedbackUser = FeedbackUser::where('feedback_id', $allSurvey->id)->where('user_id', $user->id)->first();
            if (! $feedbackUser) {
                $feedbackUser = new FeedbackUser();
            }

            $feedbackUser->feedback_id = $allSurvey->id;
            $feedbackUser->user_id     = $user->id;
            $feedbackUser->save();
        }
    }

    public function increaseEmployeeId($user)
    {

        $company = Company::find($user->company_id);
        $userEmployeeNumber = substr($user->employee_number, 4, 4);

        $userEmployeeNumber = $userEmployeeNumber + 1;

        $increasedNumber = str_pad($userEmployeeNumber, $company->employee_id_digits, '0', STR_PAD_LEFT);
        $company->employee_id_start = $increasedNumber;

        $company->save();
    }

    public function updating($user)
    {
        $loggedUser = user();
        $request    = request();

        // Can not change role because only one
        // Admin exists for whole app
        if ($user->user_type == "staff_members") {
            $adminRoleUserCount = Role::join('role_user', 'roles.id', '=', 'role_user.role_id')
                ->where('roles.name', '=', 'admin')
                ->count('role_user.user_id');

            if ($adminRoleUserCount <= 1 && $loggedUser->id == $user->id && $user->isDirty('role_id')) {
                throw new ApiException("Can not change role because you are only admin of app");
            }
        }

        if ($user->user_type != $this->userType) {
            throw new ApiException("Don't have valid permission");
        }

        // Demo mode admin cannot be edit
        // email, password, status, role_id
        if (env('APP_ENV') == 'production' && ($user->isDirty('password') || $user->isDirty('email') || $user->isDirty('status') || $user->isDirty('role_id'))) {
            if ($user->user_type == 'staff_members' && ($user->getOriginal('email') == 'admin@example.com' || $user->getOriginal('email') == 'stockmanager@example.com' || $user->getOriginal('email') == 'salesman@example.com')) {
                throw new ApiException('Not Allowed In Demo Mode');
            }
        }

        if ($loggedUser->ability('admin', 'assign_role')) {
            if ($user->user_type == 'staff_members') {
                $user->role_id = $loggedUser->ability('admin', 'assign_role') && $request->has('role_id') && $request->role_id ? $this->getIdFromHash($request->role_id) : $user->role_id;
            }

            $user->is_manager = $request->has('is_manager') && ($request->is_manager == 0 || $request->is_manager == 1) ? $request->is_manager : $user->is_manager;
            $user->visibility = $request->has('visibility') && $request->is_manager == 1 && $request->visibility != '' ? $request->visibility : $user->visibility;
        } else {
            $user->is_manager = $user->is_manager;
            $user->visibility = $user->visibility;
            $user->role_id    = $user->role_id;
        }

        if ($request->has('annual_ctc') && $request->annual_ctc != '') {
            if ($user->user_type == 'staff_members' && $loggedUser->ability('admin', 'salary_settings')) {
                $salaryGroupId = $request->has('salary_group_id') && $request->salary_group_id ? $this->getIdFromHash($request->salary_group_id) : null;
                $user->salary_group_id = $salaryGroupId;
                // salary details update function
                Payrolls::updateEmployeeSalary($user->id, $user->calculation_type, $user->basic_salary, $user->monthly_amount, $user->annual_amount, $user->annual_ctc, $user->ctc_value, $user->net_salary, $user->special_allowances, $request->salary_components, $salaryGroupId);
            } else {
                throw new ApiException("Don't have valid permission for Salary Setting");
            }
        }

        return $user;
    }

    public function updated($user)
    {
        $this->saveAndUpdateRole($user);
        $this->storeUserDocuments($user);
    }

    public function saveAndUpdateRole($user)
    {
        $request = request();

        // Only For Staff Members
        if ($user->user_type == 'staff_members') {

            DB::table('role_user')->where('user_id', $user->id)->delete();
            $user->attachRole($user->role_id);
        }

        return $user;
    }

    public function destroying($user)
    {
        if ($user->user_type != $this->userType) {
            throw new ApiException("Don't have valid permission");
        }

        $loggedUser        = user();
        $loggedUserCompany = company();

        if ($loggedUserCompany->admin_id == $user->id) {
            throw new ApiException('Can not delete company root admin');
        }

        if (env('APP_ENV') == 'production' && $user->user_type == 'staff_members' && ($user->getOriginal('email') == 'admin@example.com' || $user->getOriginal('email') == 'stockmanager@example.com' || $user->getOriginal('email') == 'salesman@example.com')) {
            throw new ApiException('Not Allowed In Demo Mode');
        }

        // If application have only one admin
        // Then staff member cannot be deleted
        if ($user->user_type == "staff_members") {
            if ($user->role_id) {
                $userRole = Role::find($user->role_id);

                if ($userRole && $userRole->name == 'admin') {
                    $adminRoleUserCount = Role::join('role_user', 'roles.id', '=', 'role_user.role_id')
                        ->where('roles.name', '=', 'admin')
                        ->count('role_user.user_id');

                    if ($adminRoleUserCount <= 1) {
                        throw new ApiException('You are the only admin of app. So not able to delete.');
                    }
                }
            }
        }

        if ($loggedUser->id == $user->id) {
            throw new ApiException('Can not delete yourself.');
        }

        return $user;
    }

    public function destroyed($user)
    {
        // Updating Company Total Users
        Common::calculateTotalUsers($user->company_id, true);
    }

    public function import(ImportRequest $request)
    {
        if ($request->hasFile('file')) {
            Excel::import(new UserImport($this->userType), request()->file('file'));
        }

        return ApiResponse::make('Imported Successfully', []);
    }

    public function updateBasicSalary(SalaryUpdateRequest $salaryUpdateRequest)
    {
        $request    = request();
        $loggedUser = user();

        if ($request->has('annual_ctc') && $request->annual_ctc != '') {
            if ($loggedUser->ability('admin', 'salary_settings')) {
                $salaryGroupId = $this->getIdFromHash($salaryUpdateRequest->salary_group_id);
                $id = $this->getIdFromHash($salaryUpdateRequest->xid);
                $salaryComponents = $salaryUpdateRequest->salary_components;
                Payrolls::updateEmployeeSalary($id, $salaryUpdateRequest->calculation_type, $salaryUpdateRequest->basic_salary, $salaryUpdateRequest->monthly_amount, $salaryUpdateRequest->annual_amount, $salaryUpdateRequest->annual_ctc, $salaryUpdateRequest->ctc_value, $salaryUpdateRequest->net_salary, $salaryUpdateRequest->special_allowances, $salaryComponents, $salaryGroupId);
            } else {
                throw new ApiException("Don't have valid permission for Salary Setting");
            }
        }
    }

    public function storeUserDocuments($user)
    {
        $request = request();
        if (isset($request['employee_fields_values']) && is_array($request['employee_fields_values'])) {
            foreach ($request['employee_fields_values'] as $employeeFieldData) {
                if (! isset($employeeFieldData['field_type_id'])) {
                    continue;
                }

                $id = $this->getIdFromHash($employeeFieldData['field_type_id']);
                $userDocument = UserDocument::where('field_type_id', $id)->where('user_id', $user->id)->first();

                if (isset($employeeFieldData['value'])) {
                    if (! $userDocument) {
                        $userDocument = new UserDocument();
                    }
                    $userDocument->user_id       = $user->id;
                    $userDocument->field_type_id = $id;
                    $userDocument->values        = $employeeFieldData['value'];
                    $userDocument->save();
                } else {
                    if ($userDocument) {
                        $userDocument->delete();
                    }
                }
            }
        }
    }
}
