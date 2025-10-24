<?php

namespace App\Http\Controllers\Api;

use App\Classes\Payrolls;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\SalaryComponent\IndexRequest;
use App\Http\Requests\Api\SalaryComponent\StoreRequest;
use App\Http\Requests\Api\SalaryComponent\UpdateRequest;
use App\Http\Requests\Api\SalaryComponent\DeleteRequest;
use App\Models\BasicSalaryDetails;
use App\Models\SalaryComponent;
use App\Models\SalaryGroupComponent;
use App\Models\StaffMember;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;

class SalaryComponentController extends ApiBaseController
{
    protected $model = SalaryComponent::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();
        if ($request->has('activeType') && $request->activeType != "") {
            $activeType = $request->activeType;
            $query = $query->where('salary_components.type', $activeType);
        };

        return  $query;
    }

    public function updated(SalaryComponent $salaryComponent)
    {
        $this->updateUsersSalary($salaryComponent);
    }


    public function destroying(SalaryComponent $salaryComponent)
    {
        $assignSalaryComponentNotDelete = SalaryGroupComponent::where('salary_component_id', $salaryComponent->id)->count();

        if ($assignSalaryComponentNotDelete > 0) {
            throw new ApiException('Salary component assigned to some group is not deletable.');
        }

        return $salaryComponent;
    }


    public static function updateUsersSalary($salaryComponent)
    {
        $salaryComponentId = $salaryComponent->id;

        $userIds = BasicSalaryDetails::where('salary_component_id', $salaryComponentId)->pluck('user_id');
        $users = StaffMember::whereIn('id', $userIds)->with('basicSalaryDetails.salaryComponent')->get();

        foreach ($users as $user) {
            $annualCTC = (float) $user->annual_ctc;
            Payrolls::updateUserSalary($user->id, $annualCTC);
        }
    }

    public function disabledValue($id)
    {
        $salaryComponentId = $this->getIdFromHash($id);

        $exists = DB::table('salary_group_components')
            ->where('salary_component_id', $salaryComponentId)
            ->exists();

        return ApiResponse::make('Status Check Successfully', ['disabled' => $exists]);
    }
}
