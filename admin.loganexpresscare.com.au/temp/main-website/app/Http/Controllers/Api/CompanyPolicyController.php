<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CompanyPolicy\IndexRequest;
use App\Http\Requests\Api\CompanyPolicy\StoreRequest;
use App\Http\Requests\Api\CompanyPolicy\UpdateRequest;
use App\Http\Requests\Api\CompanyPolicy\DeleteRequest;
use App\Models\CompanyPolicy;
use App\Models\User;

class CompanyPolicyController extends ApiBaseController
{
    protected $model = CompanyPolicy::class;
    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();

        if ($request->has('location_id') && ($request->location_id != "" || $request->location_id != null)) {
            $locationId = $this->getIdFromHash($request->location_id);
            $query = $query->where('company_policies.location_id', $locationId);
        };

        return  $query;
    }

    public function stored(CompanyPolicy $companyPolicy)
    {
        $allUser = User::select('id')->where('location_id', $companyPolicy->location_id)->where('status', 'active')->get();

        foreach ($allUser as $user) {

            $user->company_policy_id = $companyPolicy->id;

            // Notifying to User
            Notify::send('employee_company_policies_create', $user);
        }



        return $companyPolicy;
    }
}
