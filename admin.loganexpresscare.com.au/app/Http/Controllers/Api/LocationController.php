<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Location\IndexRequest;
use App\Http\Requests\Api\Location\StoreRequest;
use App\Http\Requests\Api\Location\UpdateRequest;
use App\Http\Requests\Api\Location\DeleteRequest;
use App\Models\Location;

class LocationController extends ApiBaseController
{
	protected $model = Location::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;
}
