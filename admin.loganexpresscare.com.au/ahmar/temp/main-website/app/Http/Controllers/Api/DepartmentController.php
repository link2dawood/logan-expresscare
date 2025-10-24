<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Department\IndexRequest;
use App\Http\Requests\Api\Department\StoreRequest;
use App\Http\Requests\Api\Department\UpdateRequest;
use App\Http\Requests\Api\Department\DeleteRequest;
use App\Models\Department;

class DepartmentController extends ApiBaseController
{
    protected $model = Department::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function storing($departments)
    {
        $loggedUser = user();

        $departments->created_by = $loggedUser->id;

        return $departments;
    }

}