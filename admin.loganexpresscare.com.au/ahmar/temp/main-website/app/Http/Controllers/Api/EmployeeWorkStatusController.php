<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\EmployeeWorkStatus\IndexRequest;
use App\Http\Requests\Api\EmployeeWorkStatus\StoreRequest;
use App\Http\Requests\Api\EmployeeWorkStatus\UpdateRequest;
use App\Http\Requests\Api\EmployeeWorkStatus\DeleteRequest;
use App\Models\EmployeeWorkStatus;

class EmployeeWorkStatusController extends ApiBaseController
{
    protected $model = EmployeeWorkStatus::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
}
