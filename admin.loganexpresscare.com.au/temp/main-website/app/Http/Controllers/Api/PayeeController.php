<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Payee\IndexRequest;
use App\Http\Requests\Api\Payee\StoreRequest;
use App\Http\Requests\Api\Payee\UpdateRequest;
use App\Http\Requests\Api\Payee\DeleteRequest;
use App\Models\Payee;

class PayeeController extends ApiBaseController
{
	protected $model = Payee::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;
}
