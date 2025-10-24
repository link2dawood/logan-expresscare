<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Payer\IndexRequest;
use App\Http\Requests\Api\Payer\StoreRequest;
use App\Http\Requests\Api\Payer\UpdateRequest;
use App\Http\Requests\Api\Payer\DeleteRequest;
use App\Models\Payer;

class PayerController extends ApiBaseController
{
	protected $model = Payer::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;
}
