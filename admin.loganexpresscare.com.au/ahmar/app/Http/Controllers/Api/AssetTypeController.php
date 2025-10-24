<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\AssetType\IndexRequest;
use App\Http\Requests\Api\AssetType\StoreRequest;
use App\Http\Requests\Api\AssetType\UpdateRequest;
use App\Http\Requests\Api\AssetType\DeleteRequest;
use App\Models\AssetType;

class AssetTypeController extends ApiBaseController
{
	protected $model = AssetType::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;
}
