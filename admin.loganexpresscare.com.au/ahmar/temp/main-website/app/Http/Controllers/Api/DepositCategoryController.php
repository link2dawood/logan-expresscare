<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\DepositCategory\IndexRequest;
use App\Http\Requests\Api\DepositCategory\StoreRequest;
use App\Http\Requests\Api\DepositCategory\UpdateRequest;
use App\Http\Requests\Api\DepositCategory\DeleteRequest;
use App\Models\DepositCategory;

class DepositCategoryController extends ApiBaseController
{
	protected $model = DepositCategory::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;
}
