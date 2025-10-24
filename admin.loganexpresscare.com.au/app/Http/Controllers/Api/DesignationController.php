<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Designation\IndexRequest;
use App\Http\Requests\Api\Designation\StoreRequest;
use App\Http\Requests\Api\Designation\UpdateRequest;
use App\Http\Requests\Api\Designation\DeleteRequest;
use App\Models\Designation;

class DesignationController extends ApiBaseController
{
    protected $model = Designation::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function storing($designations)
    {
        $loggedUser = user();

        $designations->created_by = $loggedUser->id;

        return $designations;
    }
}
