<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\User\IndexRequest;
use App\Http\Requests\Api\User\StoreRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Requests\Api\User\DeleteRequest;
use App\Http\Requests\Api\User\SalaryUpdateRequest;
use App\Models\StaffMember;
use Examyou\RestAPI\ApiResponse;
use App\Traits\UserTraits;

class UsersController extends ApiBaseController
{
    use UserTraits;

    protected $model = StaffMember::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $salaryUpdateRequest = SalaryUpdateRequest::class;

    public function __construct()
    {
        parent::__construct();

        $this->userType = "staff_members";
    }

    public function toGetEmployeeProfile()
    {
        $request = request();
        $data = [];

        $id = $this->getIdFromHash($request->id);

        $data[] = StaffMember::where('id', '=', $id)->with('location', 'shift', 'department', 'designation', 'reporter', 'appreciation', 'appreciation.award', 'leaves.leaveType', 'assets.location', 'assets_type')->first();
        return ApiResponse::make('Success', $data);
    }
}
